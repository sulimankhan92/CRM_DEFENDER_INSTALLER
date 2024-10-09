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
/*
 * https://community.sugarcrm.com/thread/24086
* */
class EmailNotifications {

	/*
	 * Function sendEmail
	 *
	 * send a message to the admin user
	 *
	 * @ip (string)
	 */
	function sendEmail($ip){
		$result = "";
		//$body ="Alert Intrusion attempt by".$ip;
		$body ="".
"~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~".PHP_EOL.
"CRM Defender Notification System".PHP_EOL.
"~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~".PHP_EOL.
"The ip address: ".$ip." has been Locked Out due to CRM Defender's settings.".PHP_EOL.
PHP_EOL.
"If you are an admin user, you can login anytime and whitelist this IP in admin > Lion Solution CRM Defender > CRM Defender's settings.".PHP_EOL.
"If the blocked IP is yours, log into your CRM system from a different IP, using e.g. a mobile connection or a free proxy server.";
				
		$subject="CRM Defender Notification System: ".$ip." has been Locked Out";
		//$recipientAddress="mayer@lionsolution.it"; //admin user
		$adminUser = new User();
		$adminUser->retrieve('1');
		global $sugar_config;
		if (isset($sugar_config['LS_CRM_Defender_recipientAddress'])){
			$recipientAddress = $sugar_config['LS_CRM_Defender_recipientAddress'];
		} //if
		else {
			$recipientAddress = $adminUser->emailAddress->getPrimaryAddress($adminUser);
		} //else
		$toName="CRM Administrator";

		require_once('include/SugarPHPMailer.php');
		include_once('include/utils/db_utils.php'); // for from_html function

		$emailObj = new Email();
		$defaults = $emailObj->getSystemDefaultEmail();

		$mail = new SugarPHPMailer();
		$mail->setMailerForSystem();

		$mail->From = $defaults['email'];
		$mail->FromName = $defaults['name'];
		$mail->Subject = $subject;
		$mail->Body = $body;


		// Clear recipients
		$mail->ClearAllRecipients();
		$mail->ClearReplyTos();
		// Add recipient
		$mail->AddAddress($recipientAddress, $toName);

		$mail->prepForOutbound();
		//Send mail, log if there is error
		$result = $mail->Send();
		if (!$result) {
			$GLOBALS['log']->fatal("CRM Defender Notification System ERROR: Mail sending failed");
		} //if failed
		//return $result;
	} //function sendEmail

} //class EmailNotifications

