<?php

namespace Jadlog\Embarcador\Controller\Frete;
//http://magento.dev.local/jadlog_embarcador/frete/valor

use Jadlog\Embarcador\Helper\Data;
use Jadlog\Embarcador\Integracao\Frete\Valor as FreteValor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Valor extends Action implements HttpPostActionInterface
{
    protected $_pageFactory;
    protected $_helperData;
    protected $request;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $pageFactory,
        Data $helperData,
        RequestInterface $request
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_helperData = $helperData;
        $this->request = $request;
        $this->resultJsonFactory = $resultJsonFactory;

        return parent::__construct($context);
    }

    public function getParam($param)
    {
        return $this->request->getParam($param);
    }

    public function execute()
    {
        $ceps = $this->getParam('cep');
        $cep_array = [];

        foreach ($ceps as $cep) {
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
