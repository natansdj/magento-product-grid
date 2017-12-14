<?php

/**
 * Class VTI_Pgrid_Model_Mysql4_Group
 */
class VTI_Pgrid_Model_Mysql4_Group extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/grid_group', 'entity_id');
    }
}