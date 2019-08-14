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
 
class Magmodules_Sooqr_Block_Search extends Mage_Core_Block_Template {

	public function isEnabled() 
	{
		$enabled = Mage::getStoreConfig('sooqr_connect/general/enabled');
		$frontend_enabled = Mage::getStoreConfig('sooqr_connect/general/frontend_enabled');
		$account_id = Mage::getStoreConfig('sooqr_connect/general/account_id');
		$api_key = Mage::getStoreConfig('sooqr_connect/general/api_key');
		if($enabled && $frontend_enabled && (!empty($account_id)) && (!empty($api_key))) {
			return true;       
		}
	}

    public function getSooqrOptions()
    {
        $account_id = Mage::getStoreConfig('sooqr_connect/general/account_id');        
        $options = array('account' => $account_id, 'fieldId' => 'search');
        $parent = Mage::getStoreConfig('sooqr_connect/general/parent');        
		if(!empty($parent)) {
            $options['containerParent'] = $parent;
        }    
        $version = Mage::getStoreConfig('sooqr_connect/general/frontend_version');        
		if(!empty($version)) {
            $options['version'] = $version;
        }
        return $options;
    }
    
    public function getSooqrLanguage()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }    
    
    public function getSooqrJavascript() {
        $custom_js = Mage::getStoreConfig('sooqr_connect/general/custom_js');        
    	if(!empty($custom_js)) {
    		return $custom_js;
    	}
    }

    public function isTrackingEnabled()
    {
        if(Mage::getStoreConfig('sooqr_connect/general/statistics')) {
			return true;
		}	
    }

    public function getSooqrScriptUri()
    {
        if(Mage::getStoreConfig('sooqr_connect/general/staging')) {
            return 'static.staging.sooqr.com/sooqr.js';
        }
        return 'static.sooqr.com/sooqr.js';
    }
        
}