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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Pricemodel {

	public function toOptionArray() 
	{
		$type = array();
		$type[] = array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('Use default price'));
		$type[] = array('value'=>'min', 'label'=> Mage::helper('adminhtml')->__('Use minimum price'));
		$type[] = array('value'=>'max', 'label'=> Mage::helper('adminhtml')->__('Use maximum price'));
		$type[] = array('value'=>'total', 'label'=> Mage::helper('adminhtml')->__('Use total price'));
		return $type;		
	}

}