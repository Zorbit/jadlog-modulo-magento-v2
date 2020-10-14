<?php
namespace Jadlog\Embarcador\Model;

use Magento\Framework\DataObject;
use Jadlog\Embarcador\Api\Data\PudoInterface;

class Pudo extends DataObject implements PudoInterface {
  /**
  * @return string
  */
  public function getName() {
    return (string)$this->_getData('name');
  }

  /**
  * @return string
  */
  public function getPudoId() {
    return (string)$this->_getData('pudo_id');
  }

  /**
  * @return string
  */
  public function getLocation() {
    return (string)$this->_getData('location');
  }

  /**
  * @return string
  */
  public function getOpeningHours() {
    return (string)$this->_getData('opening_hours');
  }

  /**
  * @return string
  */
  public function getLatitude() {
    return (string)$this->_getData('latitude');
  }

  /**
  * @return string
  */
  public function getLongitude() {
    return (string)$this->_getData('longitude');
  }

  /**
  * @return string
  */
  public function getDistance() {
    return (string)$this->_getData('distance');
  }

  /**
  * @return string
  */
  public function getCity() {
    return (string)$this->_getData('city');
  }

  /**
  * @return string
  */
  public function getZipcode() {
    return (string)$this->_getData('zipcode');
  }

  /**
  * @return float
  */
  public function getRate() {
    return (float)$this->_getData('rate');
  }

  /**
  * @return string
  */
  public function getId() {
    return (string)$this->_getData('id');
  }
}
?>