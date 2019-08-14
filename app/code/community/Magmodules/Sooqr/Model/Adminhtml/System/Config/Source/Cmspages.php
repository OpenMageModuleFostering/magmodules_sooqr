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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Cmspages {

	public function toOptionArray()
	{
		$store_id = '';
		$cms = array();
		$code = Mage::app()->getRequest()->getParam('store');
		if(!empty($code)) { 
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        } else {
        	$code = Mage::app()->getRequest()->getParam('website');
        	if(!empty($code)) {
	            $website_id = Mage::getModel('core/website')->load($code)->getId();
    	        $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();	
        	}
        }
		if($store_id) {
			$cmspages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($store_id);
		} else {
			$cmspages = Mage::getModel('cms/page')->getCollection(); 		
		}
		foreach($cmspages as $page) {
			$cms[] = array('value' => $page->getId(), 'label' => $page->getTitle() . ' (' . $page->getIdentifier() . ')');
		}
        return $cms;
    }
    
}