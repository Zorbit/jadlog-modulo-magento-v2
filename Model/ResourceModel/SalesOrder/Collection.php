<?php
namespace Jadlog\Embarcador\Model\ResourceModel\SalesOrder;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
  protected $_idFieldName = 'id';
  protected $_eventPrefix = 'jadlog_sales_order_collection';
  protected $_eventObject = 'sales_order_collection';

  protected function _construct() {
    $this->_init('Jadlog\Embarcador\Model\SalesOrder', 'Jadlog\Embarcador\Model\ResourceModel\SalesOrder');
    $this->_map['fields']['shipping_description'] = 'sales_order.shipping_description';
  }
}
