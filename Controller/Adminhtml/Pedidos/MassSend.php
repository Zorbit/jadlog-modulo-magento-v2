<?php
namespace Jadlog\Embarcador\Controller\Adminhtml\Pedidos;

use Jadlog\Embarcador\Controller\Adminhtml\AbstractPedidos;
use Jadlog\Embarcador\Integracao\Pedido\Incluir as PedidoIncluir;
use Magento\Framework\Controller\ResultFactory;

class MassSend extends AbstractPedidos {

  public function execute() {
    $collection = $this->_filter->getCollection($this->_jadlogOrderCollectionFactory->create());
    $collectionSize = $collection->getSize();

    //log
    $message = [
      'file' => __FILE__,
      'line' => __LINE__,
      '$collectionSize' => print_r($collectionSize, true)
    ];
    $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

    $processados = 0;
    foreach ($collection as $jadlogOrder) {
      $order = $this->_orderFactory->create();
      $order->load($jadlogOrder->getOrderId());

      $p = new PedidoIncluir(
        $this->_helperData,
        $order,
        $jadlogOrder,
        $this->_regionFactory
      );
      if($p->execute()) {
        $processados++;
      }
    }

    $this->messageManager->addSuccessMessage(
      __('Um total de %1 pedido(s) foram processados.', $processados)
    );

    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

    return $resultRedirect->setPath('*/*/');
  }
}
