<?php
namespace Jadlog\Embarcador\Model\ResourceModel;

class SalesOrder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
    parent::__construct($context);
  }

  protected function _construct() {
    $this->_init('jadlog_sales_order', 'id');
  }

}
?>
