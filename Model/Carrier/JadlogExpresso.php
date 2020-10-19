<?php

namespace Jadlog\Embarcador\Model\Carrier;

use Jadlog\Embarcador\Helper\Data;
use Jadlog\Embarcador\Integracao\Frete\Valor as FreteValor;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class JadlogExpresso extends AbstractCarrier implements CarrierInterface
{
    const CODE = 'jadlog_expresso';
    protected $_code = JadlogExpresso::CODE;

    public static function carrierMethod()
    {
        return JadlogExpresso::CODE . "_" . JadlogExpresso::CODE;
    }

    protected $_rateResultFactory;
    protected $_rateMethodFactory;
    protected $_logger;
    protected $_helperData;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        Data $helperData,
        MethodFactory $rateMethodFactory,
        array $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_logger = $logger;
        $this->_helperData = $helperData;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    private function getShippingPrice($cep, $peso, $valor_declarado, $modalidade)
    {
        $f = new FreteValor($this->_helperData, $cep, $peso, $valor_declarado, $modalidade);
        $r = $f->getData();

        $shippingPrice = isset($r[$cep]['frete'][0]['vltotal']) && $r[$cep]['frete'][0]['vltotal'] != '' ? $r[$cep]['frete'][0]['vltotal'] : null;

        return $shippingPrice;
    }

    public function collectRates(RateRequest $request)
    {
        // saved in var/log/shipping.log
        $this->_logger->debug(
            get_class($this) . '->' . __FUNCTION__, [
                $request->getDestPostcode(),
                strlen($request->getDestPostcode()),
                $this->_helperData->getExpressoHabilitado()
            ]
        );

        if (!$this->_helperData->getExpressoHabilitado() || (strlen($request->getDestPostcode()) < 8)) {
            return false;
        }

        $cep = $this->_helperData->getCep($request->getDestPostcode());

        if (empty($cep["error"])) {
            // @var \Magento\Shipping\Model\Rate\Result $result
            $result = $this->_rateResultFactory->create();

            // @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            $amount = $this->getShippingPrice(
                $cep['cep'],
                $request->getPackageWeight(),
                $request->getPackageValue(),
                $this->_helperData->getCodigoExpresso()
            );

            if ($amount == null) {
                return false;
            }

            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);

            return $result;
        } else {
            $message = "Digite o CEP corretamente. CEP informado: " . $request->getDestPostcode();
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('title'))
                ->setErrorMessage(__($cep["error"]));
            return $error;
        }
    }
}