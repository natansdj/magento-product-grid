<?php

/**
 * Class VTI_Pgrid_Model_Mysql4_Columngroup
 */
class VTI_Pgrid_Model_Mysql4_Columngroup extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/grid_column_group', 'entity_id');
    }

}