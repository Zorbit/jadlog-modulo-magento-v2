<?php
namespace Jadlog\Embarcador\Model;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class SalesOrder extends AbstractModel implements IdentityInterface {

  const CACHE_TAG = 'jadlog_sales_order';
  protected $_cacheTag = 'jadlog_sales_order';
  protected $_eventPrefix = 'jadlog_sales_order';

  protected function _construct() {
    $this->_init(\Jadlog\Embarcador\Model\ResourceModel\SalesOrder::class);
  }

  public function getIdentities() {
    return [self::CACHE_TAG . '_' . $this->getId()];
  }

  //public function getDefaultValues() {
  //  $values = [];
  //  return $values;
  //}
}
?>
