<?php
/*********************************************************************************
 * This file is part of Lion Solution CRM Defender.
* Lion Solution CRM Defender is a package for SugarCRM
*
* Author : Lion Solution (http://www.lionsolution.it)
* All rights (c) 2013-2017 by Lion Solution
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

#[\AllowDynamicProperties]
class monitorAccesses {
	/*
	 * Function updateAccessControl
	 *
	 * launched by Logic Hook in Users module
	 * Insert into ls_crm_defender table the result of the access temptative
	 *
	 * @bean (array)
	 * @event (string)
	 * @arguments (string)
	 */
	function updateAccessControl($bean, $event, $arguments='failed'){
		require_once('modules/LS_CRM_Defender/license/OutfittersLicense.php');
		$validate_license = OutfittersLicense::isValid('LS_CRM_Defender');
		if(true) {
			global $current_user,$db;
			if(empty($event)){
				$event = 'user_session_init_failed';
			} //if
			switch($event) {
				case 'user_session_init_failed':
					$result = "Failed";
					break;
				case 'after_user_session_init':
					$result = "Success";
					break;
			} //switch

			$uuid = create_guid();
			if (!$_SESSION['login_user_name']) 	//in case of succesful attempt
				$typed_name = $bean->user_name;
			else 							    //in case of failed attempt
				$typed_name = $_SESSION['login_user_name'];
			if($typed_name){                    //if typed_name != '' to avoid false trigger like Collabspot or Dispage connections)
				$ip_address = $_SERVER['REMOTE_ADDR'];
				$timestamp = gmdate('Y-m-d H:i:s');
				$query = "INSERT INTO ls_crm_defender (id,name,date_entered,date_modified,modified_user_id,created_by,description,deleted,assigned_user_id,ip_address,typed_name,is_admin,result)"
					."VALUES('"
					.$uuid."','"
					.$event." ".$result."','"
					.$timestamp."','"
					.$timestamp."','"
					.$current_user->id."','1','','0','1','"
					.$ip_address."','"
					.$typed_name."','"
					.$current_user->is_admin."','"
					.$result."')";
				$res = $db->query($query,false);
			} //if typed_name != ''
		} //if validate_license
	} //updateAccessControl

	/*
	 * Function deleteLine
	 *
	 * remove one or multiple lines
	 * if multiple doesn't exist it removes the @line_number-th
	 * if multiple == true, removes 9 lines from the @line_number-th to the @line_number+8th from the file referenced by @handle
	 *
	 * @line_number (integer)
	 * @handle (string)
	 * @multiple (true/false)
	 */
	function deleteLine($line_number, $handle, $multiple = false){
		if ($handle) {
			// Add each line to an array
			$array = explode("\n", stream_get_contents($handle, -1,0));
			if (!$multiple){
				unset($array[$line_number-1]);
			} //if !$multiple
			else{
				for ($i=$line_number-1;$i<$line_number+8;$i++){
					//$GLOBALS['log']->fatal("i vale: ".$i);
					unset($array[$i]);
				} //for
			} //for
		} //if handle
		ftruncate($handle, 0); //empty the file
		rewind ($handle); //set the pointer to the beginning
		foreach($array as $row) {
			$row = str_replace("\r", "", $row); //remove any carriage return from the row
			fwrite($handle, $row.PHP_EOL); //write the row to the file plus \r\n
			//fwrite($handle, $row);
		} //foreach
	} //deleteLine

	/*
	 * Function addToHTAccess
	 *
	 * if the .htaccess file dosn't exist, create it
	 *  search for END Tag and unset it, writes back on the file the previsious content plus the ipAddress restriction needed
	 *
	 * @ip_address (string) separated by dots as in aaa.bbb.ccc.ddd
	 */
	function addToHTAccess($ip_address){
		$filename = '.htaccess';
		$text = "";
		$handle = fopen($filename, 'c+'); //Open the file for reading and writing. If the file does not exist, it is created. If it exists, the file pointer is positioned on the beginning of the file.
		if (flock($handle, LOCK_EX)) {// acquire an exclusive lock
			$search = "# CRM DEFENDER END RESTRICTIONS";
			$line_number = false;
			$count = 0;
			while (($line = fgets($handle, 4096)) !== FALSE and !$line_number) {
				$count++;
				$line_number = (strpos($line, $search) !== FALSE) ? $count : $line_number;
			} //while
			if ($line_number){ // if  # CRM DEFENDER END RESTRICTIONS has been found
				$this->deleteLine($line_number, $handle);
			} //if
			else {
				$text .= '
# CRM DEFENDER BEGIN RESTRICTIONS';
			} //else

			$ip_address_parts = explode(".",$ip_address);
			$a = $ip_address_parts[0];
			$b = $ip_address_parts[1];
			$c = $ip_address_parts[2];
			$d = $ip_address_parts[3];

			$text .= '
# IP ADDRESS BEGIN:'. $ip_address.'
SetEnvIF REMOTE_ADDR "^'.$a.'\.'.$b.'\.'.$c.'\.'.$d.'$" DenyAccess
SetEnvIF X-FORWARDED-FOR "^'.$a.'\.'.$b.'\.'.$c.'\.'.$d.'$" DenyAccess
SetEnvIF X-CLUSTER-CLIENT-IP "^'.$a.'\.'.$b.'\.'.$c.'\.'.$d.'$" DenyAccess
order allow,deny
deny from env=DenyAccess
deny from '.$a.'.'.$b.'.'.$c.'.'.$d.'
allow from all
# IP ADDRESS END:'. $ip_address.'
# CRM DEFENDER END RESTRICTIONS';
			fwrite($handle, $text);
			chmod($filename, 0644);  	// set correct file permission on .htaccess
			fflush($handle);			// free the handle pointer
			flock($handle, LOCK_UN);	// unlock the file
		} //if flock
		else {
			$GLOBALS['log']->fatal("Couldn't get the lock on $filename");
		} //else flock
		fclose($handle);
	} //addToHTAccess

	/*
	 * Function markRecordLockOut
	 *
	 * Look for last failed record related to @ip_address entered
	 * set it to Banned
	 *
	 * @ip_address (string) in the format aaa.bbb.ccc.ddd
	 */
	function markRecordLockOut($ip_address){
		global $db;
		$res = $db->query("SELECT id FROM ls_crm_defender WHERE ip_address = '".$ip_address."' AND result='Failed' AND deleted=0 ORDER BY date_entered DESC LIMIT 1");
		$row = $res->fetch_assoc();
		$id = $row['id'];
		$query="UPDATE ls_crm_defender SET result = 'Banned' WHERE id = '".$id."'";
		$res = $db->query($query,false);
	} //markRecordLockOut

	/*
	 * Function updateHTAccess
	 *
	 * launched by Logic Hook in Users module
	 * Look in the DB if from the ipAddress submitted there have been other Failed temptatives on the same day
	 * if failedAccesses more than maxFailedAccesses times in a day
	 * addToHTAccess that ipAddress
	 * mark as Banned on ls_crm_defender table, the last failed temptative record with result = "Locked Out"
	 *
	 * @bean (array)
	 * @event (string)
	 * @arguments (string)
	 */
	function updateHTAccess($bean, $event, $arguments=null){
		require_once('modules/LS_CRM_Defender/license/OutfittersLicense.php');
		$validate_license = OutfittersLicense::isValid('LS_CRM_Defender');
		//if($validate_license === true) {
		if(true) {

			global $sugar_config;
			if (isset($sugar_config['LS_CRM_Defender_maxFailedAccesses']) && ($sugar_config['LS_CRM_Defender_maxFailedAccesses']!=''))
				$maxFailedAccesses = $sugar_config['LS_CRM_Defender_maxFailedAccesses'];
				else $maxFailedAccesses = 100;

				$ip_address = $_SERVER['REMOTE_ADDR'];
				$control = 1;

				// If ip address is 127.0.0.1 and free_localhost = true => no control
				if (
					(($ip_address == '127.0.0.1')||($ip_address == '::1')) &&
					isset($sugar_config['LS_CRM_Defender_free_localhost']) &&
					$sugar_config['LS_CRM_Defender_free_localhost'])
					$control = 0;

					// If ip_address is whitelisted
					elseif ( (isset($sugar_config['LS_CRM_Defender_whiteList'])) && (strpos($sugar_config['LS_CRM_Defender_whiteList'],$ip_address)!== false) )
					$control = 0;

					if ($control){
						$timestamp = gmdate('Y-m-d H:i:s');
						$day = explode (" ", $timestamp);
						$day = $day[0];
						$dayAfter= date('Y-m-d H:i:s', strtotime($timestamp . ' +1 day'));
						$dayAfter = explode (" ", $dayAfter);
						$dayAfter = $dayAfter[0];

						global $db;
						$res = $db->query("SELECT COUNT(ip_address) AS num FROM ls_crm_defender WHERE ip_address = '".$ip_address."' AND date_entered >'".$day." 00:00:00' AND date_entered < '".$dayAfter." 00:00:00' AND result ='Failed' AND deleted=0");
						$row = $res->fetch_assoc();
						$failedAccesses = $row['num']; // Failed accesses in a day from the same ip address
						$GLOBALS['log']->fatal("$ip_address failed access ".$failedAccesses." times today");
						if($failedAccesses > $maxFailedAccesses){
							$this->addToHTAccess($ip_address);
							$this->markRecordLockOut($ip_address);
							if (isset($sugar_config['LS_CRM_Defender_recipientAddressEnabled']) && ($sugar_config['LS_CRM_Defender_recipientAddressEnabled']) ){
								require_once 'EmailNotifications.php';
								$emailObj = new EmailNotifications();
								$emailObj->sendEmail($ip_address);
							} //if LS_CRM_Defender_recipientAddressEnabled
						} //if failedAccesses

					} //if control
		} //if validate_license
	} //updateHTAccess

	/*
	 * Function htaccess_look
	 *
	 * launched by SaveConfig.php
	 * Look in the htaccess file
	 * Search for the ip address
	 * return line number if found
	 *
	 * @ip (string)
	 */
	function htaccess_look ($ip){
		$line_number = false;
		$filename = '.htaccess';
		if ($handle = fopen($filename, 'r')) { //Open for reading only; place the file pointer at the beginning of the file.
			$count = 0;
			while (($line = fgets($handle, 4096)) !== FALSE and !$line_number) {
				$count++;
				$line_number = (strpos($line, $ip) !== FALSE) ? $count : $line_number;
			} //while
			fclose($handle);
		} //if
		return $line_number;
	} //function htaccess_look

} //class monitorAccesses
?>
