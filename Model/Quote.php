<?php

namespace Jadlog\Embarcador\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Quote extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'jadlog_quote';
    protected $_cacheTag = 'jadlog_quote';
    protected $_eventPrefix = 'jadlog_quote';

    protected function _construct()
    {
        $this->_init('Jadlog\Embarcador\Model\ResourceModel\Quote');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
