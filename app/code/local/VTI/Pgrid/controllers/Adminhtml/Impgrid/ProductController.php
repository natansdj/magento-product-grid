<?php

/**
 * Class VTI_Pgrid_Adminhtml_Impgrid_ProductController
 */
class VTI_Pgrid_Adminhtml_Impgrid_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'products.csv';
        $grid = $this->getLayout()->createBlock('adminhtml/catalog_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'products.xml';
        $grid = $this->getLayout()->createBlock('adminhtml/catalog_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
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
}