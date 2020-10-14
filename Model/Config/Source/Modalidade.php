<?php
namespace Jadlog\Embarcador\Model\Config\Source;
use Magento\Framework\Option\ArrayInterface;

// ./generated/code/Jadlog/Embarcador/Model/ResourceModel/Modalidade/CollectionFactory.php

class Modalidade implements ArrayInterface {
  protected $_modalidadeFactory;
  protected $_modalidadeCollectionFactory;

  public function __construct(
    \Jadlog\Embarcador\Model\ModalidadeFactory $modalidadeFactory,
    \Jadlog\Embarcador\Model\ResourceModel\Modalidade\CollectionFactory $modalidadeCollectionFactory
  ) {
    $this->_modalidadeFactory = $modalidadeFactory;
    $this->_modalidadeCollectionFactory = $modalidadeCollectionFactory;
  }

  public function getModalidadeCollection($ativo = true, $sortBy = false, $pageSize = false) {
    $collection = $this->_modalidadeCollectionFactory->create();

    if ($ativo) {
      $collection->addFieldToFilter('ativo', true);
    }

    if ($sortBy) {
      $collection->addOrder($sortBy, $direction = 'asc');
    }

    if ($pageSize) {
      $collection->setPageSize($pageSize);
    }
    return $collection;
  }

  public function toOptionArray() {
    $arr = $this->_toArray();
    $ret = [];

    foreach ($arr as $key => $value) {
      $ret[] = [
        'value' => $key,
        'label' => $value
      ];
    }

    return $ret;
  }

  private function _toArray() {
    $modalidades = $this->getModalidadeCollection(true, "descricao", false);

    $modalidadeList = array();
    foreach ($modalidades as $modalidade) {
      $modalidadeList[$modalidade->getCodigo()] = __($modalidade->getDescricao());
    }

    return $modalidadeList;
  }

}

?>