<?php
namespace Jadlog\Embarcador\Observer\Model;
use Magento\Framework\Event\ObserverInterface;
use Jadlog\Embarcador\Model\SalesOrderFactory;
use Jadlog\Embarcador\Model\QuoteFactory;

class Order implements ObserverInterface {

  protected $_helperData;
  protected $_orderFactory;
  protected $_quoteFactory;

  public function __construct(
    \Jadlog\Embarcador\Helper\Data $helperData,
    SalesOrderFactory $orderFactory,
    QuoteFactory $quoteFactory
  ) {
    //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $this->_helperData = $helperData;
    $this->_orderFactory = $orderFactory;
    $this->_quoteFactory = $quoteFactory;
  }

  public function execute(\Magento\Framework\Event\Observer $observer) {
    $order = $observer->getOrder();
    $customerId = $order->getCustomerId();

    $jadlog_quote = $this->_quoteFactory->create();
    $jadlog_quote->load($order->getQuoteId(), 'quote_id');

    $pudo = $jadlog_quote->getPickup();
    $pudo_id = $jadlog_quote->getPudoId();

    //log
    $message = [
      'file' => __FILE__,
      'line' => __LINE__,
      '$this->_helperData->isEntregaJadlog($order->getShippingMethod())' => $this->_helperData->isEntregaJadlog($order->getShippingMethod()),
      '$order->getShippingMethod()' => $order->getShippingMethod(),
      '$order->getId()' => $order->getId(),
      '$jadlog_quote->getId()' => $jadlog_quote->getId(),
      "customerId" => $customerId,
      "pudo" => $pudo
    ];
    $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    if ($order->getId() && $this->_helperData->isEntregaJadlog($order->getShippingMethod())) {
      $jadlog_sales_order = $this->_orderFactory->create();
      $jadlog_sales_order->load($order->getId(), 'order_id');

      //salvar jadlog sales order
      $jadlog_sales_order->
        setOrderId($order->getId())->
        setPickup($pudo)->
        setPudoId($pudo_id)->
        save();
      $message = [
        'salvar jadlog_sales_order' => 'sim',
        '$order->getId()' => $order->getId(),
        '$jadlog_sales_order->getOrderId()' => $jadlog_sales_order->getOrderId(),
        '$jadlog_sales_order->getPickup()' => $jadlog_sales_order->getPickup(),
        '$jadlog_sales_order->getPudoId()' => $jadlog_sales_order->getPudoId()
      ];
      $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    }

    return $this;
  }
}
?>
