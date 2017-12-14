<?php

/**
 * Class VTI_Pgrid_Model_Observer
 */
class VTI_Pgrid_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function adminUserSaveAfter(Varien_Event_Observer $observer)
    {
        $adminId = $observer->getEvent()->getObject()->getUserId();
        $columns = Mage::getModel('impgrid/column')->getCollection();

        $currentGroup = Mage::getModel('impgrid/group');
        $currentGroup->setData('title', 'Default');
        $currentGroup->setData('user_id', $adminId);
        $currentGroup->save();

        Mage::getConfig()->saveConfig('impgrid/attributes/ongrid' . $adminId, $currentGroup->getId());
        Mage::getModel('core/config')->cleanCache();

        foreach ($columns as $columnData) {
            $columnModel = Mage::getModel('impgrid/groupcolumn');
            $columnModel->setData('column_id', $columnData['entity_id']);
            $columnModel->setData('group_id', $currentGroup->getId());
            $columnModel->setData('is_visible', $columnData['visible']);
            $columnModel->setData('custom_title', $columnData['title']);
            $columnModel->save();
        }
    }
}