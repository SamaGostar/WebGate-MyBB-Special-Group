<?

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

	include_once('nusoap.php');

	if($_SERVER['REQUEST_METHOD']!="POST") die("Forbidden!");
	
		define("IN_MYBB", "1");
		require("./global.php");
	if (!$mybb->user['uid'])
	error_no_permission();
		$au = $_GET['Authority'];
	    $merchantID = $mybb->settings['myzp_merchant'];
		$num = $_GET['num'];
	    $query0 = $db->query("SELECT * FROM ".TABLE_PREFIX."myzp WHERE num=$num");
        $myzp0 = $db->fetch_array($query0);
		$amount = $myzp0['price']; 
		$gid = $myzp0['group'];
		$pgid = $mybb->user['usergroup'];
		$uid = $mybb->user['uid'];
		$time = $myzp0['time'];		
		$period = $myzp0['period'];
        $st = $_GET['Status'];
		$bank = $myzp0['bank'];
		if($st == "OK")
		{
					if ($mybb->settings['myzp_soap'] == 0)
				{
						$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', array('encoding'=>'UTF-8'));
						$res = $client->PaymentVerification(
									array(
									'MerchantID'	 => $merchantID ,
									'Authority' 	 => $au ,
									'Amount'	 	 => $amount
								)
						);
				}
				if ($mybb->settings['myzp_soap'] == 1)
				{
					$client = new nusoap_client('https://www.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
					$res = $client->call("PaymentVerification", array(
								array(
									'MerchantID'	 => $merchantID ,
									'Authority' 	 => $au ,
									'Amount'	 	=> $amount
								)
					));
				}
					
						$res = $res->Status;
						$refid = $res->RefID;
		}else{
		$res = 0;
		$refid = 0;
		//$info = "عملیات پرداخت توسط کاربر کنسل شده است";
		}
						
$query1 = $db->simple_select("myzp_tractions", "*", "trackid='$refid'");
$check1 = $db->fetch_array($query1);
if ($check1)
{
$info = "این تراکنش قبلاً ثبت شده است. بنابراین شما نمی‌توانید به صورت غیر مجاز از این سیستم استفاده کنید.";
}
else
{

$query2 = $db->simple_select("myzp", "*", "`num` = '$num'");
while($check = $db->fetch_array($query2))
{
if ($amount != $check['price'])
{
$info = "اطلاعات داده شده اشتباه می باشد . به همین دلیل عضویت انجام نشد.";
}

if($res == -1)
{

$info = "اطلاعات وارد شده کامل نمي باشد . به همين دليل عضويت شما انجام نشد.";
}

if($res == -2)
{

$info = "نحوه دستيابي شما به اين صفحه معتبر نمي باشد . به همين دليل عضويت شما انجام نشد.";
}

if($res == 0)
{

$info = "عمليات پرداخت شما به طور کامل انجام نشده است. به همين دليل عضويت شما انجام نشد.";
}

if($res == -11)
{

$info = "شما از يک تراکنش جعلي براي عضويت استفاده کرده ايد.";
}

if($res == -12)
{

$info = "عمليات پرداخت به طور کامل انجام نشده است . به همين دليل عضويت شما انجام نشد.";
}

if($res == 100)
{
		$query1 = $db->simple_select('usergroups', 'title, gid', '1=1');
        while($group = $db->fetch_array($query1))
{
	$groups[$group['gid']] = $group['title'];
}
		$query5 = $db->simple_select('users', 'username, uid', '');
        while($uname1 = $db->fetch_array($query5, 'username, uid'))
{
	$usname[$uname1['uid']] = $uname1['username'];
}

  }  
if	($time == "1")
{
$dateline = strtotime("+{$period} days");
}

if	($time == "2")
{
$dateline = strtotime("+{$period} weeks");
}
if	($time == "3")
{
$dateline = strtotime("+{$period} months");
}
if	($time == "4")
{
$dateline = strtotime("+{$period} years");
}
$stime = time();
$add_traction = array(
'packnum' => $num,
'uid' => $uid,
'gid' => $gid ,
'pgid' => $pgid ,
'stdateline' => $stime,
'dateline' => $dateline,
'trackid' => $refid,
'payed' => $amount,
'stauts' => "1",
);
if ($db->table_exists("bank_pey") && $bank != 0)
{
	$query7 = $db->simple_select("bank_pey", "*", "`uid` = '$uid'");
    $bankadd = $db->fetch_array($query7);
    $bank_traction = array(
    'uid' => $uid,
    'tid' => 0,
    'pid' => 0,
    'pey' => $bank ,
    'type' => '<img src="'.$mybb->settings['bburl'].'/images/inc.gif">',
    'username' => "مدیریت",
    'time' => $stime,
     'info' => "خرید از درگاه زرین پال",
);
	
		if(!$bankadd)
		{
$add_money = array(
'uid' => $uid,
'username' => $usname[$uid],
'pey' => $bank ,
);
                   $db->insert_query("bank_pey", $add_money);
				   $db->insert_query("bank_buy", $bank_traction);
		}
		if($bankadd)
		{
		$pey = $bankadd['pey'];
		$type='<img src="'.$mybb->settings['bburl'].'/images/inc.gif">';
                   $db->query("update ".TABLE_PREFIX."bank_pey set pey=$pey+$bank where uid=$uid");
                   $db->insert_query("bank_buy", $bank_traction);

		}
		
}
else{
$bank = "0";
}
$db->insert_query("myzp_tractions", $add_traction);
$db->update_query("users", array("usergroup" => $gid), "`uid` = '$uid'");
$expdate = my_date($mybb->settings['dateformat'], $dateline).", ".my_date($mybb->settings['timeformat'], $dateline);
$profile_link = "[url={$mybb->settings['bburl']}/member.php?action=profile&uid={$uid}]{$usname[$uid]}[/url]";
$profile_link1 = build_profile_link($usname[$uid], $uid, "_blank");
$info = preg_replace(
			array(
				'#{username}#',
				'#{group}#',
				'#{refid}#',
				'#{expdate}#',
				'#{bank}#',
				
			),
			array(
				$profile_link1,
				$groups[$gid],
				$refid,
				$expdate,
				$bank,
				
			),
			$mybb->settings['myzp_note']
		);
$username = $mybb->user['username'];
// Notice User By PM
	require_once MYBB_ROOT."inc/datahandlers/pm.php";
	$pmhandler = new PMDataHandler();
		$from_id = intval($mybb->settings['myzp_uid']);
		$recipients_bcc = array();
		$recipients_to = array(intval($uid));
        $subject = "گزارش پرداخت";
		$message = preg_replace(
			array(
				'#{username}#',
				'#{group}#',
				'#{refid}#',
				'#{expdate}#',
				'#{bank}#',
				
			),
			array(
				$profile_link,
				$groups[$gid],
				$refid,
				$expdate,
				$bank,
				
			),
			$mybb->settings['myzp_pm']
		);
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

// Notice Admin By PM
	require_once MYBB_ROOT."inc/datahandlers/pm.php";
	$pmhandler = new PMDataHandler();
	$uidp=$mybb->settings['myzp_uid'];
		$from_id = intval($mybb->settings['myzp_uid']);
		$recipients_bcc = array();
		$recipients_to = array(intval($uidp));
        $subject = "عضویت کاربر در گروه ویژه";
		$message = preg_replace(
			array(
				'#{username}#',
				'#{group}#',
				'#{refid}#',
				'#{expdate}#',
				'#{bank}#',
				
			),
			array(
				$profile_link,
				$groups[$gid],
				$refid,
				$expdate,
				$bank,
				
			),
			"کاربر [B]{username}[/B] با شماره تراکنش [B]{refid}[/B] در گروه [B]{group}[/B] عضو شد.
			تاریخ پایان عضویت:[B]{expdate}[/B]"
			);
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
}
eval("\$verfypage = \"".$templates->get('myzp_payinfo')."\";");
output_page($verfypage);

?>	