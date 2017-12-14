<?php


/**
 * Class VTI_Pgrid_Model_Mysql4_Groupattribute
 */
class VTI_Pgrid_Model_Mysql4_Groupattribute extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * @var array
     */
    protected $_notEditableAttributes
        = array('tier_price', 'gallery', 'media_gallery', 'recurring_profile',
            'group_price');

    /**
     * @return array
     */
    public function getNotEditableAttributes()
    {
        return $this->_notEditableAttributes;
    }

    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/grid_group_attribute', 'group_attribute_id');
    }

    /**
     * @param $data
     * @return $this
     */
    public function insert($data)
    {
        $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);
        return $this;
    }
}