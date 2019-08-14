<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Sooqr
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Selectattribute {

	protected $_ignore = array(
		'ebizmarts_mark_visited',
		'is_recurring',
		'links_purchased_separately',
		'price_view',
		'status',
		'tax_class_id',
		'visibility',
		'sooqr_condition',
		'sooqr_exclude',
		'shipment_type',		
	);
	
    public function toOptionArray()
    {
        $options = array();
		$options[] = array('value' => '', 'label' => Mage::helper('sooqr')->__('-- none'));
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFilter('entity_type_id', $entityTypeId)->setOrder('attribute_code', 'ASC');
        foreach ($attributes as $attribute){
			if($attribute->getBackendType() == 'int') {
				if($attribute->getFrontendLabel()) {
					if(!in_array($attribute->getAttributeCode(), $this->_ignore)) {
						$options[] = array('value'=> $attribute->getAttributeCode(), 'label'=> $attribute->getFrontendLabel());				
					}
				}
			}
        }       
        return $options;
    }

}