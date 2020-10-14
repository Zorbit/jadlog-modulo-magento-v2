<?php
namespace Jadlog\Embarcador\Integracao\MyPudo;

use \Jadlog\Embarcador\Helper\Data as HelperData;

class PudoServiceDpd {

  private $helperData;
  private $mypudo_url;
  private $carrier;
  private $key;
  private $zipcode;
  private $city;
  private $countrycode;
  private $requestID;
  private $address;
  private $date_from;
  private $max_pudo_number;
  private $max_distance_search;
  private $weight;
  private $category;
  private $holiday_tolerant;

  public function __construct($helper, $zipcode, $city) {
    $this->helperData = $helper;
    $this->mypudo_url = $this->helperData->getMyPudoURL();
    $this->carrier = $this->helperData->getMyPudoShipperId();
    $this->key = $this->helperData->getMyPudoKey();
    $this->zipcode = $zipcode;
    $this->city = $city;
    $this->countrycode = "BRA";
    $this->requestID = $this->helperData->getRemetenteCNPJ() + rand(1,200);
    $this->address = "";
    $this->date_from = "";
    $this->max_pudo_number = "";
    $this->max_distance_search = "";
    $this->weight = "";
    $this->category = "";
    $this->holiday_tolerant = "";
  }

  public function getData() {
    return $this->getRealData();
  }

  private function getDataFromService($data) {
    $data_string = http_build_query($data);
    $curl = curl_init($this->mypudo_url);
    curl_setopt_array($curl, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data_string,
      CURLOPT_HTTPHEADER => array(
        "Connection: keep-alive",
        "Content-Length: " . strlen($data_string),
        "Content-Type: application/x-www-form-urlencoded"
      )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    //log
    $message = [
      'data' => print_r($data, true),
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
      $xml = (array)simplexml_load_string($response);
      $json = json_encode($xml);
      $array = json_decode($json,TRUE);
    }
    $result = $array;

    return $result;
  }

  private function getRealData() {
    $data = [
      "carrier" => $this->carrier,
      "key" => $this->key,
      "zipcode" => $this->zipcode,
      "city" => $this->city,
      "countrycode" => $this->countrycode,
      "requestID" => $this->requestID,
      "address" => $this->address,
      "date_from" => $this->date_from,
      "max_pudo_number" => $this->max_pudo_number,
      "max_distance_search" => $this->max_distance_search,
      "weight" => $this->weight,
      "category" => $this->category,
      "holiday_tolerant" => $this->holiday_tolerant
    ];
    return $this->getDataFromService($data);
  }

}

?>