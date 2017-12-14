<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Renderer_Sprice
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Renderer_Sprice
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price
{
    /**
     * @param Varien_Object $row
     * @return null|string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getTypeID() == "bundle" || $row->getTypeID() == "grouped") {
            $data = $row->getData($this->getColumn()->getIndex());
            return $data > 0 ? round($data) . "%" : NULL;
        } else {
            return parent::render($row);
        }
    }
}