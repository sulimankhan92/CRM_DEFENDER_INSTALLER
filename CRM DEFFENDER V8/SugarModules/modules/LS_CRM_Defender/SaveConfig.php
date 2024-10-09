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
/*********************************************************************************

 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('modules/Administration/Administration.php');
require_once('modules/LS_CRM_Defender/LS_CRM_Defender.php');
require_once('modules/LS_CRM_Defender/util.php');
require_once('modules/LS_CRM_Defender/IPAccessManager.php');
require('modules/Configurator/Configurator.php');

    $cfg = new Configurator();
	// save setting
    $cfg->config['LS_CRM_Defender_free_localhost'] = ($_REQUEST['free_localhost'] == 1) ? true : false;
    $cfg->config['LS_CRM_Defender_BlockAllIpsButNotWhiteList'] = ($_REQUEST['blockAllIpsButOnce'] == 1) ? true : false;
	$cfg->config['LS_CRM_Defender_maxFailedAccesses'] = $_REQUEST['maxFailedAccesses'];
	$whiteList = preg_replace('#\s+#',',',trim($_REQUEST['whiteList']));	// save ip addresses to whitelist as a comma separated list of values without "\n"
	$cfg->config['LS_CRM_Defender_whiteList'] = $whiteList;

	if ($_REQUEST['whiteList']) {

		$monitor = new monitorAccesses();
		// check if one of ips is blocked. If yes, delete it from the htaccess file
		$ips = explode(",", $whiteList);
		foreach ($ips as $ip) {
			// look for ip in .htaccess - If found return the line number
			$line_number = false;
			$line_number = $monitor->htaccess_look($ip);
			if($line_number) {
				$filename = '../../.htaccess';
				$handle = fopen($filename, 'c+');
				//remove the ip to be blocked from the htaccess file
				$multiple = true;
				$monitor->deleteLine($line_number, $handle, $multiple);
			} //if line_number
		} //foreach

	} //if ($_REQUEST['whiteList'])

	$cfg->config['LS_CRM_Defender_recipientAddressEnabled'] = ($_REQUEST['recipientAddressEnabled'] == 1) ? true : false;
	$cfg->config['LS_CRM_Defender_recipientAddress'] = $_REQUEST['recipientAddress'];

    $ipManager = new IPAccessManager();
    $whitelistIPs = explode(',', $cfg->config['LS_CRM_Defender_whiteList']);

    try {
        $whitelistIPs = array_filter($whitelistIPs, fn($ip) => !empty(trim($ip)) && filter_var($ip, FILTER_VALIDATE_IP));

        if (empty($whitelistIPs)) {
            $mod_strings = return_module_language($GLOBALS['current_language'], 'LS_CRM_Defender');
            throw new Exception($mod_strings['LBL_INVALID_IP_ERROR']);
        }

        $ipManager->allowIPs(($cfg->config['LS_CRM_Defender_BlockAllIpsButNotWhiteList'] ?? false) ? 'ALLOW' : 'REMOVE', $whitelistIPs);
    } catch (Exception $e) {
        $GLOBALS['log']->error("Error: " . $e->getMessage());

        // Redirect with error message
        header("Location: index.php?module=LS_CRM_Defender&action=config&error_message=" . urlencode($e->getMessage()));
        exit();
    }

    $cfg->handleOverride();

header("Location: index.php?action={$_POST['return_action']}&module={$_POST['return_module']}");

