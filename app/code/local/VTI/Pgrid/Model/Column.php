<?php


/**
 * Class VTI_Pgrid_Model_Column
 *
 * @method string getCode
 * @method int getWidth
 * @method int getColumnType
 * @method int getIsVisible
 * @method int getColumnId
 * @method int getGroupId
 * @method int getGroupColumnId
 * @method string getType
 * @method string getIndex
 * @method string getCustomTitle
 * @method bool getVisibility
 *
 */
class VTI_Pgrid_Model_Column extends Mage_Core_Model_Abstract
{
    const STANDARD_COLUMN = 'standard';

    public function _construct()
    {
        $this->_init('impgrid/column');
    }

    /**
     *
     * @param int $groupId
     * @return VTI_Pgrid_Model_Mysql4_Column_Collection
     */
    public function getCollectionStandard($groupId = null)
    {
        return $this->_getCollection(self::STANDARD_COLUMN, $groupId);
    }

    /**
     * @param string $columnType
     * @param int $groupId
     *
     * @return VTI_Pgrid_Model_Mysql4_Column_Collection
     */
    protected function _getCollection($columnType, $groupId = null)
    {
        $groupId = $groupId ? $groupId : $this->_getHelper()->getSelectedGroupId();
        $collection = $this->getCollection()->addFieldToFilter('column_type', $columnType);
        $collection->getSelect()->joinLeft(
            array('gc' => $collection->getTable('grid_group_column')),
            sprintf('main_table.entity_id = gc.column_id AND gc.group_id = %d', $groupId),
            array("gc.*", "IF(gc.column_id IS NULL, main_table.visible, gc.is_visible) as visibility")
        );
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
     * @return bool
     */
    public function isVisible()
    {
        return (bool)$this->getVisibility();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getCustomTitle()
            ? $this->getCustomTitle()
            : $this->getData('title');
    }

    /**
     * @param $columnCode
     *
     * @return VTI_Pgrid_Model_Column
     */
    public function loadByCode($columnCode)
    {
        $collection = $this->getCollection()->addFieldToFilter('code', $columnCode);
        $collection->getSelect()->joinInner(
            array('gc' => $collection->getTable('grid_group_column')),
            sprintf('main_table.entity_id = gc.column_id AND gc.group_id = %d', $this->_getHelper()->getSelectedGroupId())
        );
        return $collection->getFirstItem();
    }
}