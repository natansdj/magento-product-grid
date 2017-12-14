<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Filter_Multiselect
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Filter_Multiselect extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @return array
     */
    public function getCondition()
    {
        if ('null' == $this->getValue()) {
            return array('null' => true);
        }
        return array('finset' => $this->getValue());
    }
}