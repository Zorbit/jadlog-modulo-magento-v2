<?php
namespace Jadlog\Embarcador\Integracao\Pedido;

use Jadlog\Embarcador\Helper\Data as HelperData;

class Incluir {

  private $helperData;
  private $order;
  private $jadlogOrder;
  private $token;
  private $conteudo;
  private $pedido;
  private $totPeso;
  private $totValor;
  private $obs;
  private $modalidade;
  private $conta;
  private $tpcoleta;
  private $tpfrete;
  private $contrato;
  private $cdpickupdes;
  private $rem = [];
  private $des = [];
  private $dfe = [];
  private $pedidourl;

  public function __construct($helper, $order, $jadlog_order, $region_factory) {
    $this->helperData = $helper;
    $this->order = $order;
    $this->jadlogOrder = $jadlog_order;
    $this->token = $this->helperData->getToken();
    $this->conteudo = "PRODUTOS";
    $this->pedido = $this->order->getId();
    $this->totPeso = $this->order->getWeight();
    $this->totValor = $this->order->getSubTotal(); //total dos produtos, $this->order->getGrandTotal() = Total com frete
    $this->obs = "";
    $this->modalidade = $this->helperData->getModalidadeByShippingMethod($order->getShippingMethod());
    $this->conta = $this->helperData->getContaCorrente();
    $this->tpcoleta = "K";
    $this->tpfrete = 0;
    $this->cdpickupdes = null;
    $this->contrato = $this->helperData->getNumeroContrato();
    if($this->helperData->isEntregaJadlogPickup($order->getShippingMethod())) {
      $this->cdpickupdes = $jadlog_order->getPudoId();
    }
    $this->rem["nome"] = $this->helperData->getRemetenteNome();
    $this->rem["cnpjCpf"] = $this->helperData->getRemetenteCNPJ();
    $this->rem["ie"] = $this->helperData->getRemetenteIE();
    $this->rem["endereco"] = $this->helperData->getRemetenteEndereco();
    $this->rem["numero"] = $this->helperData->getRemetenteNumero();
    $this->rem["compl"] = $this->helperData->getRemetenteComplemento();
    $this->rem["bairro"] = $this->helperData->getRemetenteBairro();
    $this->rem["cidade"] = $this->helperData->getRemetenteCidade();
    $this->rem["uf"] = $this->helperData->getRemetenteUF();
    $this->rem["cep"] = $this->helperData->getRemetenteCep();
    $this->rem["fone"] = $this->helperData->getRemetenteTelefone();
    $this->rem["cel"] = $this->helperData->getRemetenteCelular();
    $this->rem["email"] = $this->helperData->getRemetenteEmail();
    $this->rem["contato"] = $this->helperData->getRemetenteContato();

    $to_address = $this->order->getShippingAddress();
    $region = $region_factory->create()->load($to_address->getRegionId());
    $this->des["nome"] = "{$to_address->getFirstname()} {$to_address->getLastname()}";
    $this->des["cnpjCpf"] = $this->helperData->extraiNumeros($this->order->getCustomerTaxvat());
    $this->des["ie"] = "";
    $this->des["endereco"] = join(" - ", $to_address->getStreet());
    $this->des["numero"] = "";
    $this->des["compl"] = "";
    $this->des["bairro"] = "";
    $this->des["cidade"] = $to_address->getCity();
    $this->des["uf"] = $region->getCode();
    $cep_des = $this->helperData->getCep($to_address->getPostcode());
    $this->des["cep"] = $cep_des['cep'];
    $this->des["fone"] = $to_address->getTelephone();
    $this->des["cel"] = $to_address->getFax();
    $this->des["email"] = $to_address->getEmail();
    $this->des["contato"] = $to_address->getFirstname();

    $this->dfe = $this->helperData->extraiDFE($jadlog_order->getCamposDfe());

    $this->pedidourl = $this->helperData->getPedidoURL();
  }


  public function execute() {
    if($this->dfe['error']) {
      $result = ['raw_result' => $this->dfe['error']];
    } else {
      $result = $this->setData();
    }
    $shipmentid = "";
    $codigo = "";
    $status = "";
    if (array_key_exists('result',$result) && $result['result']) {
      $r = $result['result'];
      $status = $r['status'];
      if(array_key_exists('erro', $r)) {
        $status = $status . ": " . $r['erro']['descricao'];
      } else {
        $shipmentid = $r['shipmentId'];
        $codigo = $r['codigo'];
      }
    } else {
      $status = $result['raw_result'];
    }

    //salvar jadlog sales order
    if ($shipmentid) {
      $this->jadlogOrder->setShipmentId($shipmentid);
    }
    if ($codigo) {
      $this->jadlogOrder->setCodigo($codigo);
    }
    $this->jadlogOrder->setStatus($status)->save();
    return true;
  }

  public function setData() {
    return $this->setRealData();
  }

  private function getDataFromService($data) {
    $data_string = json_encode($data);
    $ch = curl_init($this->pedidourl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
      $ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: ' . $this->token,
        'Content-Length: ' . strlen($data_string)
      )
    );

    $raw_result = curl_exec($ch);
    $result = json_decode($raw_result, true);

    //log
    $message = [
      'file' => __FILE__,
      'line' => __LINE__,
      '$data' => print_r($data, true),
      '$this->pedidourl' => $this->pedidourl,
      '$raw_result' => print_r($raw_result, true),
      '$result' => print_r($result, true)
    ];
    $this->helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    return ["raw_result" => $raw_result, "result" => $result];
  }

  private function setRealData() {
    $data = [
      "conteudo" => $this->conteudo,
      "pedido" => [$this->pedido],
      "totPeso" => $this->totPeso,
      "totValor" => $this->totValor,
      "obs" => $this->obs,
      "modalidade" => $this->modalidade,
      "contaCorrente" => $this->conta,
      "tpColeta" => $this->tpcoleta,
      "tipoFrete" => $this->tpfrete,
      "nrContrato" => $this->contrato,
      "rem" => [
        "nome" => $this->rem["nome"],
        "cnpjCpf" => $this->rem["cnpjCpf"],
        "ie" => $this->rem["ie"],
        "endereco" => $this->rem["endereco"],
        "numero" => $this->rem["numero"],
        "compl" => $this->rem["compl"],
        "bairro" => $this->rem["bairro"],
        "cidade" => $this->rem["cidade"],
        "uf" => $this->rem["uf"],
        "cep" => $this->rem["cep"],
        "fone" => $this->rem["fone"],
        "cel" => $this->rem["cel"],
        "email" => $this->rem["email"],
        "contato" => $this->rem["contato"]
      ],
      "des" => [
        "nome" => $this->des["nome"],
        "cnpjCpf" => $this->des["cnpjCpf"],
        "ie" => $this->des["ie"],
        "endereco" => $this->des["endereco"],
        "numero" => $this->des["numero"],
        "compl" => $this->des["compl"],
        "bairro" => $this->des["bairro"],
        "cidade" => $this->des["cidade"],
        "uf" => $this->des["uf"],
        "cep" => $this->des["cep"],
        "fone" => $this->des["fone"],
        "cel" => $this->des["cel"],
        "email" => $this->des["email"],
        "contato" => $this->des["contato"]
      ],
      "dfe" => $this->dfe['dfes'],
      "volume" => [
        [
          "peso" => $this->totPeso
        ]
      ]
    ];
    if($this->cdpickupdes) {
      $data["cdPickupDes"] = $this->cdpickupdes;
    }

    //log
    //$message = [
    //  'file' => __FILE__,
    //  'line' => __LINE__,
    //  '$data' => print_r($data, true)
    //];
    //$this->helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    return $this->getDataFromService($data);
  }

}

?>
