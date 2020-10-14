<?php
namespace Jadlog\Embarcador\Api;

interface PudoManagementInterface {

  /**
  * Set Pudo for current CheckoutSession
  * @param mixed $pudo
  * @return boolean
  */
  public function setPudo($pudo);

  /**
  * Find pudos for the customer
  *
  * @param string $postcode
  * @param string $city
  * @return \Jadlog\Embarcador\Api\Data\PudoInterface[]
  */
  public function fetchPudos($postcode, $city);
}
?>