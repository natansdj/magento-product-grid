<?php


/**
 * Class VTI_Pgrid_Model_Group
 *
 * @method setIsDefault
 * @method getIsDefault
 */
class VTI_Pgrid_Model_Group extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        $this->_init('impgrid/group');
    }

    /**
     * Flag for check categories
     * @return string
     */
    public function getCategoriesKey()
    {
        return 'category';
    }

    public function loadActiveGroup($attributesKey = '')
    {
        $groupId = Mage::helper('impgrid')->getSelectedGroupId($attributesKey);
        return $this->load($groupId);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $coll = Mage::getModel('impgrid/groupattribute')
            ->getCollection()
            ->addFieldToFilter('group_id', $this->getId())
            ->getColumnValues('attribute_id');
        return $coll;
    }
}
