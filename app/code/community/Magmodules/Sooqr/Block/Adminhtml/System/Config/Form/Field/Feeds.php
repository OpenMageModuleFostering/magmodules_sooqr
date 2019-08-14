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

class Magmodules_Sooqr_Block_Adminhtml_System_Config_Form_Field_Feeds  extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) 
    {
		$store_ids = Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled'); 		
		$html_feedlinks = '';
		foreach($store_ids as $storeId) {
			$generate_url = $this->getUrl('*/sooqr/generateFeed/store_id/' . $storeId);
			$download_url = $this->getUrl('*/sooqr/download/store_id/' . $storeId);
			$feed_text = Mage::getStoreConfig('sooqr_connect/generate/feed_result', $storeId);
			if(empty($feed_text)) {
				$feed_text = Mage::helper('sooqr')->__('No active feed found');	
				$download_url = '';
			}
			$store_title = Mage::app()->getStore($storeId)->getName();
			$store_code = Mage::app()->getStore($storeId)->getCode();
			$html_feedlinks .= '<tr><td valign="top">' . $store_title . '<br/><small>Code: ' . $store_code . '</small></td><td>' . $feed_text . '</td><td><a href="' . $generate_url . '">' . Mage::helper('sooqr')->__('Generate New') . '</a><br/><a href="' . $download_url . '">' . Mage::helper('sooqr')->__('Download Last') . '</a></td></tr>';
		}							
		if(empty($html_feedlinks)) {
			$html_feedlinks = Mage::helper('sooqr')->__('No enabled feed(s) found');
		} else {
			$html_header = '<div class="grid"><table cellpadding="0" cellspacing="0" class="border" style="width: 100%"><tbody><tr class="headings"><th>' . Mage::helper('sooqr')->__('Storeview') . '</th><th>' . Mage::helper('sooqr')->__('Feed') . '</th><th>' . Mage::helper('sooqr')->__('Action') . '</th></tr>';
			$html_footer = '</tbody></table></div>';
			$html_feedlinks = $html_header . $html_feedlinks . $html_footer;			
		}
        return sprintf('<tr id="row_%s"><td colspan="6" class="label" style="margin-bottom: 10px;">%s</td></tr>', $element->getHtmlId(), $html_feedlinks);
    }
    
}
