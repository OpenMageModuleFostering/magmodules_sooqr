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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Images {

	public function toOptionArray() 
	{
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('frontend_input', 'media_image');
		$type = array();
        foreach($attributes as $attribute) {
			if($attribute->getData('attribute_code') == 'small_image') {
				$type[] = array('value' => $attribute->getData('attribute_code'), 'label'=> str_replace("'", "", $attribute->getData('frontend_label') . ' ' . Mage::helper('sooqr')->__('(recommended)')));
			} else {
				$type[] = array('value' => $attribute->getData('attribute_code'), 'label'=> str_replace("'", "", $attribute->getData('frontend_label')));
			}				
		}
		return $type;		
	}
	
}