<?php

/**
 * Class VTI_Pgrid_Helper_Data
 */
class VTI_Pgrid_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get selected Group for user
     *
     * @param string $attributesKey
     * @return int
     */
    public function getSelectedGroupId($attributesKey = '')
    {
        // will load columns by admin users, if necessary
        $extraKey = $attributesKey;
        if ($this->isGridAllowed()) {
            if (Mage::getStoreConfig('impgrid/attr/byadmin')) {
                $extraKey .= Mage::getSingleton('admin/session')->getUser()->getId();
            } else {
                //get first admin user
                $extraKey .= '1';
            }
        } else {
            $extraKey = '';
        }

        $groupId = Mage::getStoreConfig('impgrid/attributes/ongrid' . $extraKey)
            ? Mage::getStoreConfig('impgrid/attributes/ongrid' . $extraKey) : 1;

        return (int)$groupId;
    }

    /**
     * @return bool
     */
    public function isGridAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products/impgrid');
    }

    /**
     * @param $groupId
     * @return mixed
     */
    public function prepareGridAttributesCollection($groupId)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributes */
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addStoreLabel($this->_getStoreId());

        /** @var VTI_Pgrid_Helper_Data $helper */
        $helper = Mage::helper('impgrid');
        $conditions = array(
            'main_table.attribute_id = attribute_columns.attribute_id',
            $attributes->getConnection()->quoteInto(
                'attribute_columns.attribute_id IN (?)',
                $helper->getGridAttributes($groupId)
            ),
            $attributes->getConnection()->quoteInto('attribute_columns.group_id = ?', $groupId),
        );
        $attributes->getSelect()->joinInner(
            array('attribute_columns' => $attributes->getTable(
                'impgrid/grid_group_attribute'
            )), implode(' AND ', $conditions),
            array('group_id', 'custom_title')
        );
        return $attributes;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        $storeId = (int)Mage::app()->getRequest()->getParam('store', 0);
        return $storeId;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getGridAttributes($groupId)
    {
        /**
         * @var VTI_Pgrid_Model_Group $group
         */
        $group = $this->getCurrentGridGroup($groupId);

        $selected = $group->getAttributes();

        return $selected;
    }

    /**
     *
     * @param int $groupId
     *
     * @return VTI_Pgrid_Model_Group | Mage_Core_Model_Abstract
     */
    public function getCurrentGridGroup($groupId)
    {
        return Mage::getModel('impgrid/group')->load($groupId);
    }

    /**
     * @return array
     */
    public function getDefaultColumns()
    {
        return array('name', 'second_name', 'type', 'sku', 'price', 'qty', 'visibility', 'status', 'season');
    }

    /**
     * @param VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid $grid
     * @param Mage_Catalog_Model_Resource_Product_Attribute_Collection $gridAttributes
     * @param VTI_Directory_Model_Store $store
     */
    public function attachGridColumns(&$grid, &$gridAttributes, $store)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($gridAttributes as $attribute) {
            $props = array(
                'header'       => $attribute->getCustomTitle() ? $attribute->getCustomTitle() : $attribute->getStoreLabel(),
                'index'        => $attribute->getAttributeCode(),
                'filter_index' => 'im_attribute_' . $attribute->getAttributeCode()
            );
            if ('price' == $attribute->getFrontendInput()) {
                $props['type'] = 'price';
                $props['currency_code'] = $store->getBaseCurrency()->getCode();

                if ($attribute->getAttributeCode() == "special_price")
                    $props['renderer'] = 'impgrid/adminhtml_catalog_product_grid_renderer_sprice';
            }

            if ($attribute->getFrontendInput() == 'weight') {
                $props['type'] = 'number';
            }

            if ($attribute->getFrontendInput() == 'date') {
                $props['type'] = 'date';
            }

            if ('select' == $attribute->getFrontendInput() || 'multiselect' == $attribute->getFrontendInput() || 'boolean' == $attribute->getFrontendInput()) {
                $propOptions = array();

                if ('multiselect' == $attribute->getFrontendInput()) {
                    $propOptions['null'] = $this->__('- No value specified -');
                }

                if ('custom_design' == $attribute->getAttributeCode()) {
                    $allOptions = $attribute->getSource()->getAllOptions();
                    if (is_array($allOptions) && !empty($allOptions)) {
                        foreach ($allOptions as $option) {
                            if (!is_array($option['value'])) {
                                if ($option['value']) {
                                    $propOptions[$option['value']] = $option['value'];
                                }
                            } else {
                                foreach ($option['value'] as $option2) {
                                    if (isset($option2['value'])) {
                                        $propOptions[$option2['value']] = $option2['value'];
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // getting attribute values with translation
                    $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($attribute->getId())
                        ->setStoreFilter($store->getId(), false)
                        ->load();
                    if ($valuesCollection->getSize() > 0) {
                        foreach ($valuesCollection as $item) {
                            $propOptions[$item->getId()] = $item->getValue();
                        }
                    } else {
                        $selectOptions = $attribute->getFrontend()->getSelectOptions();
                        if ($selectOptions) {
                            foreach ($selectOptions as $selectOption) {
                                $propOptions[$selectOption['value']] = $selectOption['label'];
                            }
                        }
                    }
                }

                if ($attribute->getFrontendInput() == 'boolean') {
                    $propOptions = array(
                        '1' => $this->__('Yes'),
                        '0' => $this->__('No')
                    );
                }

                if ('multiselect' == $attribute->getFrontendInput()) {
                    $props['renderer'] = 'impgrid/adminhtml_catalog_product_grid_renderer_multiselect';
                    $props['filter'] = 'impgrid/adminhtml_catalog_product_grid_filter_multiselect';
                }

                $props['type'] = 'options';
                $props['options'] = $propOptions;
            }

            $grid->addColumn($attribute->getAttributeCode(), $props);
        }
    }

    /**
     * @return Mage_Core_Model_Store|null
     */
    public function getStore()
    {
        return $this->_getStore();
    }

    /**
     * @return Mage_Core_Model_Store|null
     */
    protected function _getStore()
    {
        $ret = NULL;

        $storeId = $this->_getStoreId();

        if ($storeId === 0) {

            $ret = Mage::app()->getWebsite(true) ?
                Mage::app()->getWebsite(true)->getDefaultStore() : Mage::app()->getStore();
        } else
            $ret = Mage::app()->getStore($storeId);

        return $ret;
    }

    /**
     * @param string $attributesKey
     * @return mixed
     */
    public function getDefaultGroup($attributesKey = '1')
    {
        return Mage::getStoreConfig('impgrid/attributes/ongrid' . $attributesKey);
    }

    /**
     * @param int $userId
     * @return VTI_Pgrid_Model_Mysql4_Group_Collection
     */
    public function getGroupsByUserId($userId = null)
    {
        $groups = Mage::getModel('impgrid/group')->getCollection();
        $userId = $userId ? $userId : Mage::getSingleton('admin/session')->getUser()->getId();

        if (!Mage::getStoreConfig('impgrid/additional/share_attribute_templates')) {
            $groups->addFieldToFilter('user_id', $userId);
        }

        return $groups;
    }
}