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
 
class Magmodules_Sooqr_Block_Adminhtml_Widget_Info_Info extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

    public function render(Varien_Data_Form_Element_Abstract $element) 
    {
		$account_id = Mage::getStoreConfig('sooqr_connect/general/account_id');
		$api_key = Mage::getStoreConfig('sooqr_connect/general/api_key');
        $magento_version = Mage::getVersion();
        $module_version = Mage::getConfig()->getNode()->modules->Magmodules_Sooqr->version;
		$logo_link = '//www.magmodules.eu/logo/sooqr/' . $module_version . '/' . $magento_version . '/logo.png';
		$base_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

		$html = '<div style="background:url(\'' . $logo_link . '\') no-repeat scroll 15px center #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 200px;">
					<h4>About Magmodules.eu</h4>
					<p>We are a Magento only E-commerce Agency located in the Netherlands and we developed this extension in association with Sooqr.<br>
                    <br />
                    <table width="500px" border="0">
						<tr>
							<td width="58%">View more extensions from us:</td>
							<td width="42%"><a href="http://www.magentocommerce.com/magento-connect/developer/Magmodules" target="_blank">Magento Connect</a></td>
						</tr>
							<td>Send us an E-mail:
							<td><a href="mailto:info@magmodules.eu">info@magmodules.eu</a></td>
						</tr>
						<tr>
							<td height="30">Visit our Website and Knowledgebase:</td>
							<td><a href="http://www.magmodules.eu/help/sooqr" target="_blank">www.magmodules.eu</a></td>
						</tr>';
			
						
		if(empty($account_id) && empty($api_key)) {					
			$html .= '	<tr>
							<td>Registration on Sooqr (and free trial):</td>
							<td><a href="https://my.sooqr.com/magtrial?base=' . $base_url . '" target="_blank">Register here</a></td>
						</tr>';

		} else {
			$html .= '  <tr>
							<td>Sooqr Conversion Suite</td>
							<td><a href="https://my.sooqr.com/user/login" target="_blank">Login here</a></td>
						</tr>';
		}				
		
		$html .= '		<tr>
							<td height="30">Sooqr Support</td>
							<td><a href="http://support.sooqr.com/support/home" target="_blank">Sooqr Support</a> or <a href="mailto:support@sooqr.com" target="_blank">support@sooqr.com</a></td>
						</tr>
					</table>
                </div>';

		$flat_product = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
		$flat_category = Mage::getStoreConfig('catalog/frontend/flat_catalog_category');
		if((!$flat_product) || (!$flat_category)) {
			$msg = '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li><span>' . Mage::helper('sooqr')->__('Please enable "Flat Catalog Category" and "Flat Catalog Product" for the extension to work properly. <a href="https://www.magmodules.eu/help/enable-flat-catalog/" target="_blank">More information.</a>') . '</span></li></ul></li></ul></div>';
			$html = $html . $msg;
		}
		
		if(Mage::getStoreConfig('catalog/frontend/flat_catalog_product')) {
			$store_id =  Mage::helper('sooqr')->getStoreIdConfig();
			$non_flat_attributes = Mage::helper('sooqr')->checkFlatCatalog(Mage::getModel("sooqr/sooqr")->getFeedAttributes($store_id, 'flatcheck')); 
			if(count($non_flat_attributes) > 0) {
				$html .= '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li><span>';
				$html .= $this->__('Warning: The following used attribute(s) were not found in the flat catalog: %s. This can result in empty data or higher resource usage. Click <a href="%s">here</a> to add these to the flat catalog. ', implode($non_flat_attributes, ', '), $this->getUrl('*/sooqr/addToFlat'));
				$html .= '</span></ul></li></ul></div>';
			}	
		}
        return $html;
    }

}