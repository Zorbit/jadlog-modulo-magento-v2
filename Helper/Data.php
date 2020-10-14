<?php
namespace Jadlog\Embarcador\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Jadlog\Embarcador\Model\Carrier\JadlogExpresso;
use Jadlog\Embarcador\Model\Carrier\JadlogPickup;

class Data extends AbstractHelper {
  public function getCodigoPickup() {
    return 40; #Pickup
  }

  public function getCodigoExpresso() {
    return 9; #.COM = Expresso
  }

  public function getModalidadeByShippingMethod($shippingMethod) {
    switch($shippingMethod) {
      case JadlogExpresso::carrierMethod(): return $this->getCodigoExpresso();
      case JadlogPickup::carrierMethod(): return $this->getCodigoPickup();
      default: return false;
    }
  }

  public function extraiNumeros($x) {
    return preg_replace("/\D/", "", $x);
  }

  public function extraiDFE($d) {
    $error = "";
    $dfes = [];
    $dfeMsgErroDefault = 'Campos DFE inválido! Separar campos por "," e DFEs por "|". Ex. com 2 DFEs: "cfop,danfeCte,nrDoc,serie,tpDocumento,valor|cfop,danfeCte,nrDoc,serie,tpDocumento,valor". Com ex. de dados: "6909,null,DECLARACAO,null,2,20.2|6909,null,DECLARACAO,null,2,20.2"';

    if (empty($d)) {
      $error = $dfeMsgErroDefault;
    } else {
      $dfesToParser = explode('|', $d);
      foreach ($dfesToParser as $dfe) {
        if (strlen(trim($dfe)) == 0) {
          $error = $dfeMsgErroDefault;
          break;
        }
        $fs = explode(',', $dfe);
        if (count($fs) != 6) {
          $error = $dfeMsgErroDefault;
          break;
        }
        foreach ($fs as $f) {
          if (strlen(trim($f)) == 0) {
            $error = $dfeMsgErroDefault;
            break;
          }
        }
        if ($error) {
          break;
        }
        $dfes[] = [
          'cfop'        => trim($fs[0]) == "null" ? null : trim($fs[0]),
          'danfeCte'    => trim($fs[1]) == "null" ? null : trim($fs[1]),
          'nrDoc'       => trim($fs[2]) == "null" ? null : trim($fs[2]),
          'serie'       => trim($fs[3]) == "null" ? null : trim($fs[3]),
          'tpDocumento' => trim($fs[4]) == "null" ? null : trim($fs[4]),
          'valor'       => trim($fs[5]) == "null" ? null : trim($fs[5])
        ];
      }
    }
    return ["error" => $error, "dfes" => $dfes];
  }

  public function getCep($cep) {
    $error = "";
    //extrai somente os números
    $cepNumbers = $this->extraiNumeros($cep);
    if (strlen($cepNumbers) != 8) {
      $error = "CEP inválido. O CEP deve conter 8 algarismos.";
      $cepNumbers = $cep;
    }
    return array("error" => $error, "cep" => $cepNumbers);
  }

  public function sanitizeValue($x) {
    $r = $x;
    if (is_array($x)) {
      $r = join($x," ");
    }
    return trim($r);
  }

  public function getConfigValue($field) {
    return $this->scopeConfig->getValue($field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getModalidades() {
    return array_map("intval",explode(",",$this->getConfigValue("jadlog_embarcador/geral/modalidades")));
  }

  public function getHabilitar() {
    return boolval($this->getConfigValue("jadlog_embarcador/geral/habilitar"));
  }

  public function getPickupHabilitado() {
    return in_array($this->getCodigoPickup(), $this->getModalidades());
  }

  public function getExpressoHabilitado() {
    return in_array($this->getCodigoExpresso(), $this->getModalidades());
  }

  public function getConfig() {
    return $this->getConfigValue("jadlog_embarcador");
  }

  public function isEntregaJadlogPickup($shippingMethod) {
    return (JadlogPickup::carrierMethod() === $shippingMethod);
  }

  public function isEntregaJadlogExpresso($shippingMethod) {
    return (JadlogExpresso::carrierMethod() === $shippingMethod);
  }

  public function isEntregaJadlog($shippingMethod) {
    return ($this->isEntregaJadlogPickup($shippingMethod) || $this->isEntregaJadlogExpresso($shippingMethod));
  }

  public function getToken() {
    return $this->getConfigValue("jadlog_embarcador/geral/token");
  }

  public function getNumeroContrato() {
    return $this->getConfigValue("jadlog_embarcador/geral/numero_contrato");
  }

  public function getContaCorrente() {
    return $this->getConfigValue("jadlog_embarcador/geral/conta_corrente");
  }

  public function getFreteURL() {
    return $this->getConfigValue("jadlog_embarcador/geral/frete_url");
  }

  public function getPedidoURL() {
    return $this->getConfigValue("jadlog_embarcador/geral/pedido_url");
  }

  public function getTrackingURL() {
    return $this->getConfigValue("jadlog_embarcador/geral/tracking_url");
  }

  public function getRemetenteNome() {
    return $this->getConfigValue("jadlog_embarcador/remetente/nome");
  }

  public function getRemetenteCNPJ() {
    return $this->getConfigValue("jadlog_embarcador/remetente/cnpj");
  }

  public function getRemetenteIE() {
    return $this->getConfigValue("jadlog_embarcador/remetente/ie");
  }

  public function getRemetenteContato() {
    return $this->getConfigValue("jadlog_embarcador/remetente/contato");
  }

  public function getRemetenteTelefone() {
    return $this->getConfigValue("jadlog_embarcador/remetente/telefone");
  }

  public function getRemetenteCelular() {
    return $this->getConfigValue("jadlog_embarcador/remetente/celular");
  }

  public function getRemetenteEmail() {
    return $this->getConfigValue("jadlog_embarcador/remetente/email");
  }

  public function getRemetenteEndereco() {
    return $this->getConfigValue("jadlog_embarcador/remetente/endereco");
  }

  public function getRemetenteNumero() {
    return $this->getConfigValue("jadlog_embarcador/remetente/numero");
  }

  public function getRemetenteComplemento() {
    return $this->getConfigValue("jadlog_embarcador/remetente/complemento");
  }

  public function getRemetenteUF() {
    return $this->getConfigValue("jadlog_embarcador/remetente/uf");
  }

  public function getRemetenteCidade() {
    return $this->getConfigValue("jadlog_embarcador/remetente/cidade");
  }

  public function getRemetenteBairro() {
    return $this->getConfigValue("jadlog_embarcador/remetente/bairro");
  }

  public function getRemetenteCep() {
    return $this->getConfigValue("jadlog_embarcador/remetente/cep");
  }

  public function getMyPudoURL() {
    return $this->getConfigValue("jadlog_embarcador/pickup/mypudo_url");
  }

  public function getMyPudoShipperId() {
    return $this->getConfigValue("jadlog_embarcador/pickup/mypudo_shipper_id");
  }

  public function getMyPudoKey() {
    return $this->getConfigValue("jadlog_embarcador/pickup/mypudo_key");
  }

  public function writeLog($ident, $msg) {
    // tail -f /var/www/html/var/log/jadlog_embarcador.log
    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jadlog_embarcador.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);
    ob_start();
    $message = ["$ident" => $msg];
    print_r($message);
    $logger->info(ob_get_clean());
  }
}
?>