<?php
namespace Jadlog\Embarcador\Model;

class Quote extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
  const CACHE_TAG = 'jadlog_quote';
  protected $_cacheTag = 'jadlog_quote';
  protected $_eventPrefix = 'jadlog_quote';

  protected function _construct() {
    $this->_init('Jadlog\Embarcador\Model\ResourceModel\Quote');
  }

  public function getIdentities() {
    return [self::CACHE_TAG . '_' . $this->getId()];
  }

  public function getDefaultValues() {
    $values = [];
    return $values;
  }
}
?>
