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
		$count = count(Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled')); 		
        $cronExprString = '';
		
		if($count > 0) {
			switch($frequency) {		
				case 'custom_expr':		 
					$cronExprString = $this->getData('groups/generate/fields/custom_cron/value');
					break;	
				case 0:		
					$hours = array();
					for($i = 0; $i < $count; $i++) {					
						$hours[] = $i;
					}
					$cronExprArray = array('40', implode(',', $hours), '*', '*', '*');
					break;
				case 6:		 
					$cronExprArray = array('40', '*/6', '*', '*', '*');
					break;
				case 4:		 
					$cronExprArray = array('40', '*/4', '*', '*', '*');
					break;
				case 2:		 
					$cronExprArray = array('40', '*/2', '*', '*', '*');
					break;
				case 1:		 
					$cronExprArray = array('40', '*', '*', '*', '*');
					break;
				case 30:		 
					$cronExprArray = array('10,40', '*', '*', '*', '*');
					break;
				case 15:		 
					$cronExprArray = array('0,15,30,45', '*', '*', '*', '*');
					break;
			}
		}
				
		if(!empty($cronExprArray)) {
			$cronExprString = join(' ', $cronExprArray);
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