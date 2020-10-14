<?php
namespace Jadlog\Embarcador\Controller\Frete;
//http://magento.dev.local/jadlog_embarcador/frete/valor

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Jadlog\Embarcador\Integracao\Frete\Valor as FreteValor;

class Valor extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface {
  protected $_pageFactory;
  protected $_helperData;
  protected $request;

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
    \Magento\Framework\View\Result\PageFactory $pageFactory,
    \Jadlog\Embarcador\Helper\Data $helperData,
    \Magento\Framework\App\RequestInterface $request
  ) {
    $this->_pageFactory = $pageFactory;
    $this->_helperData = $helperData;
    $this->request = $request;
    $this->resultJsonFactory = $resultJsonFactory;
    return parent::__construct($context);
  }

  public function getParam($param) {
    return $this->request->getParam($param);
  }

  public function execute() {
    //echo "Hello World " . print_r($this->getParam('cep'),true);
    //echo "42.42";
    //exit;
    $ceps = $this->getParam('cep');
    $cep_array =[];
    foreach($ceps as $cep) {
      array_push($cep_array, $this->_helperData->getCep($cep)['cep']);
    }
    $result = $this->resultJsonFactory->create();
    $f = new FreteValor(
      $this->_helperData,
      $cep_array,
      $this->getParam('peso'),
      $this->getParam('valor_declarado'),
      $this->getParam('modalidade')
    );
    $r = $f->getData();

    //return $result->setData(['$f->getData()' => $r]);
    return $result->setData(['$f->getData()' => $r] + ['success' => true] + ['valor' => $r[$cep_array[0]]['frete'][0]['vltotal']]);
  }
}
