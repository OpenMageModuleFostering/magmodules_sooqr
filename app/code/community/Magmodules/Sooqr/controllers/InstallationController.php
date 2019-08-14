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
 
class Magmodules_Sooqr_InstallationController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction() 
	{						
		if(Mage::getStoreConfig('sooqr_connect/general/enabled')) {
			if($feed = Mage::getModel('sooqr/sooqr')->getInstallation()) {
				$this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
				$this->getResponse()->setBody(json_encode($feed));						
			}
		}
	}
    
}
