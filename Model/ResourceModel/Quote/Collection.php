<?php

namespace Jadlog\Embarcador\Model\ResourceModel\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'jadlog_quote_collection';
    protected $_eventObject = 'quote_collection';

    protected function _construct()
    {
        $this->_init('Jadlog\Embarcador\Model\Quote', 'Jadlog\Embarcador\Model\ResourceModel\Quote');
    }
}
