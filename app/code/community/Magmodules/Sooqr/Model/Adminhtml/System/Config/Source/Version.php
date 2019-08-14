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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Version {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value'=>'4', 'label'=> Mage::helper('sooqr')->__('Version 4 (Responsive)'));
		$type[] = array('value'=>'3', 'label'=> Mage::helper('sooqr')->__('Version 3 (Original)'));
		return $type;		
	}

}