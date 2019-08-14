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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Condition {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value'=>'new', 'label'=> Mage::helper('sooqr')->__('New'));
		$type[] = array('value'=>'refurbished', 'label'=> Mage::helper('sooqr')->__('Refurbished'));				
		$type[] = array('value'=>'used', 'label'=> Mage::helper('sooqr')->__('Used'));				
		return $type;		
	}
	
}