<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

if (!defined("IN_MYBB"))
{
die ("You can directly not access this file ");
}
$plugins->add_hook("admin_user_menu", "myzpzg_plugin_admin_cp");
$plugins->add_hook('admin_user_action_handler', 'myzpzg_handle');



function mybb_zarinpalzg_info()
{
 return array(
 
    "name"  => "عضویت آنی پس از پرداخت (زرین پال) زرین گیت",
	"description"   => "اين پلاگين بلافاصله پس از پرداخت توسط کاربر او را به گروه کاربري مورد نظر انتقال مي دهد.",
	"website"   => "http://www.MyBBIran.com/",
	"author"   => "Mohammad Zangeneh",
	"authorsite"   => "http://www.MyBBIran.com/",
	"version"    => "4.1",
	"compatibility"   => "16*",
	);
}

function mybb_zarinpalzg_install()
{
global $db, $mybb, $settings;

     $settings_group = array(
        'gid'          => NULL,
        'name'         => 'myzps',
        'title'        => 'تنظيمات پلاگين درگاه پرداخت زرين پال',
        'description'  => '',
        'disporder'    => $rows++,
        'isdefault'    => 'no'
    );
    $db->insert_query('settinggroups', $settings_group);
    $gid = $db->insert_id();

	$myzp2= array(
	'name' => 'myzp_merchant',
	'title' =>'MerchantID',
	'description' =>'MerchantID ای را که از سایت زرین پال دریافت کرده اید در این قسمت وارد کنید. (یک کد 32 رقمی با خط فاصله است)',
	'optionscode' => 'text',
	'value' =>'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX',
	'disporder' => 2,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp2);
	
	$myzp3 = array(
	'name' => 'myzp_uid',
	'title' =>'شناسه کاربر مدیرکل',
	'description' =>'شناسه کاربری مدیر کل که پیام خصوصی پس از پرداخت توسط او به کاربر ارسال می شود.',
	'optionscode' => 'text',
	'value' =>'1',
	'disporder' => 3,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp3);
	$myzp4 = array(
	'name' => 'myzp_pm',
	'title' =>'متن پیام خصوصی',
	'description' =>"پیام خصوصی‌ای که پس از عضویت کاربر در سایت به عنوان <strong>سند</strong> به او ارسال می‌شود. (BBCODE) </br>
	راهنما : {username} = نام کاربری | {group} = گروه جدید | {refid} = شماره تراکنش | {expdate} = تاریخ پایان عضویت | {bank} = افزایش موجودی دربانک </br>
	<script type=\"text/javascript\">
function insertText(value, textarea)
{
	// Internet Explorer
	if(document.selection)
	{
		textarea.focus();
		var selection = document.selection.createRange();
		selection.text = value;
	}
	// Firefox
	else if(textarea.selectionStart || textarea.selectionStart == \'0\')
	{
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		textarea.value = textarea.value.substring(0, start)	+ value	+ textarea.value.substring(end, textarea.value.length);
	}
	else
	{
		textarea.value += value;
	}
}
</script>
<br />
<b onclick=\"insertText(\'{username}\', $(\'setting_myzp_pm\'));\">{username}</b>
<b onclick=\"insertText(\'{group}\', $(\'setting_myzp_pm\'));\">{group}</b>
<b onclick=\"insertText(\'{refid}\', $(\'setting_myzp_pm\'));\">{refid}</b>
<b onclick=\"insertText(\'{expdate}\', $(\'setting_myzp_pm\'));\">{expdate}</b>
<b onclick=\"insertText(\'{bank}\', $(\'setting_myzp_pm\'));\">{bank}</b>",

	'optionscode' => 'textarea',
	'value' =>'[B]{username}[/B] گرامی، درود!
 عضویت شما در گروه [B]{group}[/B] انجام شد و شما به این گروه منتقل شدید. 
 شماره ی تراکنش شما: [B]{refid}[/B] 
 تاريخ پايان عضويت شما: [B]{expdate}[/B]
 مقدار افزایش موجودی در بانک: [B]{bank}[/B]
 ',
	'disporder' => 5,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp4);

	$myzp5 = array(
	'name' => 'myzp_soap',
	'title' =>'Soap / NuSoap',
	'description' =>'در صورتی که Soap روی سرور شما فعال نیست ، NuSoap را انتخاب کنید . (در صورتی که در این مورد اطلاعی ندارید ، NuSoap را انتخاب کنید)',
	'optionscode' => 'select\n0=Soap\n1=NuSoap',
	'value' =>'0',
	'disporder' => 7,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp5);
	
	$myzp6 = array(
	'name' => 'myzp_note',
	'title' =>'پیام پس از عضویت',
	'description' =>"پیامی که بلافاصله پس از انتقال کاربر از زرین پال به سایت شما به او نمایش داده می‌شود. (HTML) </br>
	راهنما : {username} = نام کاربری | {group} = گروه جدید | {refid} = شماره تراکنش | {expdate} = تاریخ پایان عضویت | {bank} = افزایش موجودی دربانک </br>
	<script type=\"text/javascript\">
function insertText(value, textarea)
{
	// Internet Explorer
	if(document.selection)
	{
		textarea.focus();
		var selection = document.selection.createRange();
		selection.text = value;
	}
	// Firefox
	else if(textarea.selectionStart || textarea.selectionStart == \'0\')
	{
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		textarea.value = textarea.value.substring(0, start)	+ value	+ textarea.value.substring(end, textarea.value.length);
	}
	else
	{
		textarea.value += value;
	}
}
</script>
<br />
<b onclick=\"insertText(\'{username}\', $(\'setting_myzp_note\'));\">{username}</b>
<b onclick=\"insertText(\'{group}\', $(\'setting_myzp_note\'));\">{group}</b>
<b onclick=\"insertText(\'{refid}\', $(\'setting_myzp_note\'));\">{refid}</b>
<b onclick=\"insertText(\'{expdate}\', $(\'setting_myzp_note\'));\">{expdate}</b>
<b onclick=\"insertText(\'{bank}\', $(\'setting_myzp_note\'));\">{bank}</b>",

	'optionscode' => 'textarea',
	'value' =>'<strong>{username}</strong> گرامی٬ از عضویت شما در گروه <strong>{group}</strong> سپاس گزاریم! </br> عضویت شما در این گروه انجام شد و شما به این گروه منتقل شدید. </br> اطلاعات عضویت شما:</br>
نام کاربری: <strong>{username}</strong> </br> گروه: <strong>{group}</strong> </br> شماره تراکنش: <strong>{refid}</strong> </br>تاریخ پایان عضویت: <strong>{expdate}</strong> </br>مقدار افزایش موجودی در بانک: <strong>{bank}</strong> </br> ضمناً یک پیام خصوصی به عنوان <strong>سند</strong> برای شما ارسال شد. لطفاً این پیام را برای اطمنیان نزد خود نگه دارید.</br>
با سپاس از عضویت شما.',
	'disporder' => 6,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp6);
	
	$myzp7 = array(
	'name' => 'myzp_ban',
	'title' =>'اعضای محروم',
	'description' =>'شناسه کاربرانی را که می‌خواهید از خرید بسته‌ها محروم شوند را وارد کنید. (به وسیله‌ی کاما(,) متمایز کنید)',
	'optionscode' => 'text',
	'value' =>'',
	'disporder' => 4,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp7);

	$myzp8 = array(
	'name' => 'myzp_bang',
	'title' =>'گروه‌های محروم',
	'description' =>'شناسه گروه‌هایی را که می‌خواهید از خرید بسته‌ها محروم شوند را وارد کنید. (به وسیله‌ی کاما(,) متمایز کنید)',
	'optionscode' => 'text',
	'value' =>'7,5',
	'disporder' => 4,
	'gid' => intval($gid));
    $db->insert_query('settings',$myzp8);

	
		 $myzp_task = array(
			"title" => "بررسی ابطال عضویت اعضای گروه ویژه",
			"description" => "این وظیفه که هر‌دقیقه اجرا می‌شود٬ ابطال عضویت اعضای گروه ویژه را بررسی می‌کند",
			"file" => "myzp",
			"minute" => '0',
			"hour" => '*',
			"day" => '*',
			"month" => '*',
			"weekday" => '*',
			"enabled" => '1',
			"logging" => '1',
			"nextrun" => time()
		);
	 $db->insert_query("tasks", $myzp_task);

	
	    rebuildsettings();


	$db->write_query("CREATE TABLE `".TABLE_PREFIX."myzp` (
	  `num` bigint(30) UNSIGNED NOT NULL auto_increment,
	  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
	  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	  `time` varchar(1) NOT NULL default '',
	  `period` int(5) UNSIGNED NOT NULL default '0',
	  `price` int(5) UNSIGNED NOT NULL default '0',
	  `group` smallint(5) UNSIGNED NOT NULL default '0',
	  `bank` int(5) UNSIGNED NOT NULL default '0',	  
	  PRIMARY KEY  (`num`)
	  ) ENGINE=MyISAM");
	  
	  	$db->write_query("CREATE TABLE `".TABLE_PREFIX."myzp_tractions` (
	  `tid` bigint(30) UNSIGNED NOT NULL auto_increment,
	  `packnum` bigint(30) UNSIGNED NOT NULL default '0',
	  `uid` int(10) UNSIGNED NOT NULL default '0',
	  `gid` smallint(5) UNSIGNED NOT NULL default '0',
	  `pgid` smallint(5) UNSIGNED NOT NULL default '0',
	  `stdateline` bigint(30) UNSIGNED NOT NULL default '0',	  
	  `dateline` bigint(30) UNSIGNED NOT NULL default '0',
	  `trackid` int UNSIGNED NOT NULL default '0',	  
	  `payed` int(5) UNSIGNED NOT NULL default '0',
	  `stauts` int(5) UNSIGNED NOT NULL default '0',

	  PRIMARY KEY  (`tid`)
	  ) ENGINE=MyISAM");


}	  
	 function mybb_zarinpalzg_is_installed()
{
	global $db;
		return $db->table_exists("myzp");
}

function mybb_zarinpalzg_uninstall()
{
global $db;
	if ($db->table_exists('myzp'))
	{
		$db->drop_table('myzp');
		}
		if ($db->table_exists('myzp_tractions'))
	{
		$db->drop_table('myzp_tractions');
		}	
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_activation', 'myzps')");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_uid', 'myzps')");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_merchant', 'myzps')");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_pm', 'myzps')");	
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_soap', 'myzps')");
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_note', 'myzps')");	
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_ban', 'myzps')");										
        $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN('myzp_bang', 'myzps')");										
		$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups where name='myzps'");
	    $db->delete_query("tasks", "file='myzp'");

		rebuildsettings();
}
 
function mybb_zarinpalzg_activate()

{
global $db, $template, $lang;

	$tmp_list = array(
		"title" => 'myzp_list',
		"template" => $db->escape_string('
<html>
<head>
<title>بسته های عضویت ویژه</title>
{$headerinclude}
</head>
<body>
{$header}
{$note}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="10"><strong>بسته های عضویت ویژه</strong></td>
</tr>
<tr>
<td class="tcat" width="25%"><strong>نام بسته</strong></td>
<td class="tcat" width="25%"><strong>توضیحات</strong></td>
<td class="tcat" width="15%" align="center"><strong>گروه کاربری</strong></td>
<td class="tcat" width="15%" align="center"><strong>مدت زمان عضویت</strong></td>
<td class="tcat" width="15%" align="center"><strong>هزینه عضویت</strong></td>
<td class="tcat" width="15%" align="center"><strong>خرید</strong></td>
</tr>
{$list}
</table>
</br>
<div align="center" class="smalltext">Powered by <a href="http://www.mybbiran.com">MyBBIran</a> ZarinPal Instant Payment Plugin</div>
{$footer}
</body>
</html>'),
		"sid" => "-1",
		);
	$db->insert_query("templates", $tmp_list);

	$tmp_table = array(
		"title" => 'myzp_list_table',
		"template" => $db->escape_string('<html>
<tr>
<td class="{$bgcolor}" width="25%"><strong>{$myzp[\'title\']}</strong></td>
<td class="{$bgcolor}" width="25%">{$myzp[\'description\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$myzp[\'usergroup\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$myzp[\'period\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$myzp[\'price\']}</td>
<td class="{$bgcolor}" width="15%" align="center">{$buybutton}</td>
</tr>'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_table);
	
	$tmp_emp = array(
		"title" => 'myzp_no_list',
		"template" => $db->escape_string('<html>
<tr>
<td class="trow1" width="100%" colspan="10">در حال حاضر بسته ی عضویت ویژه ای در این انجمن ثبت نشده است.</td>
</tr>'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_emp);
	
	$tmp_info = array(
		"title" => 'myzp_payinfo',
		"template" => $db->escape_string('<html>
<head>
<title>{$mybb->settings[bbname]} - گزارش پرداخت</title>
{$headerinclude}
</head>
<body>
{$header}
<br />

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>گزارش پرداخت</strong></td>
</tr>
<tr>
<td class="trow1" colspan="6">
{$info}
</td>
</tr>
</table>
{$footer}
</body>
</html>
'),
		"sid" => "-1",
		);

	$db->insert_query("templates", $tmp_info);
	
	}
	


function mybb_zarinpalzg_deactivate()	
{
global $db;
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='myzp_list'");
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='myzp_list_table'");
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='myzp_no_list'");	
    $db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='myzp_payinfo'");	
	
	rebuildsettings();
}

function myzpzg_plugin_admin_cp($sub_menu)
{
	global $mybb, $lang;
			
		end($sub_menu);
		$key = (key($sub_menu))+10;
		
		if(!$key)
		{
			$key = '20';
		}
		
		$sub_menu[$key] = array('id' => 'zarinpal', 'title' => "بسته های عضویت آنی (زرین پال)", 'link' => "index.php?module=user-zarinpal");
     return $sub_menu;
}

function myzpzg_handle($action)
{
	$action['zarinpal'] = array('active' => 'zarinpal', 'file' => 'zarinpal.php');
	return $action;
}

?>