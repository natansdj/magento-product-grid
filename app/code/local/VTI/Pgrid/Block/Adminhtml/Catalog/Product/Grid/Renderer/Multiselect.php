<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Renderer_Multiselect
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Renderer_Multiselect extends VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return mixed|string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        if (!empty($options) && is_array($options)) {
            $value = $row->getData($this->getColumn()->getIndex());
            $values = explode(',', $value);
            if (is_array($values)) {
                foreach ($values as &$item) {
                    if (isset($options[$item]))
                        $item = $options[$item];
                }
                $value = implode(', ', $values);
                return $value;
            }
        }
        return '';
    }
}