<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Jsinit
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Jsinit extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('vti/impgrid/js.phtml');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
}