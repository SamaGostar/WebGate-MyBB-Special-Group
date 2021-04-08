<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Armin Zahedi @ MyBBIran @ Iran 
*/
	
	define("IN_MYBB", "1");
	require("./global.php");	
	global $mybb;
	$ui = $mybb->user['uid'];
	$ug = $mybb->user['usergroup'];
	
	if (!$mybb->user['uid'])
	{
	error_no_permission();
	}
	$ban = explode(",",$mybb->settings['myzp_ban']) ;
	if(in_array($ui,$ban))
	{
	error_no_permission();
	}
	$bang = explode(",",$mybb->settings['myzp_bang']) ;
	if(in_array($ug,$bang))
	{
	error_no_permission();
	}
	
$query = $db->simple_select('usergroups', 'title, gid', '', array('order_by' => 'gid', 'order_dir' => 'asc'));
while($group = $db->fetch_array($query, 'title, gid'))
{
	$groups[$group['gid']] = $group['title'];
}


$query = $db->simple_select('myzp', '*', '', array('order_by' => 'price', 'order_dir' => 'ASC'));
while ($myzp = $db->fetch_array($query))
{
	$bgcolor = alt_trow();
	$myzp['num'] = intval($myzp['num']);
	$myzp['title'] = htmlspecialchars_uni($myzp['title']);
	$t= " تومان ";
	$myzp['price'] = floatval($myzp['price'])."$t";
	$myzp['usergroup'] = $groups[$myzp['group']];

	if($myzp['time']== 1)
	{
	$time= "روز";
}	
	if($myzp['time']== 2)
	{
	$time= "هفته";
}	
	if($myzp['time']== 3)
	{
	$time= "ماه";
}	
	if($myzp['time']== 4)
	{
	$time= "سال";
}	

	$period = intval($myzp['period']);
	$myzp['period'] = intval($myzp['period'])." ".$time;
	$uid = $mybb->user['uid'];
$query5 = $db->query("SELECT * FROM ".TABLE_PREFIX."myzp_tractions WHERE uid=$uid AND stauts = 1");
$check5 = $db->fetch_array($query5);
if ($check5)
{
$note = "<div class=\"red_alert\">به دلیل اینکه شما قبلاً یکی از این بسته ها را خریداری کرده اید و زمان عضویت شما به پایان نرسیده است ، نمی توانید  بسته ی جدیدی را خریداری نمایید </div>";
$buybutton = "
					<input type='image' src='{$mybb->settings['bburl']}/images/buy-pack.png' border='0'  name='submit'alt='خرید بسته {$myzp['title']}' />";

}
else{
$buybutton = " 							<form action='{$mybb->settings['bburl']}/zarinpal1wg.php' method='post'>
<input type='hidden' name='myzp_num' value='{$myzp['num']}' /> 
					<input type='image' src='{$mybb->settings['bburl']}/images/buy-pack.png' border='0'  name='submit'alt='خرید بسته {$myzp['title']}' />

					</form>
";
	
}	
	eval("\$list .= \"".$templates->get('myzp_list_table')."\";");
}

if (!$list)
{
	eval("\$list = \"".$templates->get('myzp_no_list')."\";");
}

eval("\$myzppage = \"".$templates->get('myzp_list')."\";");
output_page($myzppage);
?>