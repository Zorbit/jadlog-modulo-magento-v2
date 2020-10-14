<?php
namespace Jadlog\Embarcador\Model\ResourceModel;

class Modalidade extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context) {
    parent::__construct($context);
  }

  protected function _construct() {
    $this->_init('jadlog_modalidades', 'id');
  }

}
?>
