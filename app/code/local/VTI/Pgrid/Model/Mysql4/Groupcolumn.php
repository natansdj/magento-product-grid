<?php

/**
 * Class VTI_Pgrid_Model_Mysql4_Groupcolumn
 */
class VTI_Pgrid_Model_Mysql4_Groupcolumn extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/grid_group_column', 'group_column_id');
    }
}