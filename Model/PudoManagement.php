<?php
namespace Jadlog\Embarcador\Model;

use Jadlog\Embarcador\Api\PudoManagementInterface;
use Jadlog\Embarcador\Api\Data\PudoInterfaceFactory;
use Jadlog\Embarcador\Integracao\MyPudo\PudoServiceDpd as PudoServiceDpd;
use Jadlog\Embarcador\Integracao\MyPudo\PudoServiceJadlog as PudoServiceJadlog;
use Jadlog\Embarcador\Integracao\Frete\Valor as FreteValor;
use Magento\Checkout\Model\Session as CheckoutSession;

class PudoManagement implements PudoManagementInterface {
  protected $PudoFactory;
  protected $_helperData;
  protected $_checkoutSession;
  protected $_modalidade;
  protected $_serviceType;
  CONST WEEK_DAYS = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
  CONST WEEK_DAYS_ABR = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];

  /**
  * PudoManagement constructor.
  * @param PudoInterfaceFactory $PudoInterfaceFactory
  * @param \Jadlog\Embarcador\Helper\Data $helperData
  */
  public function __construct(
    PudoInterfaceFactory $PudoInterfaceFactory,
    \Jadlog\Embarcador\Helper\Data $helperData,
    CheckoutSession $checkoutSession
  ) {
    $this->_serviceType = 'JADLOG'; //other option is DPD
    //$this->_serviceType = 'DPD'; //other option is JADLOG
    $this->PudoFactory = $PudoInterfaceFactory;
    $this->_helperData = $helperData;
    $this->_checkoutSession = $checkoutSession;
    $this->_modalidade = $this->_helperData->getCodigoPickup();
  }

  /**
  * Set Pudo for current CheckoutSession
  *
  * @param mixed $pudo
  * @return boolean
  */
  public function setPudo($pudo) {
    $this->_checkoutSession->setJadlogPudoData(json_encode($pudo));
    return true;
  }

  /**
  * Get Pudos for the given postcode and city
  *
  * @param string $postcode
  * @param string $city
  * @return \Jadlog\Embarcador\Api\Data\PudoInterface[]
  */
  public function fetchPudos($postcode, $city) {
    if ($this->_serviceType == 'DPD') {
      return $this->fetchPudosDpd($postcode, $city);
    } else {
      return $this->fetchPudosJadlog($postcode, $city);
    }
  }

  /**
  * Get Pudos for the given postcode and city
  *
  * @param string $postcode
  * @param string $city
  * @return \Jadlog\Embarcador\Api\Data\PudoInterface[]
  */
  private function fetchPudosJadlog($postcode, $city) {
    $f = new PudoServiceJadlog(
      $this->_helperData,
      $postcode,
      $city
    );
    $r = $f->getData();

    $weight = $this->_checkoutSession->getJadlogWeight();
    $value = $this->_checkoutSession->getJadlogValue();
    $result = [];
    foreach($r['pudos'] as $pudo_item) {
      if ($pudo_item['ativo'] == 'S') {
        $endereco = $pudo_item['pudoEnderecoList'][0];
        $cep = $this->_helperData->getCep($endereco['cep']);
        if (empty($cep["error"])) {
          $Pudo = $this->PudoFactory->create();

          $Pudo->setName($pudo_item['razao']);
          $Pudo->setPudoId($pudo_item['pudoId']);
          $Pudo->setLocation($this->humanLocationJadlog($endereco));
          $Pudo->setOpeningHours($this->humanOpeningHoursJadlog($pudo_item));
          $Pudo->setLatitude($endereco['latitude']);
          $Pudo->setLongitude($endereco['longitude']);
          //$Pudo->setDistance($pudo_item['distancia']);
          $Pudo->setDistance(1);
          $Pudo->setZipcode($cep['cep']);
          $Pudo->setCity($endereco['cidade']);
          $Pudo->setRate($this->getPudoShippingPrice($Pudo->getZipcode(), $weight, $value));

          $Pudo->setId(json_encode([
            'Name' => $Pudo->getName(),
            'PudoId' => $Pudo->getPudoId(),
            'Location' => $Pudo->getLocation(),
            'OpeningHours' => $Pudo->getOpeningHours(),
            'Latitude' => $Pudo->getLatitude(),
            'Longitude' => $Pudo->getLongitude(),
            'Distance' => $Pudo->getDistance(),
            'Zipcode' => $Pudo->getZipcode(),
            'City' => $Pudo->getCity(),
            'Rate' => $Pudo->getRate()
          ]));

          $result[] = $Pudo;
        }
      }
    }
    return $result;
  }

  /**
  * Get Pudos for the given postcode and city
  *
  * @param string $postcode
  * @param string $city
  * @return \Jadlog\Embarcador\Api\Data\PudoInterface[]
  */
  private function fetchPudosDpd($postcode, $city) {
    $f = new PudoServiceDpd(
      $this->_helperData,
      $postcode,
      $city
    );
    $r = $f->getData();

    $weight = $this->_checkoutSession->getJadlogWeight();
    $value = $this->_checkoutSession->getJadlogValue();
    $result = [];
    foreach($r['PUDO_ITEMS']['PUDO_ITEM'] as $pudo_item) {
      $cep = $this->_helperData->getCep($pudo_item['ZIPCODE']);
      if (empty($cep["error"])) {
        $Pudo = $this->PudoFactory->create();

        $Pudo->setName($pudo_item['NAME']);
        $Pudo->setPudoId($pudo_item['PUDO_ID']);
        $Pudo->setLocation($this->humanLocationDpd($pudo_item));
        $Pudo->setOpeningHours($this->humanOpeningHoursDpd($pudo_item));
        $Pudo->setLatitude($pudo_item['LATITUDE']);
        $Pudo->setLongitude($pudo_item['LONGITUDE']);
        $Pudo->setDistance($pudo_item['DISTANCE']);
        $Pudo->setZipcode($cep['cep']);
        $Pudo->setCity($pudo_item['CITY']);
        $Pudo->setRate($this->getPudoShippingPrice($Pudo->getZipcode(), $weight, $value));

        $Pudo->setId(json_encode([
          'Name' => $Pudo->getName(),
          'PudoId' => $Pudo->getPudoId(),
          'Location' => $Pudo->getLocation(),
          'OpeningHours' => $Pudo->getOpeningHours(),
          'Latitude' => $Pudo->getLatitude(),
          'Longitude' => $Pudo->getLongitude(),
          'Distance' => $Pudo->getDistance(),
          'Zipcode' => $Pudo->getZipcode(),
          'City' => $Pudo->getCity(),
          'Rate' => $Pudo->getRate()
        ]));

        $result[] = $Pudo;
      }
    }
    return $result;
  }

  private function getPudoShippingPrice($cep, $peso, $valor_declarado) {
    $f = new FreteValor($this->_helperData, $cep, $peso, $valor_declarado, $this->_modalidade);
    $r = $f->getData();
    //log
    $message = [
      'file' => __FILE__,
      'line' => __LINE__,
      '$r' => print_r($r, true)
    ];
    $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    $shippingPrice = $r[$cep]['frete'][0]['vltotal'];

    return $shippingPrice;
  }

  private function humanLocationJadlog($pudo_inf) {
    $pi_filler = array(
      'endereco' => '',
      'numero' => '',
      'compl2' => '',
      'bairro' => '',
      'cidade' => '',
      'cep' => ''
    );
    $pi = $pudo_inf+$pi_filler;
    $address1 = $this->_helperData->sanitizeValue($pi['endereco']);
    $streetnum = $this->_helperData->sanitizeValue($pi['numero']);
    $address2 = $this->_helperData->sanitizeValue($pi['compl2']);
    $address3 = $this->_helperData->sanitizeValue($pi['bairro']);
    $city = $pi['cidade'];
    $zipcode = $this->_helperData->sanitizeValue($pi['cep']);

    //$message = "{$address1}, {$streetnum} - {$address2} - {$address3} - CEP: {$zipcode} - {$city}";
    $message = "{$address1}, {$streetnum} - {$address3} - CEP: {$zipcode} - {$city}";
    return $message;
  }

  private function humanLocationDpd($pudo_inf) {
    $pi_filler = array(
      'ADDRESS1' => '',
      'STREETNUM' => '',
      'ADDRESS2' => '',
      'ADDRESS3' => '',
      'CITY' => '',
      'ZIPCODE' => ''
    );
    $pi = $pudo_inf+$pi_filler;
    $address1 = $this->_helperData->sanitizeValue($pi['ADDRESS1']);
    $streetnum = $this->_helperData->sanitizeValue($pi['STREETNUM']);
    $address2 = $this->_helperData->sanitizeValue($pi['ADDRESS2']);
    $address3 = $this->_helperData->sanitizeValue($pi['ADDRESS3']);
    $city = $pi['CITY'];
    $zipcode = $this->_helperData->sanitizeValue($pi['ZIPCODE']);

    $message = "{$address1}, {$streetnum} - {$address2} - {$address3} - CEP: {$zipcode} - {$city}";
    return $message;
  }

  private function humanOpeningHoursJadlog($pudo_item) {
    $horarios = $pudo_item['pudoHorario'];
    $h = [];
    for($i=0; $i<7; $i++) {
      if ($horarios["{$this->abrDayId($i)}H1OpenTm"] == "0000") {
        $h[$i] = 'fechado';
      } else {
        for($j=1; $j<=4; $j+=2) {
          $j1 = $j+1;
          $open = $this->hourString($horarios["{$this->abrDayId($i)}H{$j}OpenTm"]);
          $close = $this->hourString($horarios["{$this->abrDayId($i)}H{$j1}CloseTm"]);
          if (empty($h[$i])) {
            $h[$i] = "das {$this->hourString($open)} às {$this->hourString($close)}";
          } else {
            $h[$i] = $h[$i] . " e das {$this->hourString($open)} às {$this->hourString($close)}";
          }
        }
      }
    }

    $message = "";
    for($i=0; $i<7; $i++) {
      $day = $this->humanDayId($i);
      $message = $message . "{$day}: {$h[$i]}.\n";
    }

    return $message;
  }

  private function humanOpeningHoursDpd($pudo_item) {
    $h = [];
    foreach($pudo_item['OPENING_HOURS_ITEMS']['OPENING_HOURS_ITEM'] as $oh) {
      $day = intval($oh['DAY_ID']) % 7;
      if (empty($h[$day])) {
        $h[$day] = "das {$oh['START_TM']} às {$oh['END_TM']}";
      } else {
        $h[$day] = $h[$day] . " e das {$oh['START_TM']} às {$oh['END_TM']}";
      }
    }

    $message = "";
    for($i=0; $i<7; $i++) {
      //$i = $j % 7;
      if (empty($h[$i])) {
        $h[$i] = 'fechado';
      }
      $day = $this->humanDayId($i);
      $message = $message . "{$day}: {$h[$i]}.\n";
    }

    return $message;
  }

  private function humanDayId($day_id) {
    return self::WEEK_DAYS[$day_id % 7];
  }

  private function abrDayId($day_id) {
    return self::WEEK_DAYS_ABR[$day_id % 7];
  }

  private function hourString($string) {
    return substr($string, 0, 2) . ':' . substr($string, -2);
  }
}
?>
