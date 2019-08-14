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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Configurable {

	public function toOptionArray() 
	{	
		$attributes = Mage::getModel("sooqr/sooqr")->getFeedAttributes();
		$attributes_skip = array('id','parent_id','price');
		$att = array();		
		foreach ($attributes as $key => $attribute) {
			if(!in_array($key,$attributes_skip)) {
				$att[] = array('value'=> $key, 'label'=> $key);
			}	
		}
		return $att;
	}
	
}