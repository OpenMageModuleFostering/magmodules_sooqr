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
 
class Magmodules_Sooqr_Adminhtml_SooqrController extends Mage_Adminhtml_Controller_Action {

	public function generateFeedAction($store_id = '') 
	{	
		if(Mage::getStoreConfig('sooqr/general/enabled')) {
			$store_id = $this->getRequest()->getParam('store_id');
			if(!empty($store_id)) {
				$time_start = microtime(true);
				$appEmulation = Mage::getSingleton('core/app_emulation');
				$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($store_id);
				if($result = Mage::getModel('sooqr/sooqr')->generateFeed($store_id, '', $time_start)) {
					$html = '<a href="' . $result['url'] . '" target="_blank">' . $result['url'] .'</a><br/><small>Date: ' . $result['date'] . ' (manual) - Products: ' . $result['qty'] . ' - Time: ' . number_format((microtime(true) - $time_start), 4) . '</small>';
					$config = new Mage_Core_Model_Config();
					$config->saveConfig('sooqr_connect/generate/feed_result', $html, 'stores', $store_id);
					Mage::app()->getCacheInstance()->cleanType('config');
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sooqr')->__('Generated feed with %s products. %s', $result['qty'], '<a  style="float:right;" href="' . $this->getUrl('*/sooqr/download/store_id/' . $store_id) . '">Download XML</a>'));
					$limit = Mage::getStoreConfig('sooqr_connect/generate/limit', $store_id);
					if($limit > 0) {
						Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('sooqr')->__('Note, in the feed generate configuration tab you have enabled the product limit of %s.', $limit));				
					}
				} else {
					$config = new Mage_Core_Model_Config();
					$config->saveConfig('sooqr_connect/generate/feed_result', '', 'stores', $store_id);
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sooqr')->__('No products found, make sure your filters are configured with existing values.'));
					Mage::app()->getCacheInstance()->cleanType('config');
				}
				$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);			
			}	
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sooqr')->__('Please enable the extension before generating the xml'));		
		}    	
        $this->_redirect('adminhtml/system_config/edit/section/sooqr_connect');
    } 

	public function downloadAction() 
	{
		$store_id = $this->getRequest()->getParam('store_id');
	 	$filepath = Mage::getModel('sooqr/sooqr')->getFileName('sooqr', $store_id, 0); 
		if(file_exists($filepath)) {
			$this->getResponse()
			->setHttpResponseCode(200)
			->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', true)
			->setHeader('Pragma','no-cache',1)
			->setHeader('Content-type', 'application/force-download')
			->setHeader('Content-Length', filesize($filepath) )
			->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($filepath) );
			$this->getResponse()->clearBody();
			$this->getResponse()->sendHeaders();
			readfile($filepath);
		}
	}
	
	public function addToFlatAction() 
	{
		$store_ids = Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled'); 				
		foreach($store_ids as $store_id) {
			$non_flat_attributes = Mage::helper('sooqr')->checkFlatCatalog(Mage::getModel("sooqr/sooqr")->getFeedAttributes($store_id, 'flatcheck')); 		
			foreach($non_flat_attributes as $key => $value) {
				$_attribute = Mage::getModel('catalog/resource_eav_attribute')->load($key)->setUsedInProductListing(1)->save();
			}
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sooqr')->__('Attributes added to Flat Catalog, please reindex Product Flat Data.'));
        $this->_redirect('adminhtml/system_config/edit/section/sooqr');
	}

	protected function _isAllowed() 
	{
        return Mage::getSingleton('admin/session')->isAllowed('admin/sooqr_connect/sooqr');
    }   
        
}