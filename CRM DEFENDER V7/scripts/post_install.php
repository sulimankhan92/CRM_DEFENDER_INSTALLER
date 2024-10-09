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
 
function post_install() {
	$autoexecute = false; //execute the SQL
	$show_output = false; //output to the screen
	
	//install table for user management
    global $db;
    if (!$db->tableExists('so_users')) {

        $fieldDefs = array(
            'id' => array (
              'name' => 'id',
              'vname' => 'LBL_ID',
              'type' => 'id',
              'required' => true,
              'reportable' => true,
            ),
            'deleted' => array (
                'name' => 'deleted',
                'vname' => 'LBL_DELETED',
                'type' => 'bool',
                'default' => '0',
                'reportable' => false,
                'comment' => 'Record deletion indicator',
            ),
            'shortname' => array (
                'name' => 'shortname',
                'vname' => 'LBL_SHORTNAME',
                'type' => 'varchar',
                'len' => 255,
            ),
            'user_id' => array (
                'name' => 'user_id',
                'rname' => 'user_name',
                'module' => 'Users',
                'id_name' => 'user_id',
                'vname' => 'LBL_USER_ID',
                'type' => 'relate',
                'isnull' => 'false',
                'dbType' => 'id',
                'reportable' => true,
                'massupdate' => false,
            ),
        );
        
        $indices = array(
            'id' => array (
                'name' => 'so_userspk',
                'type' => 'primary',
                'fields' => array (
                    0 => 'id',
                ),
            ),
            'shortname' => array (
                'name' => 'shortname',
                'type' => 'index',
                'fields' => array (
                    0 => 'shortname',
                ),
            ),
        );
        $db->createTableParams('so_users',$fieldDefs,$indices);
    } //if  !$db->tableExists'so_users'
    
    
    global $sugar_version;
    if(preg_match( "/^6.*/", $sugar_version)) {
        echo "
            <script>
            document.location = 'index.php?module=LS_CRM_Defender&action=license';
            </script>"
        ;
    } else {
        echo "
            <script>
            var app = window.parent.SUGAR.App;
            window.parent.SUGAR.App.sync({callback: function(){
                app.router.navigate('#bwc/index.php?module=LS_CRM_Defender&action=license', {trigger:true});
            }});
            </script>"
        ;
    } //else
    
	require_once("modules/Administration/QuickRepairAndRebuild.php");
	$randc = new RepairAndClear();
	$randc->repairAndClearAll(array('clearAll'),array(translate('LBL_ALL_MODULES')), $autoexecute,$show_output);
}
