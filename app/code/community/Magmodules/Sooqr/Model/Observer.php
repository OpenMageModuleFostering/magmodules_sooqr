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
 
class Magmodules_Sooqr_Model_Observer {

    public function scheduledGenerateSooqr($schedule) 
    {
        $enabled = Mage::getStoreConfig('sooqr_connect/general/enabled');
    	$cron = Mage::getStoreConfig('sooqr_connect/generate/cron');
    	$next_store = Mage::getStoreConfig('sooqr_connect/generate/cron_next');
		if($enabled && $cron) {
			$storeIds = Mage::helper('sooqr')->getStoreIds('sooqr/generate/enabled'); 		
			if(empty($next_store) || ($next_store >= count($storeIds))) { 
				$next_store = 0; 
			}		
			$store_id = $storeIds[$next_store];
			$time_start = microtime(true);
			$appEmulation = Mage::getSingleton('core/app_emulation');
			$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store_id);
			if($result = Mage::getModel('sooqr/sooqr')->generateFeed($store_id)) {
				$html = '<a href="' . $result['url'] . '" target="_blank">' . $result['url'] .'</a><br/><small>Date: ' . $result['date'] . ' (cron) - Products: ' . $result['qty'] . ' - Time: ' . number_format((microtime(true) - $time_start), 4) . '</small>';
				$config = new Mage_Core_Model_Config();
				$config->saveConfig('sooqr_connect/generate/feed_result', $html, 'stores', $store_id);
			}	
			$config->saveConfig('sooqr_connect/generate/cron_next', ($next_store + 1), 'default', 0);
			Mage::app()->getCacheInstance()->cleanType('config');
			$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);			
		}   	      
    }
    
}