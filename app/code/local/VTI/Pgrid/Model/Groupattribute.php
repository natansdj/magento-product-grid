<?php

/**
 * Class VTI_Pgrid_Model_Groupattribute
 */
class VTI_Pgrid_Model_Groupattribute extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('impgrid/groupattribute');
    }

    /**
     * @param int $groupId
     * @return VTI_Pgrid_Model_Mysql4_GroupAttribute_Collection
     */
    public function getCollectionAttribute($groupId)
    {
        $collection = $this->getCollection()->getCollectionAttribute($groupId);
        return $collection;
    }

    /**
     * @return VTI_Pgrid_Model_Mysql4_Groupattribute_Collection|object
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return (bool)((!$this->getColumnId() && $this->getColumnType() == 'standard') || $this->getIsVisible());
    }

    /**
     * @param $data
     * @return $this
     */
    public function insert($data)
    {
        $this->getResource()->insert($data);
        return $this;
    }
}