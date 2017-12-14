<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid
 */
class VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /**
     * @var Mage_Catalog_Model_Resource_Product_Attribute_Collection|array
     */
    protected $_gridAttributes;

    /**
     * @var int
     */
    protected $_groupId;

    /**
     * VTI_Pgrid_Block_Adminhtml_Catalog_Product_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_exportPageSize = null;
        $this->_gridAttributes = new Varien_Object();
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection|Varien_Data_Collection $collection
     */
    public function setCollection($collection)
    {
        $store = $this->_getStore();

        if (!Mage::registry('product_collection')) {
            Mage::register('product_collection', $collection);
        }

        /**
         * Adding attributes
         */
        if ($this->_gridAttributes->getSize() > 0) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            foreach ($this->_gridAttributes as $attribute) {
                $collection->joinAttribute($attribute->getAttributeCode(), 'catalog_product/' . $attribute->getAttributeCode(), 'entity_id', null, 'left', $store->getId());
            }
        }

        return parent::setCollection($collection);
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize((int)$this->getParam($this->getVarNameLimit(), Mage::getStoreConfig('impgrid/general/number_of_records')));
        $this->getCollection()->setCurPage((int)$this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    /**
     * get collection object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection|Varien_Data_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setExportVisibility('true');

        $this->_groupId = $this->_getHelper()->getSelectedGroupId();
        $this->_gridAttributes = $this->_getHelper()->prepareGridAttributesCollection($this->_groupId);

        return $this;
    }

    /**
     * @return VTI_Pgrid_Helper_Data|Mage_Core_Helper_Abstract
     */
    protected function _getHelper()
    {
        return Mage::helper('impgrid');
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();

            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();

                if ($field && isset($cond)) {
                    if (strpos($field, 'im_attribute_') !== FALSE) {
                        $attribute = str_replace('im_attribute_', '', $field);

                        $this->getCollection()->addAttributeToFilter($attribute, $cond);
                    } else {

                        parent::_addColumnFilterToCollection($column);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();

        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();

            if (strpos($columnIndex, 'im_attribute_') !== FALSE) {
                $attribute = str_replace('im_attribute_', '', $columnIndex);
                $collection->addAttributeToSort($attribute, $column->getDir());
            } else {
                parent::_setCollectionOrder($column);
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addExportType('adminhtml/impgrid_product/exportCsv', $this->_getHelper()->__('CSV'));
        $this->addExportType('adminhtml/impgrid_product/exportExcel', $this->_getHelper()->__('Excel XML'));

        $this->_prepareColumnsStandard();

        $actionsColumn = null;
        if (isset($this->_columns['action'])) {
            $actionsColumn = $this->_columns['action'];
            unset($this->_columns['action']);
        }

        // adding cost column
        if ($this->_gridAttributes->getSize() > 0) {
            Mage::register('impgrid_grid_attributes', $this->_gridAttributes);

            $this->_getHelper()->attachGridColumns($this, $this->_gridAttributes, $this->_getStore());
        }

        if ($actionsColumn && !$this->_isExport) {
            $this->_columns['action'] = $actionsColumn;
        }

        if ($this->_getHelper()->isModuleEnabled('Mage_Rss')) {
            $this->addRssList('rss/catalog/notifystock', $this->_getHelper()->__('Notify Low Stock RSS'));
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumnsStandard()
    {
        /** @var VTI_Pgrid_Model_Column $columnModel */
        $columnModel = Mage::getModel('impgrid/column');
        $standardColumns = $columnModel->getCollectionStandard($this->_groupId);
        foreach ($standardColumns as $column) {
            /**
             * @var VTI_Pgrid_Model_Column $column
             */
            if (!$column->isVisible()) {
                continue;
            }
            switch ($column->getCode()) {
                case 'entity_id':
                    $this->addColumn('entity_id',
                        array(
                            'header' => $this->_getHelper()->__($column->getTitle()),
                            'width'  => '50px',
                            'type'   => 'number',
                            'index'  => 'entity_id',
                        ));
                    break;
                case 'name':
                    $this->addColumn('name',
                        array(
                            'header' => $this->_getHelper()->__($column->getTitle()),
                            'index'  => 'name',
                        ));

                    $store = $this->_getStore();
                    if ($store->getId()) {
                        $this->addColumn('custom_name',
                            array(
                                'header' => $this->_getHelper()->__('%s in %s', $column->getTitle(), $store->getName()),
                                'index'  => 'custom_name',
                            ));
                    }
                    break;
                case 'second_name':
                    $this->addColumn('second_name',
                        array(
                            'header' => $this->_getHelper()->__($column->getTitle()),
                            'index'  => 'second_name',
                        ));
                    break;
                case 'type':
                    $this->addColumn('type',
                        array(
                            'header'  => $this->_getHelper()->__($column->getTitle()),
                            'width'   => '60px',
                            'index'   => 'type_id',
                            'type'    => 'options',
                            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
                        ));
                    break;
                case 'set_name':
                    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                        ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                        ->load()
                        ->toOptionHash();

                    $this->addColumn('set_name',
                        array(
                            'header'  => $this->_getHelper()->__($column->getTitle()),
                            'width'   => '100px',
                            'index'   => 'attribute_set_id',
                            'type'    => 'options',
                            'options' => $sets,
                        ));
                    break;
                case 'sku':
                    $this->addColumn('sku',
                        array(
                            'header' => $this->_getHelper()->__($column->getTitle()),
                            'width'  => '80px',
                            'index'  => 'sku',
                        ));
                    break;

                case 'price':
                    $store = $this->_getStore();
                    $this->addColumn('price',
                        array(
                            'header'        => $this->_getHelper()->__($column->getTitle()),
                            'type'          => 'price',
                            'currency_code' => $store->getBaseCurrency()->getCode(),
                            'index'         => 'price',
                        ));
                    break;
                case 'qty':
                    if ($this->_getHelper()->isModuleEnabled('Mage_CatalogInventory')) {
                        $this->addColumn('qty',
                            array(
                                'header' => $this->_getHelper()->__($column->getTitle()),
                                'width'  => '100px',
                                'type'   => 'number',
                                'index'  => 'qty',
                            ));
                    }
                    break;
                case 'visibility':
                    $this->addColumn('visibility',
                        array(
                            'header'  => $this->_getHelper()->__($column->getTitle()),
                            'width'   => '70px',
                            'index'   => 'visibility',
                            'type'    => 'options',
                            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
                        ));
                    break;
                case 'status':
                    $this->addColumn('status',
                        array(
                            'header'  => $this->_getHelper()->__($column->getTitle()),
                            'width'   => '70px',
                            'index'   => 'status',
                            'type'    => 'options',
                            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
                        ));
                    break;

                case 'websites':
                    if (!Mage::app()->isSingleStoreMode()) {
                        $this->addColumn('websites',
                            array(
                                'header'   => $this->_getHelper()->__($column->getTitle()),
                                'width'    => '100px',
                                'sortable' => false,
                                'index'    => 'websites',
                                'type'     => 'options',
                                'options'  => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                            ));
                    }
                    break;
                case 'action':
                    $this->addColumn('action',
                        array(
                            'header'   => $this->_getHelper()->__($column->getTitle()),
                            'width'    => '50px',
                            'type'     => 'action',
                            'getter'   => 'getId',
                            'actions'  => array(
                                array(
                                    'caption' => $this->_getHelper()->__($column->getTitle()),
                                    'url'     => array(
                                        'base'   => '*/*/edit',
                                        'params' => array('store' => $this->getRequest()->getParam('store'))
                                    ),
                                    'field'   => 'id'
                                )
                            ),
                            'filter'   => false,
                            'sortable' => false,
                            'index'    => 'stores',
                        ));
                    break;
                case 'season':
                    $entityType = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
                    $seasonattribute = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(), 'season');
                    $seasonoptionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($seasonattribute->getId());
                    $seasonoptionCollection->getSelect()->joinLeft(
                        array('optionVals' => $seasonoptionCollection->getTable('eav/attribute_option_value')),
                        'optionVals.option_id = main_table.option_id and optionVals.store_id = 0',
                        array('value')
                    );
                    $seasonoptionArray = array();
                    foreach ($seasonoptionCollection->toOptionArray() as &$option) {
                        $seasonoptionArray[$option['value']] = $option['label'];
                    }
                    $this->addColumn('season', array(
                        'header'  => $this->_getHelper()->__($column->getTitle()),
                        'index'   => 'season',
                        'type'    => 'options',
                        'options' => $seasonoptionArray
                    ));
                    break;
                case 'product_type':
                    $entityType = Mage::getSingleton('eav/config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
                    $typeattribute = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(), 'type');
                    $typeoptionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                        ->setAttributeFilter($typeattribute->getId());
                    $typeoptionCollection->getSelect()->joinLeft(
                        array('optionVals' => $typeoptionCollection->getTable('eav/attribute_option_value')),
                        'optionVals.option_id = main_table.option_id and optionVals.store_id = 0',
                        array('value')
                    );
                    $typeoptionArray = array();
                    foreach ($typeoptionCollection->toOptionArray() as &$option) {
                        $typeoptionArray[$option['value']] = $option['label'];
                    }
                    $this->addColumn('product_type', array(
                        'header'  => $this->_getHelper()->__($column->getTitle()),
                        'index'   => 'type',
                        'type'    => 'options',
                        'options' => $typeoptionArray
                    ));
                    break;
            }
        }

        return $this;
    }

    /**
     * @param string $columnId
     * @param $column
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    public function addColumn($columnId, $column)
    {
        if (isset($column['sortable']) && !isset($column['renderer']) && $column['sortable'] === FALSE) {
            if (isset($column['type']) && $column['type'] == 'action') {
                $column['renderer'] = 'impgrid/adminhtml_catalog_product_grid_renderer_action';
            } else if (isset($column['options'])) {
                $column['renderer'] = 'impgrid/adminhtml_catalog_product_grid_renderer_options';
            }
        }

        return parent::addColumn($columnId, $column);
    }
}
