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
 
global $sugar_version;

$admin_option_defs=array();


// LIST VIEW
$admin_option_defs['Administration']['LS_CRM_Defender'] = array(
	'Users', 'LBL_LS_CRM_DEFENDER_ADMIN', 'LBL_LS_CRM_DEFENDER_DESCRIPTION', './index.php?module=LS_CRM_Defender&action=index'
	);
// SETTINGS
$admin_option_defs['Administration']['LS_CRM_Defender_Settings'] = array(
	'Administration', 'LBL_LS_CRM_DEFENDER_SETTINGS', 'LBL_LS_CRM_DEFENDER_SETTINGS_DESCRIPTION', './index.php?module=LS_CRM_Defender&action=config'
);

if(preg_match( "/^6.*/", $sugar_version) ) {
    $admin_option_defs['Administration']['LS_CRM_Defender_info']= array('helpInline','LBL_LS_CRM_DEFENDER_LICENSE_TITLE','LBL_LS_CRM_DEFENDER_LICENSE','./index.php?module=LS_CRM_Defender&action=license');
} else {
    $admin_option_defs['Administration']['LS_CRM_Defender_info']= array('helpInline','LBL_LS_CRM_DEFENDER_LICENSE_TITLE','LBL_LS_CRM_DEFENDER_LICENSE','javascript:parent.SUGAR.App.router.navigate("#bwc/index.php?module=LS_CRM_Defender&action=license", {trigger: true});');
}
$admin_group_header[]= array('LBL_LS_CRM_DEFENDER_GROUP','',false,$admin_option_defs, 'LBL_LS_CRM_DEFENDER_GROUP_DESCRIPTION');

?>
