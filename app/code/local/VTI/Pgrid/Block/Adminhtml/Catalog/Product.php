<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product extends Mage_Adminhtml_Block_Catalog_Product
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vti/impgrid/product.phtml');
    }

    /**
     * @return Mage_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        if ($this->_getHelper()->isGridAllowed()) {
            $url = $this->getUrl('adminhtml/impgrid_attribute/index');
            $this->_addButton('attribute_button', array(
                'label'   => $this->_getHelper()->__('Grid Columns'),
                'onclick' => sprintf("pAttribute.showConfig('%s');", $url),
                'class'   => 'task'
            ));
        }

        return parent::_prepareLayout();
    }

    /**
     * @return VTI_Pgrid_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('impgrid');
    }
}