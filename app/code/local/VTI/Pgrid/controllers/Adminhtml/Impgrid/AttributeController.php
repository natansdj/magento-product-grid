<?php

/**
 * Class VTI_Pgrid_Adminhtml_Impgrid_AttributeController
 */
class VTI_Pgrid_Adminhtml_Impgrid_AttributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Column grid
     */
    public function indexAction()
    {
        $attributesKey = Mage::app()->getRequest()->getParam('attributesKey', '');
        $block = $this->getLayout()->createBlock('impgrid/adminhtml_catalog_product_grid_attributes', '', array('attributes_key' => $attributesKey));
        if ($block) {
            /** @var VTI_Pgrid_Helper_Utils $utilsHelp */
            $utilsHelp = Mage::helper('impgrid/utils');
            $utilsHelp->_echo($block->toHtml());
            $utilsHelp->_exit(1);
        }
    }

    /**
     * Save columns grid settings in to groups
     *
     * @throws Exception
     */
    public function saveAction()
    {
        $request = Mage::app()->getRequest();

        $extraKey = $request->getParam('attributesKey', '');

        //Save Group
        /** @var VTI_Pgrid_Model_Group $currentGroup */
        $currentGroup = Mage::getModel('impgrid/group');
        $currentGroup->loadActiveGroup($extraKey);
        $currentGroup->save();

        //Save Columns
        $columns = $this->getRequest()->getParam('column');
        $this->_saveColumns($columns);

        //Save Attributes
        $attributes = $request->getParam('pattribute', array());
        foreach ($attributes as $key => $attr) {
            if (!$attr['attribute_id']) {
                unset($attributes[$key]);
            }
        }
        $this->_saveAttributes($attributes, $currentGroup);

        $this->_redirectReferer();
    }

    /**
     * @param array $columnsData
     *
     * @throws Exception
     */
    protected function _saveColumns($columnsData)
    {
        foreach ($columnsData as $columnId => $columnData) {
            $columnModel = Mage::getModel('impgrid/groupcolumn');
            $columnModel->load($columnData['group_column_id']);
            $columnModel->setData('is_visible', $columnData['is_visible']);
            if (array_key_exists('custom_title', $columnData)) {
                $columnModel->setData(
                    'custom_title', $columnData['custom_title']
                );
            }
            $columnModel->save();
        }
    }

    /**
     * @param array $attributesData
     * @param VTI_Pgrid_Model_Group $currentGroup
     *
     * @throws Exception
     */
    protected function _saveAttributes($attributesData, $currentGroup)
    {
        /** @var VTI_Pgrid_Model_Groupattribute $attrModel */
        $attrModel = Mage::getModel('impgrid/groupattribute');
        $attrModel->getCollection()
            ->addFieldToFilter('group_id', $currentGroup->getId())
            ->walk('delete');
        if (!empty($attributesData)) {
            $copyAttribute = array();
            foreach ($attributesData as $key => &$value) {
                $value['group_id'] = $currentGroup->getId();

                if (in_array($value['attribute_id'], $copyAttribute)) {
                    unset($attributesData[$key]);
                } else {
                    $copyAttribute[] = $value['attribute_id'];
                }
            }
            $attrModel->insert($attributesData);
        }
    }

    /**
     * @param $groupId
     * @param string $attributeKey
     */
    protected function _changeGroup($groupId, $attributeKey = '')
    {
        if (Mage::getStoreConfig('impgrid/attr/byadmin')) {
            $attributeKey .= Mage::getSingleton('admin/session')->getUser()->getId();
        }

        Mage::getConfig()->saveConfig('impgrid/attributes/ongrid' . $attributeKey, $groupId);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        /** @var VTI_Pgrid_Helper_Data $helper */
        $helper = Mage::helper('impgrid');
        return $helper->isGridAllowed();
    }

    /**
     * @return Mage_Core_Controller_Response_Http
     */
    protected function _redirectBack()
    {
        $backUrl = Mage::app()->getRequest()->getParam('backurl');
        if (!$backUrl) {
            $backUrl = Mage::getUrl('adminhtml/catalog/product');
        }
        return $this->getResponse()->setRedirect($backUrl);
    }
}