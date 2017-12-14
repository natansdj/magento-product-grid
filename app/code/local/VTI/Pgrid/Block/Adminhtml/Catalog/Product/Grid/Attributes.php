<?php


/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Attributes
 *
 * @method array getStandardColumns
 * @method string getAttributesKey
 * @method VTI_Pgrid_Model_Mysql4_GroupAttribute_Collection getAttributeColumns
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid_Attributes extends Mage_Adminhtml_Block_Template
{
    /**
     * @return mixed
     */
    public function getAttributes()
    {
        $collection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addFieldToFilter('main_table.frontend_input', array('in' => array('text', 'select', 'multiselect', 'boolean', 'textarea', 'price', 'weight', 'date')))
            ->addFieldToFilter('main_table.attribute_code', array('nin' => $this->_getHelper()->getDefaultColumns()))
            ->setOrder('main_table.frontend_label', "ASC");
        return $collection;
    }

    /**
     * @return VTI_Pgrid_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('impgrid');
    }

    /**
     * @return mixed|string
     */
    public function getSaveUrl()
    {
        $url = $this->getUrl('adminhtml/impgrid_attribute/save');
        if (Mage::getStoreConfig('web/secure/use_in_adminhtml')) {
            $url = str_replace(Mage::getStoreConfig('web/unsecure/base_url'), Mage::getStoreConfig('web/secure/base_url'), $url);
        }
        return $url;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->initVariables();
        $this->setTemplate('vti/impgrid/columns.phtml');
    }

    protected function initVariables()
    {
        $attributesKey = $this->getAttributesKey();
        $groupId = $this->_getHelper()->getSelectedGroupId($attributesKey);

        /** @var VTI_Pgrid_Model_Column $columnModel */
        $columnModel = Mage::getModel('impgrid/column');
        $standardColumns = $columnModel->getCollectionStandard($groupId);

        /** @var VTI_Pgrid_Model_Groupattribute $groupAttrModel */
        $groupAttrModel = Mage::getModel('impgrid/groupattribute');
        $attributeColumns = $groupAttrModel->getCollectionAttribute($groupId);

        $variables = array(
            'group_id'          => $groupId,
            'groups'            => $this->_getHelper()->getGroupsByUserId(),
            'standard_columns'  => $standardColumns,
            'attribute_columns' => $attributeColumns,
        );

        foreach ($variables as $varName => $value) {
            $this->setData($varName, $value);
        }

    }

}