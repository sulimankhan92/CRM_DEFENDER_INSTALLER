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
$dictionary['LS_CRM_Defender'] = array(
	'table'=>'ls_crm_defender',
	'audited'=>true,
	'fields'=>array (
  'ip_address' => 
  array (
    'required' => false,
    'name' => 'ip_address',
    'vname' => 'LBL_IP_ADDRESS',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'false',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => 0,
    'reportable' => 0,
    'len' => '15',
	'editable' => false,
	'inline_edit'=> false,
  ),
  'typed_name' => 
  array (
    'required' => false,
    'name' => 'typed_name',
    'vname' => 'LBL_TYPED_NAME',
    'type' => 'varchar',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'false',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => 0,
    'reportable' => 0,
    'len' => '25',
	'editable' => false,
	'inline_edit'=> false,
  ),
  'is_admin' => 
  array (
    'required' => false,
    'name' => 'is_admin',
    'vname' => 'LBL_IS_ADMIN',
    'type' => 'bool',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'false',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => 0,
    'reportable' => 0,
    'len' => '255',
	'editable' => false,
	'inline_edit'=> false,
  ),
  'result' =>
  array (
    'required' => false,
    'name' => 'result',
    'vname' => 'LBL_RESULT',
    'type' => 'enum',
    'massupdate' => 0,
    'comments' => '',
    'help' => '',
    'importable' => 'false',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => 0,
    'reportable' => 0,
	'unified_search' => false,
    'merge_filter' => 'disabled',
    'len' => 13,
	'size' => '20',
    'options' => 'ls_crm_defender_result_dom',
	'dependency' => false,
	'editable' => false,
	'inline_edit'=> false,
  ),  
),
	'relationships'=>array (
),
	'optimistic_lock'=>false,/*#LS 2017_04_24*/
);
require_once('include/SugarObjects/VardefManager.php');
VardefManager::createVardef('LS_CRM_Defender','LS_CRM_Defender', array('basic','assignable'));
