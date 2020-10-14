<?php
namespace Jadlog\Embarcador\Controller\Adminhtml\Pedidos;
use Jadlog\Embarcador\Helper\Data as HelperData;

#ref. https://webkul.com/blog/inline-editing-grid-in-magento-2-backend/
class InlineEdit extends \Magento\Backend\App\Action {

  protected $jsonFactory;
  protected $_helperData;

  public function __construct(
    \Magento\Backend\App\Action\Context $context,
    \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
    HelperData $helperData
  ) {
    parent::__construct($context);
    $this->jsonFactory = $jsonFactory;
    $this->_helperData = $helperData;
  }

  public function execute() {
    /** @var \Magento\Framework\Controller\Result\Json $resultJson */
    $resultJson = $this->jsonFactory->create();
    $error = false;
    $messages = [];

    if ($this->getRequest()->getParam('isAjax')) {
      $postItems = $this->getRequest()->getParam('items', []);
      //log
      $message = [
        'file' => __FILE__,
        'line' => __LINE__,
        '$postItems' => print_r($postItems, true)
      ];
      $this->_helperData->writeLog(date('Y-m-d H:i:s') . ": " . get_class($this) . '->' . __FUNCTION__, $message);
    //log

      if (!count($postItems)) {
        $messages[] = __('Por favor confira os dados enviados.');
        $error = true;
      } else {
        foreach (array_keys($postItems) as $entityId) {
          /** load model to update the data */
          $model = $this->_objectManager->create('Jadlog\Embarcador\Model\SalesOrder')->load($entityId);
          try {
            $model->setData(array_merge($model->getData(), $postItems[$entityId]));
            $model->save();
          } catch (\Exception $e) {
            $messages[] = "[Error:]  {$e->getMessage()}";
            $error = true;
          }
        }
      }
    }

    return $resultJson->setData([
      'messages' => $messages,
      'error' => $error
    ]);
  }
}
?>
