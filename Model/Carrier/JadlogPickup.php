<?php
namespace Jadlog\Embarcador\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Jadlog\Embarcador\Integracao\Frete\Valor as FreteValor;
use Magento\Checkout\Model\Session as CheckoutSession;

class JadlogPickup extends \Magento\Shipping\Model\Carrier\AbstractCarrier
implements \Magento\Shipping\Model\Carrier\CarrierInterface {

  const CODE = 'jadlog_pickup';
  protected $_code = JadlogPickup::CODE;

  public static function carrierMethod() {
    return JadlogPickup::CODE . "_" . JadlogPickup::CODE;
  }

  protected $_rateResultFactory;
  protected $_rateMethodFactory;
  protected $_logger;
  protected $_helperData;
  protected $_checkoutSession;

  public function __construct(
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
    \Jadlog\Embarcador\Helper\Data $helperData,
    \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
    CheckoutSession $checkoutSession,
    array $data = []
  ) {
    $this->_rateResultFactory = $rateResultFactory;
    $this->_rateMethodFactory = $rateMethodFactory;
    $this->_logger = $logger;
    $this->_helperData = $helperData;
    $this->_checkoutSession = $checkoutSession;
    parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
  }

  public function getAllowedMethods() {
    return [$this->_code => $this->getConfigData('name')];
  }

  private function getShippingPrice($cep, $peso, $valor_declarado, $modalidade) {
    $f = new FreteValor($this->_helperData, $cep, $peso, $valor_declarado, $modalidade);
    $r = $f->getData();
    //log
    $message = [
      'file' => __FILE__,
      'line' => __LINE__,
      '$r' => print_r($r, true)
    ];
    $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log
    $shippingPrice = $r[$cep]['frete'][0]['vltotal'];

    return $shippingPrice;
  }

  public function collectRates(RateRequest $request) {
    // saved in var/log/shipping.log
    $this->_logger->debug(
      get_class($this) . '->' . __FUNCTION__, [
        $request->getDestPostcode(),
        strlen($request->getDestPostcode()),
        $this->_helperData->getPickupHabilitado(),
        $this->_checkoutSession->getJadlogPudoData()
      ]
    );

    if (!$this->_helperData->getPickupHabilitado() || (strlen($request->getDestPostcode()) < 8)) {
      return false;
    }

    //verifica se há pudo escolhido
    $pudo = json_decode($this->_checkoutSession->getJadlogPudoData());
    $cep_destino = $request->getDestPostcode();
    if($pudo) {
      $cep_destino = $pudo->Zipcode;
    }
    $cep = $this->_helperData->getCep($cep_destino);

    if (empty($cep["error"])) {
      /*
      $this->_logger->debug(
        get_class($this) . '->' . __FUNCTION__, [
          "Utilizando o cep: {$cep['cep']}"
        ]
      );
      */
      // @var \Magento\Shipping\Model\Rate\Result $result
      $result = $this->_rateResultFactory->create();

      // @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method
      $method = $this->_rateMethodFactory->create();

      $method->setCarrier($this->_code);
      $method->setCarrierTitle($this->getConfigData('title'));
      $method->setMethod($this->_code);
      $method->setMethodTitle($this->getConfigData('name'));

      $peso = $request->getPackageWeight();
      $valor_declarado = $request->getPackageValue();

      //grava os novos parametros e chama o servico em seguida
      $this->_checkoutSession->setJadlogWeight($peso);
      $this->_checkoutSession->setJadlogValue($valor_declarado);
      $amount = $this->getShippingPrice(
        $cep['cep'],
        $peso,
        $valor_declarado,
        $this->_helperData->getCodigoPickup()
      );

      $method->setPrice($amount);
      $method->setCost($amount);
      $result->append($method);

      return $result;
    } else {
      $message = "Digite o CEP corretamente. CEP informado: " . $cep_destino;
      $error = $this->_rateErrorFactory->create();
      $error->setCarrier($this->_code)
        ->setCarrierTitle($this->getConfigData('title'))
        ->setErrorMessage(__($cep["error"]));
      return $error;
    }
  }

}
