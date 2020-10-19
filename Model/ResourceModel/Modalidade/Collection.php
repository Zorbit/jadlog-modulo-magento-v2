<?php

namespace Jadlog\Embarcador\Model\ResourceModel\Modalidade;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'jadlog_modalidade_collection';
    protected $_eventObject = 'modalidade_collection';

    protected function _construct()
    {
        $this->_init('Jadlog\Embarcador\Model\Modalidade', 'Jadlog\Embarcador\Model\ResourceModel\Modalidade');
    }
}
