<?php
namespace Jadlog\Embarcador\Model;

class Modalidade extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
  const CACHE_TAG = 'jadlog_modalidades';
  protected $_cacheTag = 'jadlog_modalidades';
  protected $_eventPrefix = 'jadlog_modalidades';

  protected function _construct() {
    $this->_init('Jadlog\Embarcador\Model\ResourceModel\Modalidade');
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
