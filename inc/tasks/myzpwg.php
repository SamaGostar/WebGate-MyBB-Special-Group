<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

if(!defined("IN_MYBB"))
{
	die("You can not directly access this file!");
}

	function task_myzpzg($task)
	{
		global $db, $mybb;
    $ctime = time();
	
	$query = $db->simple_select("myzpzg_tractions", "*", "`stauts` ='1' AND `stauts` != 0 AND `dateline` < '$ctime'");
	
	
	while($myzpzg = $db->fetch_array($query))
	{
	
  $uid = $myzpzg['uid'];
  $pgid = $myzpzg['pgid'];
  $stauts = $myzpzg['stauts'];
  
  $update_array = array (
  'stauts' => '0'
  );

  $update_array1 = array(
  'usergroup' => $pgid
  );
                $db->update_query("users", $update_array1, "`uid` = '$uid'");
                $db->update_query("myzpzg_tractions", $update_array, "`uid` = '$uid'");
		require_once MYBB_ROOT."inc/datahandlers/pm.php";
	$pmhandler = new PMDataHandler();
		$from_id = intval($mybb->settings['myzpzg_uid']);
		$recipients_bcc = array();
		$recipients_to = array(intval($myzpzg['uid']));
        $subject = "پایان عضویت";
		$message = "عضویت شما در گروه ویژه پایان یافت و شما به گروه قبلی خود منتقل شدید.";
		$pm = array(
			'subject' => $subject,
			'message' => $message,
			'icon' => -1,
			'fromid' => $from_id,
			'toid' => $recipients_to,
			'bccid' => $recipients_bcc,
			'do' => '',
			'pmid' => ''
		);
		
		$pm['options'] = array(
			"signature" => 1,
			"disablesmilies" => 0,
			"savecopy" => 1,
			"readreceipt" => 1
		);
	
		$pm['saveasdraft'] = 0;
		$pmhandler->admin_override = true;
		$pmhandler->set_data($pm);
		
		if($pmhandler->validate_pm())
		{
			$pmhandler->insert_pm();
		}
}



$query2 = $db->simple_select("myzpzg_tractions", "*", "`stauts` ='0' AND `stauts` != 1 AND `dateline` > '$ctime'");
	while($myzpzg1 = $db->fetch_array($query2))
	{
	
  $uid1 = $myzpzg1['uid'];
  $pgid1 = $myzpzg1['pgid'];
  $gid1 = $myzpzg1['gid'];
  $stauts1 = $myzpzg1['stauts'];
  
  $update_array1 = array (
  'stauts' => '1'
  );

  $update_array11 = array(
  'usergroup' => $gid1
  );

                $db->update_query("users", $update_array11, "`uid` = '$uid1'");
                $db->update_query("myzpzg_tractions", $update_array1, "`uid` = '$uid1'");
}
		add_task_log($task, "بررسی ابطال عضویت اعضای گروه ویژه");
}


?>	