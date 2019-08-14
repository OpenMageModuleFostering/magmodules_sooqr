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
 
class Magmodules_Sooqr_Model_Adminhtml_System_Config_Source_Cacheresize {

	public function toOptionArray() 
	{
		$store_id =  Mage::helper('sooqr')->getStoreIdConfig();
		$source = Mage::getStoreConfig('sooqr_connect/products/image_source', $store_id);
		$options = array();
		if($source) {
			$dir = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . 'cache' . DS . $store_id . DS . $source . DS;
			if(file_exists($dir)) {
				$dirs = array_filter(glob($dir . '*'), 'is_dir');
				if(count($dirs)) {
					foreach($dirs as $img_option) {
						$img_option = str_replace($dir, '', $img_option); 
						if(strlen($img_option) < 8) {
							$options[] = array('value'=> $img_option, 'label'=> $img_option);
						}	
					}
				}
			}
		}
		if(empty($options)) {
			$options[] = array('value'=> '', 'label'=>  Mage::helper('adminhtml')->__('No cached sizes found'));
		}
		return $options;		
	}
	
}