<?php

/**
 * Class VTI_Pgrid_Model_System_Config_Source_Records
 */
class VTI_Pgrid_Model_System_Config_Source_Records
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => '20', 'label' => Mage::helper('impgrid')->__('20')),
            array('value' => '30', 'label' => Mage::helper('impgrid')->__('30')),
            array('value' => '50', 'label' => Mage::helper('impgrid')->__('50')),
            array('value' => '100', 'label' => Mage::helper('impgrid')->__('100')),
            array('value' => '200', 'label' => Mage::helper('impgrid')->__('200')),
        );
        return $options;
    }
}