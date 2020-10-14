<?php
namespace Jadlog\Embarcador\Observer\Model;
use Magento\Framework\Event\ObserverInterface;
use Jadlog\Embarcador\Model\QuoteFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

class Quote implements ObserverInterface {

  protected $_helperData;
  protected $_quoteFactory;
  protected $_checkoutSession;

  public function __construct(
    \Jadlog\Embarcador\Helper\Data $helperData,
    QuoteFactory $quoteFactory,
    CheckoutSession $checkoutSession
  ) {
    //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $this->_helperData = $helperData;
    $this->_quoteFactory = $quoteFactory;
    $this->_checkoutSession = $checkoutSession;
  }

  public function execute(\Magento\Framework\Event\Observer $observer) {
    $order = $observer->getOrder();
    $method = $order->getShippingMethod();

    $message = [
      '$method' => $method,
      '$this->_helperData->isEntregaJadlog($method)' => $this->_helperData->isEntregaJadlog($method)
    ];
    $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);


    if ($this->_helperData->isEntregaJadlog($method)) {
      $quote = $observer->getQuote();

      $pudo = '';
      $pudo_id = '';
      if ($this->_helperData->isEntregaJadlogPickup($method)) {
        $pudo_data = json_decode($this->_checkoutSession->getJadlogPudoData());
        if($pudo_data) {
          $pudo_id = $pudo_data->PudoId;
          $pudo = "{$pudo_data->Name} | {$pudo_data->Location}";
        }
      }

      $message = [
        '$method' => $method,
        '$order->getId()' => $order->getId(),
        '$quote->getId()' => $quote->getId(),
        "pudo" => $pudo
      ];
      $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);

      if ($quote->getId()) {
        $jadlog_quote = $this->_quoteFactory->create();
        $jadlog_quote->load($quote->getId(), 'quote_id');

        $jadlog_quote->
          setQuoteId($quote->getId())->
          setPickup($pudo)->
          setPudoId($pudo_id)->
          save();
        $message = [
          'salvar jadlog_quote' => 'sim',
          '$quote->getId()' => $quote->getId(),
          '$jadlog_quote->getQuoteId()' => $jadlog_quote->getQuoteId(),
          '$jadlog_quote->getPickup()' => $jadlog_quote->getPickup()
        ];
        $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
      }
    }
    return $this;
  }
}
?>
