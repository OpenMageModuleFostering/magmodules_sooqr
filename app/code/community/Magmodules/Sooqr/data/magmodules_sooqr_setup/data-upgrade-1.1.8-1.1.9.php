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

$token = '';
$chars = str_split("abcdefghijklmnopqrstuvwxyz0123456789");
for($i = 0; $i < 16; $i++) {
	$token .= $chars[array_rand($chars)];
}

$config = new Mage_Core_Model_Config();
$config->saveConfig('sooqr_connect/generate/token', $token, 'default', 0);