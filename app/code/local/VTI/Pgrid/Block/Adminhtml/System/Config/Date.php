<?php

/**
 * Class VTI_Pgrid_Block_Adminhtml_System_Config_Date
 */
class VTI_Pgrid_Block_Adminhtml_System_Config_Date extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $date = new Varien_Data_Form_Element_Date;
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $data = array(
            'name'    => $element->getName(),
            'html_id' => $element->getId(),
            'image'   => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => $dateFormatIso,
            'class'   => 'validate-date'
        );
        $date->setData($data);
        $date->setValue($element->getValue(), $dateFormatIso);
        $date->setForm($element->getForm());

        return $date->getElementHtml();
    }
}
