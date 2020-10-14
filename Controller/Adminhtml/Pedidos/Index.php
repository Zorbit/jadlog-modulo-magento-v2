<?php
namespace Jadlog\Embarcador\Controller\Adminhtml\Pedidos;

use Jadlog\Embarcador\Controller\Adminhtml\AbstractPedidos;

class Index extends AbstractPedidos {
  /**
  * @return \Magento\Framework\View\Result\Page
  */
  public function execute() {
    $resultPage = $this->_resultPageFactory->create();
    $resultPage->setActiveMenu('Jadlog_Embarcador::pedidos')
    ->addBreadcrumb(__('Sales'), __('Sales'))
    ->addBreadcrumb(__('Jadlog'), __('Jadlog'))
    ->addBreadcrumb(__('Gerenciar Pedidos'), __('Gerenciar Pedidos'));
    $resultPage->getConfig()->getTitle()->prepend(__('Gerenciar Pedidos'));

    return $resultPage;
  }
}
?>
