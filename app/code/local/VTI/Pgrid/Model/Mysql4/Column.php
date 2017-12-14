<?php

/**
 * Class VTI_Pgrid_Model_Mysql4_Column
 */
class VTI_Pgrid_Model_Mysql4_Column extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/grid_column', 'entity_id');
    }
}