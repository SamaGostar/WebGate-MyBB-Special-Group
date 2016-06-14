<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

	include_once('nusoap.php');
	define("IN_MYBB", "1");
	require("./global.php");
	
	if($_SERVER['REQUEST_METHOD']!="POST") die("Forbidden!");

	$merchantID = $mybb->settings['myzp_merchant'];
	$num = $_POST['myzp_num'];
	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."myzp WHERE num=$num");
    $myzp = $db->fetch_array($query);
	$amount = $myzp['price']; //Amount will be based on Toman
	$callBackUrl = "{$mybb->settings['bburl']}/zarinpal_verfy.php?num={$myzp['num']}";
	$desc = "{$myzp['description']}  ({$mybb->user['username']})";
	
if ($mybb->settings['myzp_soap'] == 0)
{
	$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding'=>'UTF-8'));
	$res = $client->PaymentRequest(
	array(
					'MerchantID' 	=> $merchantID ,
					'Amount' 		=> $amount ,
					'Description' 	=> $desc ,
					'Email' 		=> '' ,
					'Mobile' 		=> '' ,
					'CallbackURL' 	=> $callBackUrl

		)
	);
}
if ($mybb->settings['myzp_soap'] == 1)
{
	$client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
	$res = $client->call('PaymentRequest', array(
			array(
					'MerchantID' 	=> $merchantID ,
					'Amount' 		=> $amount ,
					'Description' 	=> $desc ,
					'Email' 		=> '' ,
					'Mobile' 		=> '' ,
					'CallbackURL' 	=> $callBackUrl

		)
	
	
	));
}
	
	if($res->Status == 100){
	Header('Location: https://www.zarinpal.com/pg/StartPay/' . $res->Authority . '/ZarinGate');	
	}else{
		echo'ERR:'.$res->Status ;
	}
?>
