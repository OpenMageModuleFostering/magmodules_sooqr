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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Backend_Sooqr_Cron extends Mage_Core_Model_Config_Data {

    const CRON_MODEL_PATH = 'sooqr/generate/cron_schedule';

    protected function _afterSave() 
    {
        $enabled = $this->getData('groups/generate/fields/enabled/value');
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/fields/frequency/value');
		$store_ids = Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled'); 		
		if(count($store_ids) > 0) {
			$minute = array();
			if(count($store_ids) > 10) {
				$n = 3;
			} else {
				$n = 5;
			}
			for($i = 1; $i < count($store_ids); $i++) {		
				$minute[] = ($i * $n);
			}		
			$minute = implode(',', $minute);

			switch($frequency) {		
				case 0:
					$cronExprArray = array(intval($time[1]), intval($time[0]), '*', '*', '*');
					break;
				case 6:		 
					$cronExprArray = array($minute, '*/6', '*', '*', '*');
					break;
				case 4:		 
					$cronExprArray = array($minute, '*/4', '*', '*', '*');
					break;
				case 2:		 
					$cronExprArray = array($minute, '*/2', '*', '*', '*');
					break;
				case 1:		 
					$cronExprArray = array($minute, '*', '*', '*', '*');
					break;
			}
	        $cronExprString = join(' ', $cronExprArray);
		} else {
	        $cronExprString = '';
		} 
        try {
            Mage::getModel('core/config_data')->load(self::CRON_MODEL_PATH, 'path')->setValue($cronExprString)->setPath(self::CRON_MODEL_PATH)->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}
