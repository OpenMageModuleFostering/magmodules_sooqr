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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Resize {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value'=>'','label'=> Mage::helper('adminhtml')->__('No'));
		$type[] = array('value'=>'fixed','label'=> Mage::helper('adminhtml')->__('Yes, fixed value'));				
		$type[] = array('value'=>'custom','label'=> Mage::helper('adminhtml')->__('Yes, custom value'));				
		return $type;		
	}
	
}