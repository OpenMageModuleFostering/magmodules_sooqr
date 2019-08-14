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

class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Frequency {

    public function toOptionArray() 
    {
		$frequency = array();
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Daily'), 'value' => '0');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every 6 hours'), 'value' => '6');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every 4 hours'), 'value' => '4');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every 2 hours'), 'value' => '2');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every hour'), 'value' => '1');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every 30 minutes'), 'value' => '30');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Every 15 minutes'), 'value' => '15');
		$frequency[] = array('label' => Mage::helper('adminhtml')->__('Custom'), 'value' => 'custom_expr');
		return $frequency;
    }

}