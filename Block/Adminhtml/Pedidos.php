<?php
namespace Jadlog\Embarcador\Block\Adminhtml;
use Magento\Backend\Block\Widget\Grid\Container;

class Pedidos extends Container {

  protected function _construct() {
    $this->_controller = 'adminhtml_pedidos';
    $this->_blockGroup = 'Jadlog_Embarcador';
    $this->_headerText = __('Gerenciar Pedidos');
    //$this->_addButtonLabel = __('Adicionar Pedidos');
    parent::_construct();
    $this->buttonList->remove('add'); //to remove add button
    $this->buttonList->remove('save'); //to remove save button
    $this->buttonList->remove('reset'); //to remove reset button
    $this->buttonList->remove('delete'); // to remove delete button
  }
}
?>