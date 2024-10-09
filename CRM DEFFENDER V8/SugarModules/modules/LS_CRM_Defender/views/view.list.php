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

require_once 'include/MVC/View/views/view.list.php';

class LS_CRM_DefenderViewList extends ViewList{
    // Hide Quick Edit Pencil
	public function preDisplay(){
        parent::preDisplay();
        $this->lv->quickViewLinks = false;
    } //preDisplay
	
	
	// Hide Create Button
	function Display(){
		print '<style type="text/css">#create_link, #create_image{ display:none; }</style>';
		parent::Display();
	} //Display
	
} //class LS_CRM_DefenderViewList
?>
