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

class Magmodules_Sooqr_Block_Adminhtml_Config_Form_Field_Filter extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();

	/**
	 * Magmodules_Sooqr_Block_Adminhtml_Config_Form_Field_Shipping constructor.
	 */
	public function __construct()
    {        
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        $renderer_attributes = $layout->createBlock('sooqr/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_attributes->setOptions(Mage::getModel('sooqr/adminhtml_system_config_source_attribute')->toOptionArray());
       
        $renderer_conditions = $layout->createBlock('sooqr/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_conditions->setOptions(Mage::getModel('sooqr/adminhtml_system_config_source_conditions')->toOptionArray());

        $this->addColumn('attribute', array(
            'label' => Mage::helper('sooqr')->__('Attribute'),
            'style' => 'width:100px',
        	'renderer' => $renderer_attributes
        ));
        
        $this->addColumn('condition', array(
            'label' => Mage::helper('sooqr')->__('Condition'),
            'style' => 'width:100px',
        	'renderer' => $renderer_conditions
        ));

        $this->addColumn('value', array(
            'label' => Mage::helper('sooqr')->__('Value'),
            'style' => 'width:100px',
        ));        

        $this->_renders['attribute'] = $renderer_attributes; 
        $this->_renders['condition'] = $renderer_conditions; 

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('sooqr')->__('Add Filter');
        parent::__construct();
    }

	/**
	 * @param Varien_Object $row
	 */
	protected function _prepareArrayRow(Varien_Object $row)
    {    	
    	foreach ($this->_renders as $key => $render){
	        $row->setData(
	            'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
	            'selected="selected"'
	        );
    	}
    } 

}