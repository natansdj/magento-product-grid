<?php

/**
 * Class VTI_Pgrid_Model_Mysql4_Columngroup_Collection
 */
class VTI_Pgrid_Model_Mysql4_Columngroup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Resource initialization
     */
    public function _construct()
    {
        $this->_init('impgrid/columngroup');
    }
}