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

class Magmodules_Sooqr_Model_Adminhtml_System_Config_Backend_Design_Filter extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array {    

 	protected function _beforeSave() 
 	{
        $value = $this->getValue();
        if(is_array($value)) {
            unset($value['__empty']);
            if(count($value)) { 
            	$value = $this->orderData($value, 'attribute');
				foreach($value as $key => $field){													
					if(!empty($field['attribute']) && !empty($field['condition']) &&  !empty($field['value'])) {
						$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $field['attribute']);							
						$value[$key]['attribute'] = $field['attribute'];				
						$value[$key]['condition'] = $field['condition'];				
						$value[$key]['value'] = $field['value'];				
						$value[$key]['type'] = $attribute->getFrontendInput();							
					} else {
						unset($value[$key]);
					}
				}										
            	$keys = array();
            	for($i=0; $i < count($value); $i++){
            		$keys[] = 'filter_' . uniqid();
            	}   
				$value = array_combine($keys, array_values($value));
            }
        }
        $this->setValue($value);
        parent::_beforeSave();
    }

	function orderData($data, $sort) 
	{ 
		$code = "return strnatcmp(\$a['$sort'], \$b['$sort']);"; 
		usort($data, create_function('$a,$b', $code)); 
		return $data; 
	} 
    
}