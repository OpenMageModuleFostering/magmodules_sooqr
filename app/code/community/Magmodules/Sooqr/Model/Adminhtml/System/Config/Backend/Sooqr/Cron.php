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

    const CRON_MODEL_PATH = 'sooqr_connect/generate/cron_schedule';
    const CRON_STRING_PATH = 'crontab/jobs/sooqr_generate/schedule/cron_expr';
    const CRON_RUNMODEL_PATH = 'crontab/jobs/sooqr_generate/run/model';

    protected function _afterSave() 
    {
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/fields/frequency/value');
		$store_ids = Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled'); 		
		$count = count($store_ids);
		if($count > 0) {
			$minute[0] = 0;
			$n = floor(60/$count);
			if($n == 60) { $n = 0; }		
			for($i = 1; $i < $count; $i++) {		
				$min = ($minute[0] + ($i * $n));
				if($min >= 60) {
					$min = ($minute[0] - ($i * $n));
				}
				$minute[] = $min;
			}
			asort($minute);
			$minute = implode(',', $minute);
			switch($frequency) {		
				case 0:
					$cronExprArray = array($minute, intval($time[0]), '*', '*', '*');
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
            Mage::getModel('core/config_data')
            	->load(self::CRON_MODEL_PATH, 'path')
            	->setValue($cronExprString)
            	->setPath(self::CRON_MODEL_PATH)
            	->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_RUNMODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_RUNMODEL_PATH))
                ->setPath(self::CRON_RUNMODEL_PATH)
                ->save();              	
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}