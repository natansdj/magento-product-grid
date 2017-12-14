<?php


class VTI_Pgrid_Model_Columngroup extends Mage_Core_Model_Abstract
{

    public function __construct()
    {
        $this->_init('impgrid/columngroup', 'entity_id');
    }

    /**
     * @return bool
     */
    public function isSelected()
    {
        $selectedGroupId = Mage::helper('impgrid')->getSelectedGroupId();
        return $this->getId() == $selectedGroupId;
    }

    /**
     * @return bool
     */
    public function categoryColumnEnabled()
    {
        $additionalColumns = $this->getAdditionalColumns();
        $additionalColumns = explode(',', $additionalColumns);

        return in_array($this->getCategoriesKey(), $additionalColumns);
    }

    /**
     * Flag for check categories
     * @return string
     */
    public function getCategoriesKey()
    {
        return 'category';
    }
}
