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
$module_name = 'LS_CRM_Defender';

global $current_user;

if (!is_admin($current_user) && !is_admin_for_any_module($current_user))
{
   sugar_die("Unauthorized access to CRM Defender.");
}


if(!isset($_REQUEST['orderBy'])){
        $_REQUEST['orderBy'] = 'date_entered';
        $_REQUEST['sortOrder'] = 'desc';
}


$listViewDefs [$module_name] = 
array (
  'IP_ADDRESS' => 
  array (
    'width' => '10%',
    'label' => 'LBL_IP_ADDRESS',
    'default' => true,
  ),
  'TYPED_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_TYPED_NAME',
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'width' => '10%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => true,
  ),
  'MODIFIED_BY_NAME' => 
  array (
	'module' => 'Users',
    'link' => true,
    'label' => 'LBL_RECOGNIZED_USER',
    'id' => 'MODIFIED_USER_ID',
    'width' => '10%',
    'default' => true,
	'related_fields' => 
    array (
      0 => 'modified_user_id',
    ),
  ),
  'IS_ADMIN' => 
  array (
    'width' => '10%',
    'label' => 'LBL_IS_ADMIN',
    'default' => true,
  ),
  'RESULT' => 
  array (
    'width' => '10%',
    'label' => 'LBL_RESULT',
    'default' => true,
  ),
);
$this->lv->showMassupdateFields = 0;
?>
