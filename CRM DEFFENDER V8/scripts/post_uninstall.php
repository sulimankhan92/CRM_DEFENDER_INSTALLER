<?php
/*********************************************************************************
 * This file is part of Lion Solution CRM Defender.
 * Lion Solution CRM Defender is a package for SugarCRM
 * 
 * Author : Lion Solution (http://www.lionsolution.it)
 * All rights (c) 2011-2017 by Lion Solution
 *
 * This Version of the Lion Solution CRM Defender is licensed software and may only be used in 
 * alignment with the License Agreement received with this Software.
 * This Software is copyrighted and may not be further distributed without
 * written consent of Lion Solution
 * 
 * You can contact Lion Solution at Lion Solution - Via della Provvidenza, 37/A - 35030 - Rubano (PD) - Italy
 * or via email at info@lionsolution.it
 * 
 ********************************************************************************/
 
if (! defined('sugarEntry') || ! sugarEntry) 
	die('Not A Valid Entry Point');

function post_uninstall() {
	require('modules/Configurator/Configurator.php');
    $cfg = new Configurator();
	$cfg->loadConfig();
	
	if (isset($cfg->config['LS_CRM_Defender_free_localhost'])) {
		$GLOBALS['log']->fatal("LS_CRM_Defender_free_localhost found in SugarConfig");
		unset($cfg->config['LS_CRM_Defender_free_localhost']);
	}
	if (isset($cfg->config['LS_CRM_Defender_maxFailedAccesses'])) {
		unset($cfg->config['LS_CRM_Defender_maxFailedAccesses']);
	}
	if (isset($cfg->config['LS_CRM_Defender_whiteList'])) {
		unset($cfg->config['LS_CRM_Defender_whiteList']);
	}
	$cfg->handleOverride();
	
		
	$autoexecute = false; //execute the SQL
	$show_output = false; //output to the screen
	require_once("modules/Administration/QuickRepairAndRebuild.php");
	$randc = new RepairAndClear();
	$randc->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), $autoexecute,$show_output);
}
?>