<?php
namespace Jadlog\Embarcador\Model\ResourceModel\SalesOrder\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Jadlog\Embarcador\Model\ResourceModel\SalesOrder\Collection as SalesOrderCollection;

/**
 * Class Collection
 * Collection for displaying grid of sales documents
 */
class Collection extends SalesOrderCollection implements SearchResultInterface {
  /**
  * @var AggregationInterface
  */
  protected $aggregations;


  /**
  * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
  * @param \Psr\Log\LoggerInterface $logger
  * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
  * @param \Magento\Framework\Event\ManagerInterface $eventManager
  * @param \Magento\Store\Model\StoreManagerInterface $storeManager
  * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
  * @param string $mainTable
  * @param string $eventPrefix
  * @param string $eventObject
  * @param string $resourceModel
  * @param string $model
  * @param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection
  * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
  *
  * @SuppressWarnings(PHPMD.ExcessiveParameterList)
  */
  public function __construct(
    \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
    \Psr\Log\LoggerInterface $logger,
    \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
    \Magento\Framework\Event\ManagerInterface $eventManager,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    $mainTable,
    $eventPrefix,
    $eventObject,
    $resourceModel,
    $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
    \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
    \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
  ) {
    parent::__construct(
      $entityFactory,
      $logger,
      $fetchStrategy,
      $eventManager,
      $connection,
      $resource
    );
    $this->_eventPrefix = $eventPrefix;
    $this->_eventObject = $eventObject;
    $this->_init($model, $resourceModel);
    $this->setMainTable($mainTable);
  }

  /**
  * @return AggregationInterface
  */
  public function getAggregations() {
    return $this->aggregations;
  }

  /**
  * @param AggregationInterface $aggregations
  * @return $this
  */
  public function setAggregations($aggregations) {
    $this->aggregations = $aggregations;
    return $this;
  }

  /**
  * Get search criteria.
  *
  * @return \Magento\Framework\Api\SearchCriteriaInterface|null
  */
  public function getSearchCriteria() {
    return null;
  }

  /**
  * Set search criteria.
  *
  * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
  * @return $this
  * @SuppressWarnings(PHPMD.UnusedFormalParameter)
  */
  public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {
    return $this;
  }

  /**
  * Get total count.
  *
  * @return int
  */
  public function getTotalCount() {
    return $this->getSize();
  }

  /**
  * Set total count.
  *
  * @param int $totalCount
  * @return $this
  * @SuppressWarnings(PHPMD.UnusedFormalParameter)
  */
  public function setTotalCount($totalCount) {
    return $this;
  }

  /**
  * Set items list.
  *
  * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
  * @return $this
  * @SuppressWarnings(PHPMD.UnusedFormalParameter)
  */
  public function setItems(array $items = null) {
    return $this;
  }

  /*protected function _renderFiltersBefore() {
    $joinTable = $this->getTable('sales_order');
    $this->getSelect()->join(
      $joinTable.' as sales_order','jadlog_sales_order.order_id = sales_order.entity_id', ['shipping_description']
    );
    parent::_renderFiltersBefore();
  }*/

  protected function _initSelect() {
    parent::_initSelect();

    $this->getSelect()->join(
      ['sales_order' => $this->getTable('sales_order')],
      'main_table.order_id = sales_order.entity_id',
      ['shipping_description', 'increment_id']
    );
    $this->addFilterToMap('shipping_description', 'sales_order.shipping_description');
    $this->addFilterToMap('increment_id', 'sales_order.increment_id');
  }

}


