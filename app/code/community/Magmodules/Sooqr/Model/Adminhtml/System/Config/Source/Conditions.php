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
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Conditions {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=> '', 'label'=> Mage::helper('sooqr')->__(''));
		$type[] = array('value'=> 'eq', 'label'=> Mage::helper('sooqr')->__('Equal'));
		$type[] = array('value'=> 'neq', 'label'=> Mage::helper('sooqr')->__('Not equal'));
		$type[] = array('value'=> 'gt', 'label'=> Mage::helper('sooqr')->__('Greater than'));
		$type[] = array('value'=> 'gteq', 'label'=> Mage::helper('sooqr')->__('Greater than or equal to'));
		$type[] = array('value'=> 'lt', 'label'=> Mage::helper('sooqr')->__('Less than'));
		$type[] = array('value'=> 'lteg', 'label'=> Mage::helper('sooqr')->__('Less than or equal to'));
		$type[] = array('value'=> 'in', 'label'=> Mage::helper('sooqr')->__('In'));
		$type[] = array('value'=> 'nin', 'label'=> Mage::helper('sooqr')->__('Not in'));
		$type[] = array('value'=> 'like', 'label'=> Mage::helper('sooqr')->__('Like'));
		$type[] = array('value'=> 'empty', 'label'=> Mage::helper('sooqr')->__('Empty'));
		$type[] = array('value'=> 'not-empty', 'label'=> Mage::helper('sooqr')->__('Not Empty'));		
		return $type;		
	}
	
}