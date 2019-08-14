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
 * @version		26-03-2016
 * =============================================================
 */
 
class Magmodules_Sooqr_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getStoreIds($path) 
	{
		$store_ids = array(); 
		foreach(Mage::app()->getStores() as $store)  {
			$store_id = Mage::app()->getStore($store)->getId();
			if(Mage::getStoreConfig($path, $store_id)) {
				$store_ids[] = $store_id;			
			}
		}
		return $store_ids; 	
	}

	public function getProductDataRow($product, $config, $parent) 
	{
		$fields = $config['field'];
		$data = array();

		if(!$this->validateParent($parent, $config, $product)) { $parent = ''; }
		if(!$this->validateProduct($product, $config, $parent)) { return false; }
		
		foreach($fields as $key => $field) {
			$rows = $this->getAttributeValue($key, $product, $config, $field['action'], $parent);						
			if(is_array($rows)) {
				$data = array_merge($data, $rows);
			}	
		}
		if(empty($config['skip_validation'])) {
			if(!empty($data[$fields['price']['label']])) {
				return $data;
			} 	
		} else {
			return $data;	
		}
	}

	public function getAttributeValue($field, $product, $config, $actions = '', $parent) 
	{
		$data = $config['field'][$field];
		$product_data = $product;
		
		if(!empty($parent)) {
			if(!empty($data['parent'])) {
				$product_data = $parent;
			}
		}
		
		switch($field) {
			case 'product_url':
				$value = $this->getProductUrl($product, $config, $parent);
				break;
			case 'image_link':
				if(!empty($parent)) {
					$value = $this->getProductImage($product, $config);
					if(empty($value)) {
						$value = $this->getProductImage($parent, $config);
					}					
				} else {
					$value = $this->getProductImage($product_data, $config);				
				}										
				break;
			case 'condition':
				$value = $this->getProductCondition($product_data, $config);
				break;	
			case 'availability':
				$value = $this->getProductAvailability($product_data, $config);
				break;								
			case 'weight':
				$value = $this->getProductWeight($product_data, $config);
				break;
			case 'price':
				$value = $this->getProductPrice($product_data, $config);
				break;
			case 'bundle':
				$value = $this->getProductBundle($product_data, $config);
				break;
			case 'parent_id':
				$value = $this->getProductData($parent, $data);
				break;
			case 'categories':
				$value = $this->getProductCategories($product_data, $config);
				break;
			default:	
				if(!empty($data['source'])) {
					$value = $this->getProductData($product_data, $data, $config);
				} else {
					$value = '';
				}	
				break;
		}
		
		if((isset($actions)) && (!empty($value))) {
			$value = $this->cleanData($value, $actions);
		}
			
		if((is_array($value) && ($field == 'image_link'))) {
			$i = 1;
			foreach($value as $key => $val) {
				$data_row[$key] = $val;	
				$i++;					
			}
			return $data_row;
		}

		if(!empty($value) || is_numeric($value)) {
			$data_row[$data['label']] = $value;				
			return $data_row;
		}	
	}
	
	public function cleanData($st, $action = '') 
	{	
		if($action) {
			$actions = explode('_', $action);
			if(in_array('striptags', $actions)) {			
				$st = $this->stripTags($st);
				$st = trim($st);
			}	
			if(in_array('replacetags', $actions)) {			
				$st = str_replace(array("\r", "\n"), "", $st);
				$st = str_replace(array("<br>","<br/>", "<br />"), '\n', $st);
				$st = $this->stripTags($st);
				$st = rtrim($st);
			}
			if(in_array('replacetagsn', $actions)) {			
				$st = str_replace(array("\r", "\n"), "", $st);
				$st = str_replace(array("<br>","<br/>", "<br />"), '\\' . '\n', $st);
				$st = $this->stripTags($st);
				$st = rtrim($st);
			}
			if(in_array('rn', $actions)) {			
				$st = str_replace(array("\r", "\n"), "", $st);
			}			
			if(in_array('truncate', $actions)) {			
				$st = Mage::helper('core/string')->truncate($st, '5000');
			}	
			if(in_array('cdata', $actions)) {			
				$st = '<![CDATA[' . $st . ']]>';
			}	
			if(in_array('round', $actions)) {			
				if(!empty($actions[1])) {
					if($st > $actions[1]) {
						$st = $actions[1];
					}	
				}
				$st = round($st);
			}			
			if(in_array('boolean', $actions)) {			
				($st > 0 ? $st = 1 : $st = 0);
			}			
		}	
		return $st;
	}    	
	
	public function getProductUrl($product, $config, $parent) 
	{
		$url = '';
		if(!empty($parent)) {
			if($parent->getRequestPath()) {
				$url = Mage::helper('core')->escapeHtml(trim($config['website_url'] . $parent->getRequestPath()));			
			}			
			if(empty($url)) {
				if($parent->getUrlKey()) {
					$url = Mage::helper('core')->escapeHtml(trim($config['website_url'] . $parent->getUrlKey()));
				}
			}
		} else {
			if($product->getRequestPath()) {
				$url = Mage::helper('core')->escapeHtml(trim($config['website_url'] . $product->getRequestPath()));			
			}			
			if(empty($url)) {
				if($product->getUrlKey()) {
					$url = Mage::helper('core')->escapeHtml(trim($config['website_url'] . $product->getUrlKey()));
				}
			}
		}
		if(!empty($config['product_url_suffix'])) {
			if(strpos($url, $config['product_url_suffix']) === false) {
				$url = $url . $config['product_url_suffix'];
			}
		}
		if(!empty($parent) && !empty($config['conf_switch_urls'])) {
			if($parent->getTypeId() == 'configurable') {
				$productAttributeOptions = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);
				$url_extra = '';
				foreach ($productAttributeOptions as $productAttribute) {
					if($id = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), $productAttribute['attribute_code'], $config['store_id'])) {
						$url_extra .= $productAttribute['attribute_id'] . '=' . $id . '&';
					}
				}
				if(!empty($url_extra)) {
					$url = $url . '#' . rtrim($url_extra, '&');
				}	
			}
		}
		return $url;
	}

	public function getProductImage($product, $config) 
	{		
		$image_data = array();
		if(!empty($config['image_resize']) && !empty($config['image_size'])) { 
			$image_file = $product->getData($config['image_source']);
			if($image_file != 'no_selection') {
				$imageModel = Mage::getModel('catalog/product_image')->setSize($config['image_size'])->setDestinationSubdir($config['image_source'])->setBaseFile($image_file);
				if(!$imageModel->isCached()) {
					$imageModel->resize()->saveFile();
				}
				$productImage = $imageModel->getUrl();
				return (string)$productImage;
			} 	
		} else {		
			$image = '';		
			if(!empty($config['media_attributes'])) {
				foreach($config['media_attributes'] as $media_att) {
					if($media_att == 'base') { $media_att = 'image'; }
					$media_data = $product->getData($media_att);
					if(!empty($media_data)) {
						if($media_data != 'no_selection') {
							$image = $config['media_image_url'] . $media_data; 
							$image_data['image'][$media_att] = $image;				
						}	
					}
				}
			} else { 
				if($product->getThumbnail()) {		
					if($product->getThumbnail() != 'no_selection') {
						$image = $config['media_image_url'] . $product->getThumbnail(); 
						$image_data['image']['thumbnail'] = $image;
					}
				}
				if($product->getSmallImage()) {		
					if($product->getSmallImage() != 'no_selection') {
						$image = $config['media_image_url'] . $product->getSmallImage(); 
						$image_data['image']['small_image'] = $image;
					}
				}	
				if($product->getImage()) {		
					if($product->getImage() != 'no_selection') {
						$image = $config['media_image_url'] . $product->getImage(); 
						$image_data['image']['image'] = $image;					
					}
				}
			}
			if(!empty($config['images'])) {
				$image_data['image_link'] = $image;
				$container = new Varien_Object(array('attribute' => new Varien_Object(array('id' => $config['media_gallery_id']))));
				$img_product = new Varien_Object(array('id' => $product->getId(),'store_id' => $config['store_id']));
				$gallery = Mage::getResourceModel('catalog/product_attribute_backend_media')->loadGallery($img_product, $container);
				$images = array(); $i = 1;
				usort($gallery, function($a, $b) { return $a['position_default'] > $b['position_default']; });
				foreach($gallery as $gal_image) {									
					if($gal_image['disabled'] == 0) {
						$image_data['image']['all']['image_' . $i] = $config['media_image_url'] . $gal_image['file'];
						$image_data['image']['last'] = $config['media_image_url'] . $gal_image['file'];
						if($i == 1) { $image_data['image']['first'] = $config['media_image_url'] . $gal_image['file']; }				
						$i++;
					}
				}
				return $image_data; 
			} else {
				if(!empty($image_data['image'][$config['image_source']])) {
					return $image_data['image'][$config['image_source']];
				} else {
					return $image;
				}			
			}
		}
	}

	public function getProductCondition($product, $config) 
	{
		if(isset($config['condition_attribute'])) {
			if($condition = $product->getAttributeText($config['condition_attribute'])) {
				return $condition;
			} else{
				return false;
			}
		}
		return $config['condition_default']; 
	}

	public function getProductBundle($product, $config) 
	{
		if($product->getTypeId() == 'bundle') {		
			return 'true';
		}	
	}
		
	public function getProductAvailability($product, $config) 
	{
		if(!empty($config['stock_instock'])) {
			if($product->getUseConfigManageStock()) {
				$manage_stock = $config['stock_manage'];
			} else {
				$manage_stock = $product->getManageStock();			
			}	
			if($manage_stock) {
				if($product['stock_status']) {
					$availability = $config['stock_instock'];
				} else {
					$availability = $config['stock_outofstock'];
				}
			} else {
				$availability = $config['stock_instock'];
			}
			return $availability; 
		}
	}	

	public function getProductWeight($product, $config) 
	{
		if(!empty($config['weight'])) {
			$weight = number_format($product->getWeight(), 2, '.', '');		
			if(isset($config['weight_units'])) {
				$weight = $weight . ' ' . $config['weight_units'];
			}	
			return $weight;	
		}
	}

	public function getProductCategories($product, $config) 
	{
		if(isset($config['category_data'])) {
			$category_data = $config['category_data']; 
			$products_cat = array();
			$category_ids = $product->getCategoryIds();
			$level = 0;
			if(!empty($config['category_full'])) {
				$path = array(); 
				foreach($category_ids as $category_id) {
					if(isset($category_data[$category_id])) {
						$path[] = $category_data[$category_id]['name'];						
					}				
				}
				$products_cat = array('path' => $path);
			} else {
				foreach($category_ids as $category_id) {
					if(isset($category_data[$category_id])) {
						$products_cat[] = $category_data[$category_id];
						$level = $category_data[$category_id]['level'];
					}	
				}
			}
			return $products_cat;
		}
	}
	
	public function getProductData($product, $data, $config = '') 
	{
		$type = $data['type'];
		$source = $data['source'];
		$value = '';
		switch($type) {
			case 'price':
				if(!empty($product[$source])) {					
					$value = number_format($product[$source], 2, '.', '');	
					if(!empty($config['currency'])) {
						$value .= ' ' . $config['currency'];
					}	
				}
				break;
			case 'select':
				$value = $product->getAttributeText($source);
				break;
			case 'multiselect':
				if(count($product->getAttributeText($source))) {				
					if(count($product->getAttributeText($source)) > 1) {
						$value = implode(',', $product->getAttributeText($source));
					} else {
						$value = $product->getAttributeText($source);			
					}
				}
				break;
			default:
				if(isset($product[$source])) {					
					$value = $product[$source];	
				}		
				break;
		}
		return $value;
	}
	
	public function getProductPrice($product, $config) 
	{
		$price_data = array();
		$price_markup = $this->getPriceMarkup($config);
		$tax_param = $config['use_tax'];
		
		if(!empty($config['hide_currency'])) {
			$currency = '';
		} else {
			$currency = ' ' . $config['currency'];
		}			
		
		if(!empty($config['price_scope'])) {
			$price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'price', $config['store_id']);
		} else {
			$price = $product->getPrice();
		}

		$price = Mage::helper('tax')->getPrice($product, $price, $tax_param);
		$price_data['regular_price'] = number_format(($price * $price_markup), 2, '.', '') . $currency;
		$pricerule_price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $tax_param);
						
		$special_price = ''; $special_date = '';			
		if(($pricerule_price > 0) && ($pricerule_price < $price)) {
			$sales_price = $pricerule_price;
			$specialPriceFromDate = $product->getSpecialFromDate();
			$specialPriceToDate = $product->getSpecialToDate();
			$today = time();
			if($today >= strtotime($specialPriceFromDate)) {
				if($today <= strtotime($specialPriceToDate) || is_null($specialPriceToDate)) {
					$price_data['sales_date_start'] = $specialPriceFromDate;
					$price_data['sales_date_end'] = $specialPriceToDate;
				}
			}			
		}

		if(($product->getTypeId() == 'bundle') && ($price < 0.01)) {
			$price = $this->getPriceBundle($product, $config['store_id']);
		}		

		if($product->getTypeId() == 'grouped') {
			if(!empty($config['price_grouped'])) {
				$price = $this->getPriceGrouped($product, $config['price_grouped']);
			} else {
				if($price < 0.01) {
					$price = $this->getPriceGrouped($product);
				}	
			}
		}
		
		$price_data['final_price_clean'] = $price;	
		$price_data['price'] = number_format(($price * $price_markup), 2, '.', '') . $currency;

		if(isset($sales_price)) {
			$price_data['sales_price'] = number_format(($sales_price * $price_markup), 2, '.', '') . $currency;
		}
		
		return $price_data;		
	}
	
	public function getPriceMarkup($config) 
	{
		$markup = 1;
		if(!empty($config['price_add_tax']) && !empty($config['price_add_tax_perc'])) {
			$markup = 1 + ($config['price_add_tax_perc'] / 100);			
		}
		if($config['base_currency_code'] != $config['currency']) {
			$exchange_rate = Mage::helper('directory')->currencyConvert(1, $config['base_currency_code'], $config['currency']);		
			$markup = ($markup * $exchange_rate);
		}
		return $markup;	
	}

	public function getTaxUsage($config) 
	{
		if(!empty($config['force_tax'])) {
			if($config['force_tax'] == 'incl') {
				return 'true';
			} else {
				return '';		
			}	
		} else {
			return 'true';		
		}
	}
		
	public function addAttributeData($attributes, $config = '')
	{			
		foreach($attributes as $key => $attribute) {
			$type = (!empty($attribute['type']) ? $attribute['type'] : '');
			$action = (!empty($attribute['action']) ? $attribute['action'] : '');
			$parent = (!empty($attribute['parent']) ? $attribute['parent'] : '');
			if(isset($attribute['source'])) {
				$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attribute['source']);
				$type = $attributeModel->getFrontendInput();
			}
			if(!empty($config['conf_fields'])) {
				$conf_attributes = explode(',', $config['conf_fields']);
				if(in_array($key, $conf_attributes)) {
					$parent = '1';
				}	
			}
			$attributes[$key] = array('label' => $attribute['label'], 'source' => $attribute['source'], 'type' => $type, 'action' => $action, 'parent' => $parent);
		}
		return $attributes;
	}

	public function getCategoryData($config, $storeId) 
	{
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
		$attributes = array('entity_id','path','name','level');

		if(!empty($config['category_custom'])) {
			$attributes[] = $config['category_custom'];
		}	
		if(!empty($config['category_replace'])) {
			$attributes[] = $config['category_replace'];
		}

		if(!empty($config['filter_enabled'])) {			
			$type = $config['filter_type'];
			$f_categories = explode(',', $config['filter_cat']);
			if($type && $f_categories) {
				if($type == 'include') {
					$categories = Mage::getModel('catalog/category')->setStoreId($storeId)->getCollection()->addAttributeToSelect($attributes)->addFieldToFilter('is_active', array('eq' => 1))->addAttributeToFilter('entity_id', array('in' => $f_categories));
				} else {
					$categories = Mage::getModel('catalog/category')->setStoreId($storeId)->getCollection()->addAttributeToSelect($attributes)->addFieldToFilter('is_active', array('eq' => 1))->addAttributeToFilter('entity_id', array('nin' => $f_categories));
				}
			}
		} else {			
			$categories = Mage::getModel('catalog/category')->setStoreId($storeId)->getCollection()->addAttributeToSelect($attributes)->addFieldToFilter('is_active', array('eq' => 1));
		}
		$_categories = array();

		foreach($categories as $cat) {
			$custom = ''; $name = '';
			if(!empty($config['category_replace'])) {
				if(!empty($cat[$config['category_replace']])) {
					$name = $cat[$config['category_replace']];
				}
			}	
			if(isset($config['category_custom'])) {
				if(!empty($cat[$config['category_custom']])) {
					$custom = $cat[$config['category_custom']];
				}
			}
			if(empty($name)) { $name = $cat['name']; } 
			$_categories[$cat->getId()] = array('path' => $cat['path'], 'custom' => $custom, 'name' => $name, 'level' => $cat['level']);
		}	
	
		foreach($_categories as $key => $cat) {
			$path = array();
			$custom_path = array();
			$paths = explode('/', $cat['path']);
			foreach($paths as $p) {
				if(!empty($_categories[$p]['name'])) {
					if($_categories[$p]['level'] > 1) {
						$path[] = $_categories[$p]['name'];
						if(!empty($_categories[$p]['custom'])) {
							$custom_path[] = $_categories[$p]['custom'];
						}	
					}	
				}	
			}
			$_categories[$key] = array('path' => $this->cleanData($path, 'stiptags'), 'custom_path' => $this->cleanData($custom_path, 'stiptags'), 'custom' => $this->cleanData(end($custom_path), 'striptags'), 'name' => $this->cleanData($cat['name'], 'striptags'), 'level' => $cat['level']);
		}
		return $_categories;
	}

	public function getParentData($product, $config) 
	{
		if(!empty($config['conf_enabled'])) {
			if(($product['type_id'] == 'simple')) {
				$config_ids = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());	
				$group_ids = Mage::getResourceSingleton('catalog/product_link')->getParentIdsByChild($product->getId(), Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
				if($config_ids) {		
					return $config_ids[0]; 	
				}
				if($group_ids) {		
					return $group_ids[0]; 	
				}				
			}	
		}
	}
	
	public function validateProduct($product, $config, $parent) 
	{
		if(empty($config['skip_validation'])) {
			if($product['visibility'] == 1) {
				if(empty($parent)) {
					return false;
				}
				if($parent['status'] != 1) {
					return false;
				}
			}
			if(!empty($config['filter_exclude'])) {
				if($product[$config['filter_exclude']] == 1) {
					return false;
				}	
			}	
			if(!empty($config['hide_no_stock'])) {
				if($product->getUseConfigManageStock()) {
					$manage_stock = $config['stock_manage'];
				} else {
					$manage_stock = $product->getManageStock();			
				}	
				if($manage_stock) {
					if(!$product['stock_status']) {
						return false;			
					}
				}	
			}
			if(!empty($config['conf_exclude_parent'])) {
				if($product->getTypeId() == 'configurable') {
					return false;
				}	
			}			
		}
		return true;
	}

	public function validateParent($parent, $config, $product) 
	{
		return $this->validateProduct($product, $config, $parent);
	}

	public function getPriceBundle($product, $storeId)
	{		
		if(($product->getPriceType() == '1') && ($product->getFinalPrice() > 0)) {
			$price = $product->getFinalPrice();
		} else {
			$priceModel = $product->getPriceModel();
			$block = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
			$options = $block->setProduct($product)->getOptions();
			$price = 0;
		
			foreach ($options as $option) {
			  $selection = $option->getDefaultSelection();
			  if($selection === null) { continue; }
				$selection_product_id = $selection->getProductId(); 
				$_resource = Mage::getSingleton('catalog/product')->getResource();
				$final_price = $_resource->getAttributeRawValue($selection_product_id, 'final_price', $storeId);
				$selection_qty = $_resource->getAttributeRawValue($selection_product_id, 'selection_qty', $storeId);
				$price += ($final_price * $selection_qty); 
			}				
		}
		if($price < 0.01) {
			$price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);			
		}
		return $price; 				
	}		

	public function getPriceGrouped($product, $pricemodel = '') 
	{		
		if(!$pricemodel) { $pricemodel = 'min'; }
		$prices = array();
		$_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
		foreach($_associatedProducts as $_item) {
			$price_associated = Mage::helper('tax')->getPrice($_item, $_item->getFinalPrice(), true);
			if($price_associated > 0) {
				$prices[] = $price_associated;
			}	
		}
		if(!empty($prices)) {
			if($pricemodel == 'min') { return min($prices); }	
			if($pricemodel == 'max') { return max($prices); }	
			if($pricemodel == 'total') { return array_sum($prices); }	
		}
	}

	public function getTypePrices($config, $products) 
	{
		$type_prices = array();
		if(!empty($config['conf_enabled'])) {	
			foreach($products as $product) {
				if($product->getTypeId() == 'configurable') {
					$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
					$att_prices = array();
					$base_price = $product->getFinalPrice();
					$base_price_reg = $product->getPrice();
					foreach ($attributes as $attribute){
						$prices = $attribute->getPrices();
						foreach ($prices as $price){
							if ($price['is_percent']) { 
								$att_prices[$price['value_index']] = (float)(($price['pricing_value'] * $base_price / 100) * $config['markup']);
								$att_prices[$price['value_index'] . '_reg'] = (float)(($price['pricing_value'] * $base_price_reg / 100) * $config['markup']);
							} else {
								$att_prices[$price['value_index']] = (float)($price['pricing_value'] * $config['markup']);
								$att_prices[$price['value_index'] . '_reg'] = (float)($price['pricing_value'] * $config['markup']);
							}
						}
					}
					$simple = $product->getTypeInstance()->getUsedProducts();
					$simple_prices = array();	
					foreach($simple as $sProduct) {
						$total_price = $base_price;
						$total_price_reg = $base_price_reg;
						foreach($attributes as $attribute) {
							$value = $sProduct->getData($attribute->getProductAttribute()->getAttributeCode());
							if(isset($att_prices[$value])) {
								$total_price += $att_prices[$value];
								$total_price_reg += $att_prices[$value . '_reg'];
							}
						}
						$type_prices[$sProduct->getEntityId()] = number_format(($total_price * $config['markup']), 2, '.', '');
						$type_prices[$sProduct->getEntityId() . '_reg'] = number_format(($total_price_reg * $config['markup']), 2, '.', '');
					}
				}
			}
		}
		return $type_prices;
	}
	
	public function checkOldVersion($dir) 
	{
		if($dir) {
			$dir = Mage::getBaseDir('app') . DS . 'code' . DS . 'local' . DS . 'Magmodules' . DS . $dir;
			return file_exists($dir);
		}
	}
	
	public function checkFlatCatalog($attributes) 
	{		
		$non_flat_attributes = array();
		foreach($attributes as $key => $attribute) {
			if(!empty($attribute['source'])) {
				if(($attribute['source'] != 'entity_id') && ($attribute['source'] != 'sku')) {
					$_attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attribute['source']);
					if($_attribute->getUsedInProductListing() == 0) {
						if($_attribute->getId()) {
							$non_flat_attributes[$_attribute->getId()] = $_attribute->getFrontendLabel();
						}
					}	
				}	
			}	
		}
		return $non_flat_attributes;
	}

	public function getMediaAttributes() 
	{
        $media_types = array();
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('frontend_input', 'media_image');
        foreach($attributes as $attribute) {
			$media_types[] = $attribute->getData('attribute_code');
		}			
		return $media_types;
	}

	public function getStoreIdConfig() 
	{
		$store_id = 0;
		if(strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) {
			$store_id = Mage::getModel('core/store')->load($code)->getId();
		}
		return $store_id;	
	}

	public function getProductUrlSuffix($storeId) 
	{
		$suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix', $storeId);
		if(!empty($suffix)) {
			if(($suffix[0] != '.') && ($suffix != '/')) {
				$suffix = '.' . $suffix;
			}
		}
		return $suffix;
	}
	
	public function getUncachedConfigValue($path, $storeId = 0) 
	{
		$collection = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path', $path);		
		if($storeId == 0) {
			$collection = $collection->addFieldToFilter('scope_id', 0)->addFieldToFilter('scope', 'default');
		} else {
			$collection = $collection->addFieldToFilter('scope_id', $storeId)->addFieldToFilter('scope', 'stores');		
		}
		return $collection->getFirstItem()->getValue();			
	}
}