<?php
namespace Jadlog\Embarcador\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Directory\Model\RegionFactory;
use Jadlog\Embarcador\Model\ResourceModel\SalesOrder\CollectionFactory as JadlogOrderCollectionFactory;
use Magento\Sales\Model\OrderFactory;
use Jadlog\Embarcador\Helper\Data as HelperData;

abstract class AbstractPedidos extends Action {
  const ADMIN_RESOURCE = 'Jadlog_Embarcador::pedidos';

  protected $_coreRegistry;
  protected $_resultPageFactory;
  protected $_orderFactory;
  protected $_jadlogOrderCollectionFactory;
  protected $_helperData;
  protected $_filter;
  protected $_regionFactory;

  public function __construct(
    Context $context,
    Registry $coreRegistry,
    PageFactory $resultPageFactory,
    JadlogOrderCollectionFactory $jadlogOrderCollectionFactory,
    OrderFactory $orderFactory,
    HelperData $helperData,
    Filter $filter,
    RegionFactory $regionFactory
  ) {
    parent::__construct($context);
    $this->_coreRegistry = $coreRegistry;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_jadlogOrderCollectionFactory = $jadlogOrderCollectionFactory;
    $this->_orderFactory = $orderFactory;
    $this->_helperData = $helperData;
    $this->_filter = $filter;
    $this->_regionFactory = $regionFactory;
  }

  //public function execute() {
  //  return $this->_resultPageFactory->create();
  //}

  protected function _isAllowed() {
    return $this->_authorization->isAllowed(AbstractPedidos::ADMIN_RESOURCE);
  }
}