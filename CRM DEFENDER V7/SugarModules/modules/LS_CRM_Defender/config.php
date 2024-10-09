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
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('XTemplate/xtpl.php');

require_once('modules/Administration/Administration.php');
require_once('modules/LS_CRM_Defender/Forms.php');
require_once('modules/LS_CRM_Defender/LS_CRM_Defender.php');
require_once('modules/LS_CRM_Defender/license/OutfittersLicense.php');
	$validate_license = OutfittersLicense::isValid('LS_CRM_Defender');
	if(false) {
		if(is_admin($current_user)) {
			SugarApplication::appendErrorMessage('LS_CRM_Defender is no longer active due to the following reason: '.$validate_license.' Users will have limited to no access until the issue has been addressed.');
		}
		echo '<BR><BR><h2><p class="error">LS_CRM_Defender is no longer active</p></h2><p class="error">Please renew your subscription or check your license configuration.</p>';
	} //if not validate_license
	else{
		global $mod_strings;
		global $app_list_strings;
		global $app_strings;
		global $current_user;
		if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");
		//Fix Notice error
		$mod_id = "";
		$mod_name = "";
		if(isset($mod_strings['LBL_MODULE_ID'])) {
			$mod_id = $mod_strings['LBL_MODULE_ID'];
		}
		if(isset($mod_strings['LBL_MODULE_NAME'])) {
			$mod_name = $mod_strings['LBL_MODULE_NAME'];
		}
		echo "\n<p>\n";
		echo get_module_title($mod_id, $mod_name.": ".$mod_strings['LBL_CONFIGURE_SETTINGS'], false);
		echo "\n</p>\n";
		global $theme;
		global $currentModule;
		$theme_path = "themes/".$theme."/";
		$image_path = $theme_path."images/";
		$focus = new Administration();
		$focus->retrieveSettings(); //retrieve all admin settings.
		
		$xtpl=new XTemplate ('modules/LS_CRM_Defender/config.html');
		$xtpl->assign("MOD", $mod_strings);
		$xtpl->assign("APP", $app_strings);
		
		$xtpl->assign("RETURN_MODULE", "Administration");
		$xtpl->assign("RETURN_ACTION", "index");
		
		$xtpl->assign("MODULE", $currentModule);
		$xtpl->assign("THEME", $theme);
		$xtpl->assign("IMAGE_PATH", $image_path);$xtpl->assign("PRINT_URL", "index.php?".$GLOBALS['request_string']);
		$xtpl->assign("HEADER", get_module_title("LS_CRM_Defender", "{MOD.LBL_CONFIGURE_SETTINGS}", true));
		$xtpl->assign("FOOTER", "", true);
		
		// Hidden value: $_SERVER['REMOTE_ADDR']
		$xtpl->assign("globalIP", $_SERVER['REMOTE_ADDR']);
		
		// avoid to block 127.0.0.1
		$free_localhost = '';
		if(isset($sugar_config['LS_CRM_Defender_free_localhost']) && $sugar_config['LS_CRM_Defender_free_localhost'] == true) {
			$free_localhost = 'CHECKED';
		} 
		$xtpl->assign('free_localhost', $free_localhost);

		$blockAllIpsButOnce = '';
		if(isset($sugar_config['LS_CRM_Defender_BlockAllIpsButNotWhiteList']) && $sugar_config['LS_CRM_Defender_BlockAllIpsButNotWhiteList'] == true) {
			$blockAllIpsButOnce = 'CHECKED';
		}
		$xtpl->assign('blockAllIpsButOnce', $blockAllIpsButOnce);

		// Max number of failed attempts before Lock Out
		if(isset($sugar_config['LS_CRM_Defender_maxFailedAccesses']) && $sugar_config['LS_CRM_Defender_maxFailedAccesses'] !=="") {
			$maxFailedAccesses = $sugar_config['LS_CRM_Defender_maxFailedAccesses'];
		} 
		$xtpl->assign('maxFailedAccesses', $maxFailedAccesses);
		
		// add to White List
		$whiteList = '';
		if(isset($sugar_config['LS_CRM_Defender_whiteList']) && $sugar_config['LS_CRM_Defender_whiteList'] !=="") {
			$whiteList = preg_replace('/[ ,]+/', PHP_EOL, $sugar_config['LS_CRM_Defender_whiteList']);
		} 
		$xtpl->assign('whiteList', $whiteList);
		
		// Enable Email Notification for LockOuts
		$recipientAddressEnabled = '';
		if(isset($sugar_config['LS_CRM_Defender_recipientAddressEnabled']) && $sugar_config['LS_CRM_Defender_recipientAddressEnabled'] == true) {
			$recipientAddressEnabled = 'CHECKED';
		}
		$xtpl->assign('recipientAddressEnabled', $recipientAddressEnabled);
		
		// Recipient Email Address for LockOut notifications
		if(isset($sugar_config['LS_CRM_Defender_recipientAddress']) && $sugar_config['LS_CRM_Defender_recipientAddress'] !=="") {
			$recipientAddress = $sugar_config['LS_CRM_Defender_recipientAddress'];
		}
		$xtpl->assign('recipientAddress', $recipientAddress);
		
		$xtpl->parse("main");
		
		$xtpl->out("main");
	} //else ok validate_license
?>
