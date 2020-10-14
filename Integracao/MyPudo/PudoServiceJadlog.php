<?php
namespace Jadlog\Embarcador\Integracao\MyPudo;

use \Jadlog\Embarcador\Helper\Data as HelperData;

class PudoServiceJadlog {

  private $helperData;
  private $mypudo_url;
  private $zipcode;
  private $city;
  private $token;

  public function __construct($helper, $zipcode, $city) {
    $this->helperData = $helper;
    $this->mypudo_url = $this->helperData->getMyPudoURL();
    $this->zipcode = $zipcode;
    $this->city = $city;
    $this->token = $this->helperData->getToken();
  }

  public function getData() {
    return $this->getRealData();
  }

  private function getDataFromService() {
    //$data_string = http_build_query($data);
    $curl = curl_init($this->mypudo_url . "/" . $this->zipcode);
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_HTTPHEADER => array(
        "Connection: keep-alive",
        //"Content-Length: " . strlen($data_string),
        //"Content-Type: application/xml",
        "Content-Type:  application/json",
        "Authorization: " . $this->token
      )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    //log
    $message = [
      'cep' => print_r($this->zipcode, true),
      'err' => print_r($err, true),
      'response' => print_r($response, true),
      '$this->mypudo_url' => $this->mypudo_url
      //'result' => print_r($result, true)
    ];
    $this->helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    if ($err) {
      $array = false;
      //echo "cURL Error #:" . $err;
    } else {
      $array = json_decode($response,TRUE);
    }
    $result = $array;

    return $result;
  }

  private function getRealData() {
    return $this->getDataFromService();
  }

}

?>