<?php
namespace Jadlog\Embarcador\Controller\PudoService;
//http://magento.dev.local/jadlog_embarcador/pudoservice/getpudolist

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Jadlog\Embarcador\Integracao\MyPudo\PudoService as PudoService;

class GetPudoList extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface {
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
    $zipcode = $this->getParam('zipcode');
    $city = $this->getParam('city');;
    $result = $this->resultJsonFactory->create();
    $f = new PudoService(
      $this->_helperData,
      $zipcode,
      $city
    );
    $r = $f->getData();

    //return $result->setData(['$f->getData()' => $r]);
    return $result->setData(['$f->getData()' => $r]);
  }
}
