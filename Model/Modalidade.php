<?php

namespace Jadlog\Embarcador\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Modalidade extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'jadlog_modalidades';
    protected $_cacheTag = 'jadlog_modalidades';
    protected $_eventPrefix = 'jadlog_modalidades';

    protected function _construct()
    {
        $this->_init('Jadlog\Embarcador\Model\ResourceModel\Modalidade');
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
