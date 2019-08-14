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
 
class Magmodules_Sooqr_Model_Sooqr extends Magmodules_Sooqr_Model_Common {
	
	public function generateFeed($store_id, $limit = '', $time_start) 
	{
        $limit = $this->setMemoryLimit($store_id);
        $config = $this->getFeedConfig($store_id);	
		$products = $this->getProducts($config, $config['limit']);	
		$prices = Mage::helper('sooqr')->getTypePrices($config, $products);
		if($feed = $this->getFeedData($products, $config, $time_start, $prices)) {
			return $this->saveFeed($feed, $config, 'sooqr', count($feed['products']));
		}	
	}

	public function getFeedData($products, $config, $time_start, $prices) 
	{		
		foreach($products as $product) {
			$parent_id = Mage::helper('sooqr')->getParentData($product, $config);
			if($parent_id > 0) { $parent = $products->getItemById($parent_id); } else { $parent = ''; }
			if($product_data = Mage::helper('sooqr')->getProductDataRow($product, $config, $parent)) {
				$product_row['content_type'] = 'product';
				foreach($product_data as $key => $value) {
					if((!is_array($value)) && (!empty($value) || is_numeric($value))) { $product_row[$key] = $value; }	
				}
				if($extra_data = $this->getExtraDataFields($product_data, $config, $product, $prices)) {
					$product_row = array_merge($product_row, $extra_data);
				}
				$feed['products'][] = $product_row;				
				unset($product_row);
			}
		}	
		if(!empty($feed)) {
			$return_feed = array();
			$return_feed['config'] = $this->getFeedHeader($config, count($feed['products']), $time_start);
			if($config['cms_pages']) {
				$return_feed['products'] = array_merge($feed['products'], $this->getCmspages($config));
			} else {
				$return_feed['products'] = $feed['products'];					
			}
			return $return_feed;
		}
	}
	
	public function getFeedConfig($storeId, $type = 'xml')
	{
		
		$config							= array();
		$feed 							= Mage::helper('sooqr'); 
        $filename 						= $this->getFileName('sooqr', $storeId);
		$websiteId 						= Mage::app()->getStore($storeId)->getWebsiteId();

		// DEFAULTS
		$config['store_id'] 			= $storeId;
		$config['website_name']			= $feed->cleanData(Mage::getModel('core/website')->load($websiteId)->getName(), 'striptags');
		$config['website_url']			= Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$config['media_url']			= Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$config['media_image_url']		= $config['media_url'] . 'catalog' . DS . 'product';
		$config['media_gallery_id'] 	= Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', 'media_gallery');
		$config['image_source']			= Mage::getStoreConfig('sooqr_connect/products/image_source', $storeId);
		$config['image_resize']			= Mage::getStoreConfig('sooqr_connect/products/image_resize', $storeId);
		$config['file_name']			= $filename;
		$config['limit']	 			= Mage::getStoreConfig('sooqr_connect/generate/limit', $storeId);
		$config['version']				= (string)Mage::getConfig()->getNode()->modules->Magmodules_Sooqr->version;
		$config['filter_enabled']		= Mage::getStoreConfig('sooqr_connect/products/category_enabled', $storeId);
		$config['filter_cat']			= Mage::getStoreConfig('sooqr_connect/products/categories', $storeId);		
		$config['filter_type']			= Mage::getStoreConfig('sooqr_connect/products/category_type', $storeId);
		$config['cms_pages']			= Mage::getStoreConfig('sooqr_connect/products/cms_pages', $storeId);
		$config['cms_include']			= Mage::getStoreConfig('sooqr_connect/products/cms_include', $storeId);
		$config['filters']				= @unserialize(Mage::getStoreConfig('sooqr_connect/products/advanced', $storeId));	
		$config['product_url_suffix']   = $feed->getProductUrlSuffix($storeId);
		$config['stock_manage']			= Mage::getStoreConfig('cataloginventory/item_options/manage_stock');
		$config['backorders']			= Mage::getStoreConfig('cataloginventory/item_options/backorders');

		// PRICE
		$config['price_scope']			= Mage::getStoreConfig('catalog/price/scope');
		$config['price_add_tax']	 	= Mage::getStoreConfig('sooqr_connect/products/add_tax', $storeId);
		$config['price_add_tax_perc']	= Mage::getStoreConfig('sooqr_connect/products/tax_percentage', $storeId);
		$config['price_grouped']		= Mage::getStoreConfig('sooqr_connect/products/grouped_price', $storeId);
		$config['force_tax']	 		= Mage::getStoreConfig('sooqr_connect/products/force_tax', $storeId);
		$config['price_rules']	 		= true;
		$config['currency'] 			= Mage::app()->getStore($storeId)->getCurrentCurrencyCode(); 
		$config['currency_allow'] 		= Mage::getStoreConfig('currency/options/allow', $storeId);							
		$config['hide_currency'] 		= true;
		$config['base_currency_code'] 	= Mage::app()->getStore($storeId)->getBaseCurrencyCode();
		$config['currency_data']		= $this->getCurrencies($storeId, $config['base_currency_code']);	
		$config['conf_enabled']			= Mage::getStoreConfig('sooqr_connect/products/conf_enabled', $storeId);	
		$config['markup']				= $feed->getPriceMarkup($config);
		$config['use_tax']				= $feed->getTaxUsage($config);
		
		// FIELD & CATEGORY DATA
		$config['field']				= $this->getFeedAttributes($storeId, $type, $config);
		$config['category_data']		= $feed->getCategoryData($config, $storeId);
		
		if($config['image_resize'] == 'fixed') {
			$config['image_size'] = Mage::getStoreConfig('sooqr_connect/products/image_size_fixed', $storeId);		
		} else {
			$config['image_size'] = Mage::getStoreConfig('sooqr_connect/products/image_size_custom', $storeId);		
		}
		
		return $config;	
	}

	public function getFeedAttributes($storeId = 0, $type = 'xml', $config = '')
	{
		$attributes = array();
		$attributes['id']			= array('label' => 'id', 'source' => Mage::getStoreConfig('sooqr_connect/products/id_attribute', $storeId));
		$attributes['name']			= array('label' => 'title', 'source' => Mage::getStoreConfig('sooqr_connect/products/name_attribute', $storeId));
		$attributes['sku']			= array('label' => 'sku', 'source' => Mage::getStoreConfig('sooqr_connect/products/sku_attribute', $storeId));
		$attributes['description']	= array('label' => 'description', 'source' => Mage::getStoreConfig('sooqr_connect/products/description_attribute', $storeId), 'action' => 'striptags');
		$attributes['brand']		= array('label' => 'brand', 'source' => Mage::getStoreConfig('sooqr_connect/products/brand_attribute', $storeId));
		$attributes['product_url']	= array('label' => 'url', 'source' => '');
		$attributes['image_link']	= array('label' => 'image_link', 'source' => Mage::getStoreConfig('sooqr_connect/products/image_source', $storeId));		
		$attributes['price']		= array('label' => 'price', 'source' => '');		
		$attributes['parent_id']	= array('label' => 'assoc_id', 'source' =>'entity_id', 'parent' => 1);				
		$attributes['qty']			= array('label' => 'stock', 'source' => 'qty', 'action' => 'round');				
		$attributes['stock_status']	= array('label' => 'stock_status', 'source' => 'stock_status');				
		$attributes['type']			= array('label' => 'product_object_type', 'source' => 'type_id');
		$attributes['visibility']	= array('label' => 'visibility', 'source' => 'visibility');
		$attributes['status']		= array('label' => 'status', 'source' => 'status');
		$attributes['categories']	= array('label' => 'categories', 'source' => '', 'parent' => 1);				
		if($extra_fields = @unserialize(Mage::getStoreConfig('sooqr_connect/products/extra', $storeId))) {
			foreach($extra_fields as $extra_field) {
				$attributes[$extra_field['attribute']] = array('label' => $extra_field['attribute'], 'source' => $extra_field['attribute'], 'action' => 'striptags');		
			}
		}
		if($type == 'flatcheck') {
			if($filters = @unserialize(Mage::getStoreConfig('sooqr_connect/products/advanced', $storeId))) {
				foreach($filters as $filter) {
					$attributes[$filter['attribute']] = array('label' => $filter['attribute'], 'source' => $filter['attribute']);
				}
			}
		}
		return Mage::helper('sooqr')->addAttributeData($attributes, $config);	
	}
	
	public function getFileName($type, $storeId, $refresh = 1) 
	{
        if(!$fileName = Mage::getStoreConfig('sooqr_connect/generate/filename', $storeId)) {
			$fileName = $type . '.xml';
		}
		
		if(substr($fileName, -3) != 'xml') {
			$fileName = $fileName . '-' . $storeId. '.xml';
		} else {
			$fileName = substr($fileName, 0, -4) . '-' . $storeId. '.xml';			
		}

        if(!file_exists(Mage::getBaseDir('media') . DS . $type)) {
        	mkdir(Mage::getBaseDir('media') . DS . $type);
        }
        
        return Mage::getBaseDir() . DS . 'media' . DS . $type . DS . $fileName;
    }

    public function saveFeed($feed, $config, $type, $count) 
    {	
		$encoding = Mage::getStoreConfig('design/head/default_charset');
		$xml_data = new SimpleXMLElement("<rss xmlns:sqr=\"http://base.sooqr.com/ns/1.0\" version=\"2.0\" encoding=\"" . $encoding . "\"></rss>");
		$this->getArray2Xml($feed, $xml_data);
		$dom = dom_import_simplexml($xml_data)->ownerDocument;
		$dom->encoding = $encoding;
		$dom->formatOutput = true;
		$xml_feed = $dom->saveXML();
        if (!file_put_contents($config['file_name'], $xml_feed)) {       
  			Mage::getSingleton('adminhtml/session')->addError(Mage::helper($type)->__('File writing not succeeded'));
        } else {
        	$filename = $config['file_name'];
        	$store_id = $config['store_id'];
	        $local_path = Mage::getBaseDir() . DS . 'media' . DS . $type . DS;
        	$filename = str_replace($local_path, '', $filename);
        	$websiteUrl = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        	$feed_url = $websiteUrl . $type . DS . $filename;			
			$result = array();
			$result['url'] = $feed_url;
			$result['shop'] = Mage::app()->getStore($store_id)->getCode();
			$result['date'] = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
			$result['qty'] = $count;
        	return $result;
        }
	}  
	   
	protected function getPrices($data, $currency, $config, $conf_prices, $product) 
	{			
		$prices = array();
		$id = $product->getEntityId();	
		$prices['currency'] = $currency;
		if(!empty($conf_prices[$id])) {
			$conf_price = Mage::helper('tax')->getPrice($product, $conf_prices[$id], true);
			$conf_price_reg = Mage::helper('tax')->getPrice($product, $conf_prices[$id . '_reg'], true);
			if($conf_price_reg > $conf_price) {
				$prices['price'] = $conf_price;
				$prices['normal_price'] = $conf_price_reg;
			} else {
				$prices['price'] = $conf_price;
			}
		} else {
			if(!empty($config['currency_data'])) {
				foreach($config['currency_data'] as $key => $value) {		
					if($currency == $key) {
						if(isset($data['sales_price'])) {
							$prices['normal_price'] = $data['regular_price'];
							$prices['price'] = $data['sales_price'];
						} else {
							$prices['price'] = $data['price'];
						}
					} else {
						if(isset($data['sales_price'])) {
							$prices['normal_price_' . strtolower($key)] = round(($data['regular_price'] * $value), 2);
							$prices['price_' . strtolower($key)] = round(($data['sales_price'] * $value), 2);
						} else {
							$prices['price_' . strtolower($key)] = round(($data['price'] * $value), 2);
						}			
					}
				}	
			} else {
				if(isset($data['sales_price'])) {
					$prices['normal_price'] = $data['regular_price'];
					$prices['price'] = $data['sales_price'];
				} else {
					$prices['price'] = $data['price'];
				} 		
			}		
		}
		return $prices;
	}

	protected function getAssocId($data) 
	{			
		$assoc_id = array();
		if(empty($data['assoc_id'])) {
			$assoc_id['assoc_id'] = $data['id'];
		}	
		if($data['product_object_type'] != 'simple') {
			$assoc_id['is_parent'] = '1';
		} else {
			$assoc_id['is_parent'] = '0';		
		}
		return $assoc_id;		
	}

	protected function getStockData($product_data, $config, $product) 
	{				
		$stock_data = array();
		if($product->getUseConfigManageStock()) {
			$stock_data['manage_stock'] = (string)$config['stock_manage'];
		} else {
			$stock_data['manage_stock'] = (string)$product->getManageStock();			
		}

		if($product->getUseConfigBackorders()) {
			$stock_data['backorders'] = (string)$config['backorders'];
		} else {
			$stock_data['backorders'] = (string)$product->getBackorders();			
		}
		return $stock_data;		
	}
	
	
	protected function getCategoryData($product_data, $config) 
	{			
		$category = array(); 
		if(!empty($product_data['categories'])) {
			foreach($product_data['categories'] as $cat) {
				if(!empty($cat['path'])) {
					$i = 0;
					foreach($cat['path'] as $catpath) {					
						$category[$i][] = $catpath;
						$i++;
					}		
				}		
			}
		}
		$category_array = array(); $i = 0;
		if(!empty($category)) {
			foreach($category as $cat) {
				$category_array['category' . $i] = array_unique($cat);
				$i++;
			}
		}
		return $category_array;		
	}
			
	protected function getExtraDataFields($product_data, $config, $product, $prices) 
	{
		$_extra = array();
		if($_category_data = $this->getCategoryData($product_data, $config)) {
			$_extra = array_merge($_extra, $_category_data);
		}
		if($_prices = $this->getPrices($product_data['price'], $config['currency'], $config, $prices, $product)) {
			$_extra = array_merge($_extra, $_prices);
		}
		if($_assoc_id = $this->getAssocId($product_data)) {
			$_extra = array_merge($_extra, $_assoc_id);
		}			
		if($_stock_data = $this->getStockData($product_data, $config, $product)) {
			$_extra = array_merge($_extra, $_stock_data);
		}			

		return $_extra;
	}	

	protected function getFeedHeader($config, $count, $time_start) 
	{
		$header = array();
		$header['system'] = 'Magento';
		$header['extension'] = 'Magmodules_Sooqr';
		$header['extension_version'] = $config['version'];
		$header['store'] = $config['website_name'];
		$header['url'] = $config['website_url'];
		$header['products_total'] = $count;
		$header['products_limit'] = $config['limit'];
		$header['date_created'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
		$header['processing_time'] = number_format((microtime(true) - $time_start), 4);
		return $header;
	}

	public function getInstallation() 
	{
		$json = array();
		$json['search']['enabled'] = '0';
		$store_ids = Mage::helper('sooqr')->getStoreIds('sooqr_connect/generate/enabled'); 		
		foreach($store_ids as $store_id) {
        	$media_url = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
			if(!$file_name = Mage::getStoreConfig('sooqr_connect/generate/filename', $store_id)) {
				$file_name = 'soorq.xml';
			}
			if(substr($file_name, -3) != 'xml') {
				$file_name = $file_name . '-' . $store_id. '.xml';
			} else {
				$file_name = substr($file_name, 0, -4) . '-' . $store_id. '.xml';			
			}
			$name = Mage::app()->getStore($store_id)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
			$name = str_replace(array('https://','http://','www'), '', $name);
				
			$json['feeds'][$store_id]['name'] 				= $name;	
			$json['feeds'][$store_id]['feed_url'] 			= $media_url . DS . 'sooqr' . DS . $file_name;	
			$json['feeds'][$store_id]['currency'] 			= Mage::app()->getStore($store_id)->getBaseCurrencyCode();
			$json['feeds'][$store_id]['locale'] 			= Mage::getStoreConfig('general/locale/code', $store_id) ;
			$json['feeds'][$store_id]['country'] 			= Mage::getStoreConfig('general/country/default', $store_id);
			$json['feeds'][$store_id]['timezone'] 			= Mage::getStoreConfig('general/locale/timezone', $store_id);
			$json['feeds'][$store_id]['system'] 			= Mage::getStoreConfig('general/locale/timezone', $store_id);
			$json['feeds'][$store_id]['extension'] 			= 'Magmodules_Sooqr';
			$json['feeds'][$store_id]['extension_version'] 	= (string)Mage::getConfig()->getNode()->modules->Magmodules_Sooqr->version;
		}
		return $json;
	}	

	public function getCmspages($config) 
	{
		$cmspages = array();
		if($config['cms_pages'] == 1) {
			$pages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($config['store_id'])->addFieldToFilter('is_active', 1)->addFieldToFilter('identifier', array(array('nin' => array('no-route','enable-cookies'))));
		} else {
			$cms_include = explode(',', $config['cms_include']);
			$pages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($config['store_id'])->addFieldToFilter('is_active', 1)->addFieldToFilter('page_id', array('in' => $cms_include));		
		}
		foreach($pages as $page) {
			$cmspages[] = array('content_type' => 'cms', 'id' => 'CMS-' . $page->getId(), 'title' => $page->getTitle(), 'content' => Mage::helper('sooqr')->cleanData($page->getContent(), 'striptags'), 'url' => $config['website_url'] . $page->getIdentifier());
		}
		return $cmspages;		
	}

	function getArray2Xml($array, &$xml_user_info) 
	{
		foreach($array as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)) {
					if(substr($key,0,8) == 'category') {
						$key = 'sqr:' . $key;
						$subnode = $xml_user_info->addChild("$key", "", "http://base.sooqr.com/ns/1.0");
						$this->getArray2Xml($value, $subnode);
					} else {					
						$subnode = $xml_user_info->addChild("$key");
						$this->getArray2Xml($value, $subnode);
					}
				} else{
					$subnode = $xml_user_info->addChild("item");
					$this->getArray2Xml($value, $subnode);
				}
			} else {
				if(is_numeric($key)) {
					$xml_user_info->addChild("node", htmlspecialchars("$value"), "http://base.sooqr.com/ns/1.0");		
				} else {
					$xml_user_info->addChild("$key", htmlspecialchars("$value"), "http://base.sooqr.com/ns/1.0");
				}	
			}
		}
	}  			

	function getCurrencies($storeId, $base_currency) 
	{
		$allow = explode(',', Mage::getStoreConfig('currency/options/allow', $storeId));
		$rates = Mage::getModel('directory/currency')->getCurrencyRates($base_currency, array_values($allow));
		return $rates;
	}	

	protected function setMemoryLimit($storeId)
	{
		if(Mage::getStoreConfig('sooqr_connect/generate/overwrite', $storeId)) {
			if($memory_limit = Mage::getStoreConfig('sooqr_connect/generate/memory_limit', $storeId)) {
				ini_set('memory_limit', $memory_limit);
			}		
			if($max_execution_time = Mage::getStoreConfig('sooqr_connect/generate/max_execution_time', $storeId)) {
				ini_set('max_execution_time', $max_execution_time);
			}		
		}	
	}
	
}