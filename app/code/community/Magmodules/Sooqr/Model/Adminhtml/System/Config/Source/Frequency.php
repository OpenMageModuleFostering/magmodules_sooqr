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

    protected static $_options;

    public function toOptionArray() 
    {
        if(!self::$_options) {
            self::$_options = array(
                array('label' => Mage::helper('adminhtml')->__('Daily'), 'value' => '0'),
                array('label' => Mage::helper('adminhtml')->__('Every 6 hours'), 'value' => '6'),
                array('label' => Mage::helper('adminhtml')->__('Every 4 hours'), 'value' => '4'),
                array('label' => Mage::helper('adminhtml')->__('Every 2 hours'), 'value' => '2'),
                array('label' => Mage::helper('adminhtml')->__('Every hour'), 'value' => '1'),
            );
        }
        return self::$_options;
    }

}