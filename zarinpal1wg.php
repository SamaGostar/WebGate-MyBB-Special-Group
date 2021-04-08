<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

define("IN_MYBB", "1");
require("./global.php");

if($_SERVER['REQUEST_METHOD']!="POST") die("Forbidden!");

$merchantID = $mybb->settings['myzp_merchant'];
$num = $_POST['myzp_num'];
$query = $db->query("SELECT * FROM ".TABLE_PREFIX."myzp WHERE num=$num");
$myzp = $db->fetch_array($query);
$amount = $myzp['price']; //Amount will be based on Toman
$callBackUrl = "{$mybb->settings['bburl']}/zarinpal_verfywg.php?num={$myzp['num']}";
$desc = "{$myzp['description']}  ({$mybb->user['username']})";

$data = array(
    'merchant_id' => $merchantID,
    'amount' => $amount * 10,
    'description' => $desc,
    'callback_url' => $callBackUrl
);
$jsonData = json_encode($data);

$ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));

$result = curl_exec($ch);
$err = curl_error($ch);
$result = json_decode($result, true, JSON_PRETTY_PRINT);
curl_close($ch);

if($result['data']['code'] == 100){
    echo' <html><body>
                    <script type="text/javascript" src="https://cdn.zarinpal.com/zarinak/v1/checkout.js"></script>
                    <script type="text/javascript">
                    window.onload = function () {
                    Zarinak.setAuthority("' . $result['data']['authority'] . '");
                    Zarinak.showQR();
                    Zarinak.open();
         };
        </script></body></html>';
}else{
    echo'ERR:'.$result['errors']['code'] ;
}
?>
