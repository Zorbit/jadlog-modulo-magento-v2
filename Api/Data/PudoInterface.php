<?php
namespace Jadlog\Embarcador\Api\Data;

/**
 * Pudo Interface
 */
interface PudoInterface {
  /**
  * @return string
  */
  public function getName();

  /**
  * @return string
  */
  public function getPudoId();

  /**
  * @return string
  */
  public function getLocation();

  /**
  * @return string
  */
  public function getOpeningHours();

  /**
  * @return string
  */
  public function getLatitude();

  /**
  * @return string
  */
  public function getLongitude();

  /**
  * @return string
  */
  public function getDistance();

  /**
  * @return string
  */
  public function getCity();

  /**
  * @return string
  */
  public function getZipcode();

  /**
  * @return float
  */
  public function getRate();

  /**
  * @return string
  */
  public function getId();
}
?>