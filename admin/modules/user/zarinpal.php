<?php

/* MyBB Instant Payment By Zarinpal Ver:4.1
Author : Mohammad Reza Zangeneh @ MyBBIran @ Iran 
*/

if(!defined("IN_MYBB"))
{
	die("Forbidden!");
}

$page->add_breadcrumb_item("پرداخت آنلاين زرين پال", "index.php?module=user-zarinpal");
	switch ($mybb->input['action'])
	{

	case "add_pack":
	$nav = "add_pack";
    break;
	case "edit_pack":
	$nav = "edit_pack";
	break;
	case "delete_pack":
	$nav = "delete_pack";
    break;
	case "tractions":
	$nav = "tractions";
    break;
	case "manual_add":
	$nav = "manual_add";
    break;
	default:
    $nav = "packs";

	}
	
	$page->output_header("بسته های عضویت آنی (زرین پال)");

	$sub_tabs['packs'] = array(
		'title' => 'بسته های عضویت آنی (زرین پال)',
		'link' => "index.php?module=user-zarinpal",
		'description' => 'در این بخش می توانید بسته های عضویت ویژه را مدیریت کنید.'
	);

	$sub_tabs['add_pack'] = array(
		'title' => 'افزودن بسته ی تازه',
		'link' => "index.php?module=user-zarinpal&amp;action=add_pack",
		'description' => 'در این بخش می توانید بسته ی جدید عضویت ویژه را اضافه کنید.'
		
	);
	
if($mybb->input['action'] == "edit_pack")
{
	$sub_tabs['edit_pack'] = array(
		'title' => 'ویرایش بسته',
		'link' => "index.php?module=user-zarinpal&amp;action=edit_pack",
		'description' => 'در این بخش می توانید بسته ی جدید عضویت ویژه را ویرایش کنید.'
		
	);
}

	$sub_tabs['manual_add'] = array(
		'title' => 'افزودن دستی تراکنش',
		'link' => "index.php?module=user-zarinpal&amp;action=manual_add",
		'description' => 'در این بخش می توانید تراکنش مورد نظر را برای کاربر، به صورت دستی در مواقع مورد نیاز وارد کنید.'
		
	);

	$sub_tabs['tractions'] = array(
		'title' => 'تراکنش های انجام شده',
		'link' => "index.php?module=user-zarinpal&amp;action=tractions",
		'description' => 'در این بخش می توانید تراکنش هایی که تا این لحظه انجام شده اند را مشاهده کنید.'
		
	);
	

	$page->output_nav_tabs($sub_tabs, $nav);
	
//overview	
	if(!$mybb->input['action'])
{
	$per_page = 20;

	if($mybb->input['page'] && $mybb->input['page'] > 1)
	{
		$mybb->input['page'] = intval($mybb->input['page']);
		$start = ($mybb->input['page']*$per_page)-$per_page;
	}
	else
	{
		$mybb->input['page'] = 1;
		$start = 0;
	}

		$table = new Table;
		$table->construct_header("نام بسته", array('class' => 'align_left', width => '15%'));
		$table->construct_header("توضیحات بسته", array('class' => 'align_left', width => '15%'));
		$table->construct_header("مدت زمان عضویت", array('class' => 'align_left', width => '7%'));				
	    $table->construct_header("قیمت بسته", array('class' => 'align_left', width => '5%'));
	    $table->construct_header("گزینه ها", array('class' => 'align_center', width => '5%'));

		$query = $db->query("SELECT *
		FROM ".TABLE_PREFIX."myzp
		WHERE 1=1
		ORDER BY num ASC
		LIMIT {$start}, {$per_page};");

		while($over = $db->fetch_array($query))
		{
if ($over['time'] == 1)
{
$d = "روز";
}
if ($over['time'] == 2)
{
$d = "هفته";
}
if ($over['time'] == 3)
{
$d = "ماه";
}
if ($over['time'] == 4)
{
$d = "سال";
}
		$pname=$over['title'];
		$table->construct_cell("<strong><a href=\"index.php?module=user-zarinpal&amp;action=edit_pack&amp;num={$over['num']}\">{$over['title']}</a></strong></td><td>{$over['description']}</td><td>{$over['period']} {$d}  </td><td>{$over['price']} تومان</td>");
		$popup = new PopupMenu("sub_{$over['num']}", $lang->options);
		$popup->add_item($lang->delete, "index.php?module=user-zarinpal&amp;action=delete_pack&amp;num={$over['num']}");
		$popup->add_item($lang->edit, "index.php?module=user-zarinpal&amp;action=edit_pack&amp;num={$over['num']}");


		$table->construct_cell($popup->fetch(), array("class" => "align_center"));

				$table->construct_row();

		}
			if ($table->num_rows() == 0)
			{
				$table->construct_cell("در حال حاضر بسته ی عضویتی در این انجمن ثبت نشده است.", array('colspan' => 5));
				$table->construct_row();
			}
	$querypage = $db->simple_select("myzp", "COUNT(num) as packs", "1=1");
	$total_rows = $db->fetch_field($querypage, "packs");
			
	$pagination = draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=user-zarinpal&amp;page={page}");
	echo $pagination;					
						$table->output("بسته های عضویت ویژه");

}	
	
// Add Subs
	if($mybb->input['action'] == "add_pack")
{
global $db;
// RQM = post
if($mybb->request_method=="post")
{

	if(!$mybb->input['packname'])
{
			$errors[] = "نامی برای بسته وارد نشده‌است. لطفاً یک نام برای بسته وارد کنید.";

}

	if(!$mybb->input['packdesc'])
{
			$errors[] = "توضیحاتی برای بسته وارد نشده‌است. لطفاً یک توضیح برای بسته وارد کنید.";

}

	if(!$mybb->input['packprice'] || !is_numeric($mybb->input['packprice']))
{
			$errors[] = "قیمتی برای بسته وارد نشده‌است. لطفاً یک قیمت برای بسته وارد کنید.";
}

	if(!$mybb->input['group'])
{
			$errors[] = "گروه کاربری معتبری انتخاب نشده‌است. لطفاً یک گروه کاربری معتبر را از لیست برگزینید.";
}

	if(is_numeric($mybb->input['packprice'])&&floatval($mybb->input['packprice'])<1000)
{
			$errors[] = "حد‌اقل قیمت بسته با توجه به قوانین زرین پال باید 1000 تومان باشد.";
}
	if(!$mybb->input['period'])
{
			$errors[] = "طول دوره‌ی زمانی وارد نشده‌است. لطفاً طول دوره‌ی زمانی را مشخص کنید.";
}
	if(!$mybb->input['time'])
{
			$errors[] = "دوره‌ی زمانی وارد نشده‌است. لطفاً دوره‌ی زمانی مورد نظر را از لیست برگزینید.";
}
if(!$errors)
{
$insert_query = array(
'title' => $db->escape_string($mybb->input['packname']),
'description' => $db->escape_string($mybb->input['packdesc']),
'price' => floatval($mybb->input['packprice']),
'bank' => floatval($mybb->input['packbank']),
'group' => intval($mybb->input['group']),
'time' => intval($mybb->input['time']),
'period' => intval($mybb->input['period']),
);
$db->insert_query("myzp", $insert_query);
					flash_message('بسته ی مورد نظر با موفقیت ذخیره شد', 'success');
					admin_redirect("index.php?module=user-zarinpal");
					}
}

//\ RQM post
// User Groups
			$query = $db->simple_select('usergroups', '*', '1=1', array('order_by' => 'gid', 'order_dir' => 'ASC'));
			while($group = $db->fetch_array($query))
			{
				$groups[$group['gid']] = $group['title'];
			}
// Time
			$times['1'] = "روز";
			$times['2'] = "هفته";
			$times['3'] = "ماه";
			$times['4'] = "سال";


		$form = new Form("index.php?module=user-zarinpal&amp;action=add_pack", "post");
		if($errors)
	{
		$page->output_inline_error($errors);
	}

		$form_container = new FormContainer('افزودن بسته ی تازه');

	$form_container->output_row("نام بسته <em>*</em>", "", $form->generate_text_box('packname',  $mybb->input['packname'], array('id' => 'packname')), 'packname');

	$form_container->output_row("توضیحات بسته <em>*</em>", "", $form->generate_text_box('packdesc', $mybb->input['packdesc'], array('id' => 'packdesc')), 'packdesc');

	$form_container->output_row("گروه مورد نظر<em>*</em>", "گروهی که می خواهید کاربرانی این بسته را خریداری می کنند به آن منتقل شوند را انتخاب کنید.", $form->generate_select_box('group', $groups, $mybb->input['group'], array('id' => 'group')), 'group');

	$form_container->output_row("واحد دوره ی عضویت<em>*</em>", "واحد دوره ی زمانی ای را که می خواهید تعداد (روز - هفته - ماه -سال ) براساس آن باشد را انتخاب کنید.", $form->generate_select_box('time', $times, $mybb->input['time'], array('id' => 'time')), 'time');
	
	$form_container->output_row("مدت<em>*</em>", "تعداد (روز - هفته - ماه - سال) را وارد کنید.", $form->generate_text_box('period', $mybb->input['period'], array('id' => 'period')), 'period');
	
	$form_container->output_row("هزینه عضویت (به تومان) <em>*</em>", "", $form->generate_text_box('packprice', $mybb->input['packprice'], array('id' => 'packprice')), 'packprice');
if ($db->table_exists('bank_pey'))
{
	$form_container->output_row("مقدار افزایش موجودی در بانک", "در صورتی که می‌خواهید موجودی کاربرانی که این بسته را خریداری می‌کنند در پلاگین بانک افزایش یابد، پر کنید.", $form->generate_text_box('packbank', $mybb->input['packbank'], array('id' => 'packbank')), 'packbank');
}
	


		$form_container->end();
		$form_container->construct_row();
	
		$buttons[] = $form->generate_submit_button("ثبت");
		$form->output_submit_wrapper($buttons);
		$form->end();	
					
}

// Edit Subs
if($mybb->input['action'] == "edit_pack")
{
// RQM post
if($mybb->request_method == "post")
{
if	($mybb->input['time'] == "1")
{
$dateline = strtotime("+{$mybb->input['period']} days");
}
if	($mybb->input['time'] == "2")
{
$dateline = strtotime("+{$mybb->input['period']} weeks");
}
if	($mybb->input['time'] == "3")
{
$dateline = strtotime("+{$mybb->input['period']} months");
}
if	($mybb->input['time'] == "4")
{
$dateline = strtotime("{$mybb->input['period']} years");
}

	if(!$mybb->input['packname'])
{
			$errors[] = "نامی برای بسته وارد نشده‌است. لطفاً یک نام برای بسته وارد کنید.";

}

	if(!$mybb->input['packdesc'])
{
			$errors[] = "توضیحاتی برای بسته وارد نشده‌است. لطفاً یک توضیح برای بسته وارد کنید.";

}

	if(!$mybb->input['packprice'] || !is_numeric($mybb->input['packprice']))
{
			$errors[] = "قیمتی برای بسته وارد نشده‌است. لطفاً یک قیمت برای بسته وارد کنید.";
}

	if(!$mybb->input['group'])
{
			$errors[] = "گروه کاربری معتبری انتخاب نشده‌است. لطفاً یک گروه کاربری معتبر را از لیست برگزینید.";
}

	if(is_numeric($mybb->input['packprice'])&&floatval($mybb->input['packprice'])<1000)
{
			$errors[] = "حد‌اقل قیمت بسته با توجه به قوانین زرین پال باید 1000 تومان باشد.";
}
	if(!$mybb->input['period'])
{
			$errors[] = "طول دوره‌ی زمانی وارد نشده‌است. لطفاً طول دوره‌ی زمانی را مشخص کنید.";
}
	if(!$mybb->input['time'])
{
			$errors[] = "دوره‌ی زمانی وارد نشده‌است. لطفاً دوره‌ی زمانی مورد نظر را از لیست برگزینید.";
}
if(!$errors)
{
$num = intval($mybb->input['num']);		
$update_array = array(
'title' => $db->escape_string($mybb->input['packname']),
'description' => $db->escape_string($mybb->input['packdesc']),
'price' => floatval($mybb->input['packprice']),
'bank' => floatval($mybb->input['packbank']),
'group' => intval($mybb->input['group']),
'time' => intval($mybb->input['time']),
'period' => intval($mybb->input['period']),


					);
					$db->update_query('myzp', $update_array, 'num=\''.intval($num).'\'');
					flash_message("بسته ی مورد نظر با موفقیت ویرایش شد.", 'success');
					admin_redirect("index.php?module=user-zarinpal");
}
}

//\ RQM post

// User Groups
			$query = $db->simple_select('usergroups', '*', '1=1', array('order_by' => 'gid', 'order_dir' => 'ASC'));
			while($group = $db->fetch_array($query))
			{
				$groups[$group['gid']] = $group['title'];
			}

// Time
			$times['1'] = "روز";
			$times['2'] = "هفته";
			$times['3'] = "ماه";
			$times['4'] = "سال";


//

    $num = $mybb->input['num'];
	$query = $db->simple_select("myzp","*","num='$num'");
    $edit = $db->fetch_array($query);

	$form = new Form("index.php?module=user-zarinpal&amp;action=edit_pack&amp;num={$num}", "post");
			if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer("ویرایش بسته");
	$form_container->output_row("نام بسته <em>*</em>", "", $form->generate_text_box('packname', $edit['title'], array('id' => 'packname')), 'packname');

	$form_container->output_row("توضیحات بسته <em>*</em>", "", $form->generate_text_box('packdesc', $edit['description'], array('id' => 'packdesc')), 'packdesc');

	$form_container->output_row("گروه مورد نظر<em>*</em>", "گروهی که می خواهید کاربرانی این بسته را خریداری می کنند به آن منتقل شوند را انتخاب کنید.", $form->generate_select_box('group', $groups, intval($edit['group']), array('id' => 'group')), 'group');	

	$form_container->output_row("واحد دوره ی عضویت<em>*</em>", "واحد دوره ی زمانی ای را که می خواهید تعداد (روز - هفته - ماه -سال ) براساس آن باشد را انتخاب کنید.", $form->generate_select_box('time', $times, $edit['time'], array('id' => 'time')), 'time');
	
	$form_container->output_row("مدت<em>*</em>", "تعداد (روز - هفته - ماه - سال) را وارد کنید.", $form->generate_text_box('period', $edit['period'], array('id' => 'period')), 'period');	

	$form_container->output_row("هزینه عضویت (به تومان) <em>*</em>", "", $form->generate_text_box('packprice', $edit['price'], array('id' => 'packprice')), 'packprice');

if ($db->table_exists('bank_pey'))
{
	$form_container->output_row("مقدار افزایش موجودی در بانک", "در صورتی که می‌خواهید موجودی کاربرانی که این بسته را خریداری می‌کنند در پلاگین بانک افزایش یابد، پر کنید.", $form->generate_text_box('packbank', $edit['bank'], array('id' => 'packbank')), 'packbank');
}
	

		$form_container->construct_row();
		$form_container->end();
	
		$buttons[] = $form->generate_submit_button("ثبت");
		$form->output_submit_wrapper($buttons);
		$form->end();

}

//delete subs
if($mybb->input['action'] == "delete_pack")
{
$num=intval($mybb->input['num']);
$db->query("DELETE FROM ".TABLE_PREFIX."myzp WHERE num=$num");
					flash_message("بسته ی مورد نظر با موفقیت حذف شد.", 'success');
					admin_redirect("index.php?module=user-zarinpal");
}

//Tractions
// Filter
	if($mybb->input['action'] == "tractions")
{
	$per_page = 20;

	if($mybb->input['page'] && $mybb->input['page'] > 1)
	{
		$mybb->input['page'] = intval($mybb->input['page']);
		$start = ($mybb->input['page']*$per_page)-$per_page;
	}
	else
	{
		$mybb->input['page'] = 1;
		$start = 0;
	}
	if($mybb->input['ttid'])
	{
		$additional_sql_criteria .= "AND l.tid='{$mybb->input['ttid']}'";
		$additional_criteria[] = "tid={$mybb->input['ttid']}";

	}
	
	if($mybb->input['uname'])
	{
		$query = $db->simple_select("users", "uid, username", "LOWER(username)='".my_strtolower($mybb->input['uname'])."'");
		$user = $db->fetch_array($query);
		if(!$user['uid'])
		{
			flash_message("نام کاربری وارد شده وجود ندارد.", 'error');
			admin_redirect("index.php?module=user-zarinpal&action=tractions");
		}
		$additional_sql_criteria .= "AND l.uid='{$user['uid']}'";
		$additional_criteria[] = "uname=".htmlspecialchars_uni($mybb->input['uname'])."";
	}
	if($mybb->input['payed'])
	{
		$additional_sql_criteria .= "AND l.payed='{$mybb->input['payed']}'";
		$additional_criteria[] = "payed={$mybb->input['payed']}";

		}
if($mybb->input['packid'])
{
		$additional_sql_criteria .= "AND l.packnum='{$mybb->input['packid']}'";
		$additional_criteria[] = "packid={$mybb->input['packid']}";

}		
		if($mybb->input['trackid'])
		{
		$additional_sql_criteria .= "AND l.trackid='{$mybb->input['trackid']}'";
		$additional_criteria[] = "trackid={$mybb->input['trackid']}";
    	}
		
		if($mybb->input['stime']&&$mybb->input['oldnew']&&$mybb->input['unit'])
		{
	{
	$time=strtotime("-{$mybb->input['stime']} days");
	}
		if($mybb->input['unit']==2)
	{
	$time=strtotime("-{$mybb->input['stime']} weeks");
	}
		if($mybb->input['unit']==3)
	{
	$time=strtotime("-{$mybb->input['stime']} months");
	}
		if($mybb->input['unit']==4)
	{
	$time=strtotime("-{$mybb->input['stime']} years");
	}

		if($mybb->input['oldnew']==1)
		{

		$additional_sql_criteria .= "AND l.stdateline<='{$time}'";
		$additional_criteria[] = "stime={$mybb->input['stime']}&amp;oldnew={$mybb->input['oldnew']}&amp;unit={$mybb->input['unit']}";
		}
		if($mybb->input['oldnew']==2)
		{

		$additional_sql_criteria .= "AND l.stdateline>='{$time}'";
		$additional_criteria[] = "stime={$mybb->input['stime']}&amp;oldnew={$mybb->input['oldnew']}&amp;unit={$mybb->input['unit']}";
		}

		}
//End Time Filter
		if($mybb->input['etime']&&$mybb->input['oldnew1']&&$mybb->input['unit1']&&$mybb->input['neper'])
		{
		if($mybb->input['neper']==1)
		{
		$pn='-';
		}
		if($mybb->input['neper']==2)
		{
		$pn='+';
		}
		if($mybb->input['unit1']==1)

	{
	$time=strtotime("{$pn}{$mybb->input['etime']} days");
	}
		if($mybb->input['unit']==2)
	{
	$time=strtotime("{$pn}{$mybb->input['etime']} weeks");
	}
		if($mybb->input['unit1']==3)
	{
	$time=strtotime("{$pn}{$mybb->input['etime']} months");
	}
		if($mybb->input['unit1']==4)
	{
	$time=strtotime("{$pn}{$mybb->input['etime']} years");
	}

		if($mybb->input['oldnew1']==1)
		{

		$additional_sql_criteria .= "AND l.dateline<='{$time}'";
		$additional_criteria[] = "etime={$mybb->input['etime']}&amp;oldnew1={$mybb->input['oldnew1']}&amp;unit1={$mybb->input['unit1']}&amp;neper={$mybb->input['neper']}";
		}
		if($mybb->input['oldnew1']==2)
		{

		$additional_sql_criteria .= "AND l.dateline>='{$time}'";
		$additional_criteria[] = "etime={$mybb->input['etime']}&amp;oldnew1={$mybb->input['oldnew1']}&amp;unit1={$mybb->input['unit1']}&amp;neper={$mybb->input['neper']}";
		}

		}

//\End Time Filter
		
	if($mybb->input['stauts'])
	{
	if($mybb->input['stauts']==1)
	{
		$additional_sql_criteria .= "AND l.stauts='{$mybb->input['stauts']}'";
		$additional_criteria[] = "stauts={$mybb->input['stauts']}";
		}
	if($mybb->input['stauts']==2)
	{
		$additional_sql_criteria .= "AND l.stauts='0'";
		$additional_criteria[] = "stauts={$mybb->input['stauts']}";
		}

		}
	
			if($additional_criteria)
		{
		$additional_criteria = "&amp;".implode("&amp;", $additional_criteria);
		}

	
	//\Filter
		$table = new Table;
		$table->construct_header("شماره", array('class' => 'align_left', width => '1%'));
		$table->construct_header("نام کاربری", array('class' => 'align_left', width => '7%'));
	    $table->construct_header("مبلغ واریزی", array('class' => 'align_left', width => '5%'));
		$table->construct_header("بسته ی خریداری شده", array('class' => 'align_left', width => '15%'));	
	    $table->construct_header("ش. تراکنش", array('class' => 'align_left', width => '5%'));
	    $table->construct_header("تاریخ خرید", array('class' => 'align_left', width => '7%'));
	    $table->construct_header("تاریخ پایان", array('class' => 'align_left', width => '7%'));		
	    $table->construct_header("وضعیت", array('class' => 'align_left', width => '5%'));
		$table->construct_header("گزینه‌ها", array('class' => 'align_left', width => '7%'));

			$query4 = $db->simple_select('myzp', 'num, title', '', array('order_by' => 'num', 'order_dir' => 'asc'));
			while($pack = $db->fetch_array($query4, 'num, title'))
			{
				$packs[$pack['num']] = $pack['title'];
			}
		$query5 = $db->query("SELECT l.*
		FROM ".TABLE_PREFIX."myzp_tractions l
		LEFT JOIN ".TABLE_PREFIX."users r ON (r.uid=l.uid)
		WHERE 1=1 {$additional_sql_criteria}
		ORDER BY l.dateline DESC
		LIMIT {$start}, {$per_page};");
				

		while($track = $db->fetch_array($query5))
		{
	$unamequery = $db->simple_select("users", "username,usergroup,displaygroup", "uid={$track['uid']}");
    while($user = $db->fetch_array($unamequery))
$username = format_name($user['username'], $user['usergroup'], $user['displaygroup']);		
$profile_link = build_profile_link($username, $track['uid'], "_blank");
$pid = $track['packnum'];
$stdate = my_date($mybb->settings['dateformat'], $track['stdateline']).", ".my_date($mybb->settings['timeformat'], $track['stdateline']);
$expdate = my_date($mybb->settings['dateformat'], $track['dateline']).", ".my_date($mybb->settings['timeformat'], $track['dateline']);
if ($track['stauts'] == 1)
{
$st = "<b><font color = \"green\">فعال</font></b>";
}

if ($track['stauts'] == 0)
{
$st = "<b><font color = \"red\">پایان یافته</font></b>";
}
if($track['stauts'] != 0)
{
		$table->construct_cell("{$track['tid']}</td><td>{$profile_link}</td><td>{$track['payed']} تومان</td><td>{$packs[$pid]}</td><td>{$track['trackid']}</td><td>{$stdate}</td><td>{$expdate}</td><td>{$st}</td><td><a href=\"index.php?module=user-zarinpal&amp;action=end&amp;tid={$track['tid']}\">پایان دادن</a></td>");
				$table->construct_row();
				}
else{
		$table->construct_cell("{$track['tid']}</td><td>{$profile_link}</td><td>{$track['payed']} تومان</td><td>{$packs[$pid]}</td><td>{$track['trackid']}</td><td>{$stdate}</td><td>{$expdate}</td><td>{$st}</td><td><a href=\"index.php?module=user-zarinpal&amp;action=deletetrac&amp;tid={$track['tid']}\">حذف</a></td>");
				$table->construct_row();

}				

		}
		
			if ($table->num_rows() == 0)
			{
				$table->construct_cell("در حال حاضر تراکنشی در این انجمن ثبت نشده است.", array('colspan' => 10));
				$table->construct_row();
			}
	$querypage = $db->simple_select("myzp_tractions l", "COUNT(l.tid) as tracs", "1=1 {$additional_sql_criteria}");
	$total_rows = $db->fetch_field($querypage, "tracs");
	$pagination = draw_admin_pagination($mybb->input['page'], $per_page, $total_rows, "index.php?module=user-zarinpal&amp;action=tractions&amp;page={page}{$additional_criteria}");
				echo $pagination;
				echo "<div class = \"float_left\"><a href=\"index.php?module=user-zarinpal&amp;action=deletealltrac\">حذف تمامی تراکنش‌های پایان یافته</a></div>";

							$table->output("تراکنش های ثبت شده");
							
							// Filter Form
	$oldnew['1'] = "قدیمی‌تر از";
	$oldnew['2'] = "جدیدتر از";
	
	$time_types['1'] = "روز";
	$time_types['2'] = "هفته";
	$time_types['3'] = "ماه";
	$time_types['4'] = "سال";

	$packquery = $db->simple_select("myzp", "num,title", "1=1");
	$packid['']="تمامی بسته‌ها";
    while($pack = $db->fetch_array($packquery))
    {
	$packid[$pack['num']]=$pack['title'];
	}
	$stauts['']="همه";
	$stauts['1']="فعال";
	$stauts['2']="پایان یافته";
	
	$neper['1']="قبل";
    $neper['2']="بعد";
	$form = new Form("index.php?module=user-zarinpal&amp;action=tractions", "post");
	$form_container = new FormContainer("پالایش تراکنش‌ها");
	$form_container->output_row("شماره:‌", "", $form->generate_text_box('ttid', $mybb->input['ttid'], array('id' => 'ttid')), 'ttid');	
	$form_container->output_row("نام کاربری:", "", $form->generate_text_box('uname', $mybb->input['uname'], array('id' => 'uname')), 'uname');	
	$form_container->output_row("مبلغ واریزی:", "", $form->generate_text_box('payed', $mybb->input['payed'], array('id' => 'payed')), 'payed');	
	$form_container->output_row("بسته‌ی خریداری شده:", "", $form->generate_select_box('packid', $packid, $mybb->input['packid'], array('id' => 'packid')), 'packid');
	$form_container->output_row("شماره‌ی تراکنش:", "", $form->generate_text_box('‌trackid', $mybb->input['‌trackid'], array('id' => '‌trackid')), '‌trackid');
	$form_container->output_row("تاریخ خرید:", "", $form->generate_select_box('oldnew', $oldnew, $mybb->input['oldnew'])." ".$form->generate_text_box('stime', $mybb->input['stime'], array('id' => 'stime'), 'stime')." ".$form->generate_select_box('unit', $time_types, $mybb->input['unit'])." قبل");
	$form_container->output_row("تاریخ پایان:", "", $form->generate_select_box('oldnew1', $oldnew, $mybb->input['oldnew1'])." ".$form->generate_text_box('etime', $mybb->input['etime'], array('id' => 'etime'), 'etime')." ".$form->generate_select_box('unit1', $time_types, $mybb->input['unit1'])." ".$form->generate_select_box('neper', $neper, $mybb->input['neper'])."");
	$form_container->output_row("وضعیت:", "", $form->generate_select_box('stauts', $stauts, $mybb->input['stauts'], array('id' => 'stauts')), 'stauts');

	
	$form_container->end();
			echo '
		<script type="text/javascript" src="../jscripts/autocomplete.js?ver=1603"></script>
		<script type="text/javascript">
		<!--
			new autoComplete("uname", "../xmlhttp.php?action=get_users", {valueSpan: "username"});
		// -->
	</script>';
	
	$buttons[] = $form->generate_submit_button("پالایش تراکنش‌ها");
	$form->output_submit_wrapper($buttons);
	$form->end();

}
// End Registration
if($mybb->input['action'] == "end")
{
$time = TIME_NOW;
$tid = intval($mybb->input['tid']);

$query = $db->simple_select("myzp_tractions", "*", "tid = $tid");
	while($myzp = $db->fetch_array($query))
	{
	
  $uid = $myzp['uid'];
  $pgid = $myzp['pgid'];
  $stauts = $myzp['stauts'];
  
  $update_array = array (
  'stauts' => '0',
  'dateline' => $time
  );

  $update_array1 = array(
  'usergroup' => $pgid
  );
                $db->update_query("users", $update_array1, "`uid` = '$uid'");
                $db->update_query("myzp_tractions", $update_array, "`tid` = '$tid'");
		require_once MYBB_ROOT."inc/datahandlers/pm.php";
	$pmhandler = new PMDataHandler();
		$from_id = intval($mybb->settings['myzp_uid']);
		$recipients_bcc = array();
		$recipients_to = array(intval($myzp['uid']));
        $subject = "پایان عضویت";
		$message = "عضویت شما در گروه ویژه پایان یافت و شما به گروه پیشین خود بازگشتید.
		اگر در این باره اعتراضی دارید٬ با مدیر‌کل تماس بگیرید.";
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


					flash_message("عضویت مورد نظر با موفقیت پایان یافت.", 'success');
					admin_redirect("index.php?module=user-zarinpal&action=tractions");
}
// Delete Traction
if($mybb->input['action'] == "deletetrac")
{
$tid=intval($mybb->input['tid']);
$db->query("DELETE FROM ".TABLE_PREFIX."myzp_tractions WHERE tid=$tid");
					flash_message("تراکنش مورد نظر با موفقیت حذف شد.", 'success');
					admin_redirect("index.php?module=user-zarinpal&action=tractions");
}
// Delete Traction
if($mybb->input['action'] == "deletealltrac")
{
$db->query("DELETE FROM ".TABLE_PREFIX."myzp_tractions WHERE stauts=0");
					flash_message("تمامی تراکنش‌های پایان یافته با موفقیت حذف شدند.", 'success');
					admin_redirect("index.php?module=user-zarinpal&action=tractions");
}
if($mybb->input['action']=="manual_add")
{
	$packquery = $db->simple_select("myzp", "num,title", "1=1");
    while($packs = $db->fetch_array($packquery))
    {
	$packids[$packs['num']]=$packs['title'];
	}
	if($mybb->request_method == "post")
	{
$query1 = $db->simple_select('usergroups', 'title, gid', '1=1');
while($gr = $db->fetch_array($query1))
{
	$grt[$gr['gid']] = $gr['title'];
}
	
$username = $db->escape_string($mybb->input['uname']);
$payed = intval($mybb->input['payed']);
$packid = intval($mybb->input['packid']);
$trackid = intval($mybb->input['‌trackid']);
$query = $db->simple_select("users", "uid,usergroup", "username='{$username}'");
$user = $db->fetch_array($query);
$uid = $user['uid'];
$pgid = $user['usergroup'];
$queryp = $db->simple_select("myzp", "*", "num={$packid}");
$pack = $db->fetch_array($queryp);
$packprice = $pack['price'];
$gid = $pack['group'];
$time = $pack['time'];
$period = $pack['period'];
$bank = $pack['bank'];
if($payed != $packprice && $payed)
{
$payed = $payed;
}
if($payed == $packprice && $payed || !$payed)
{
$payed = $packprice;
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
$querychk = $db->simple_select("myzp_tractions", "stauts", "uid='{$uid}'");
$check = $db->fetch_array($querychk);

if($check && $check['stauts']==1)
{
			$errors[] = "کاربر مورد نظر پیش‌تر یکی از بسته‌ها را خریداری کرده‌است و عضویت او نیز پایان نیافته‌است. برای ادامه، عضویت پیشین او را پایان دهید و دوباره امتحان کنید.";

}
		if(!$mybb->input['uname'])
		{
			$errors[] = "کاربر مورد نظر وجود ندارد";
		}

		if(!$mybb->input['packid'])
		{
			$errors[] = "بسته‌ی مورد نظر وجود ندارد.";
		}
		
		if(!$mybb->input['‌trackid'])
		{
			$errors[] = "شماره‌ی تراکنش وارد نشده‌است.";
		}

		if(!$errors)
		{
$insert_query = array(
'packnum' => $packid,
'uid' => $uid,
'gid' => $gid,
'pgid' => $pgid,
'stdateline' => TIME_NOW,
'dateline' => $dateline,
'trackid' => $trackid,
'payed' => $payed,
'stauts' => '1'
);
//Add TR
$db->insert_query("myzp_tractions", $insert_query);
//Change UG
if ($db->table_exists("bank_pey") && $bank != 0)
{
	$query7 = $db->simple_select("bank_pey", "*", "`uid` = '$uid'");
    $bankadd = $db->fetch_array($query7);
    $bank_traction = array(
    'uid' => intval($uid),
    'tid' => 0,
    'pid' => 0,
    'pey' => intval($bank),
    'type' => '<img src="'.$mybb->settings['bburl'].'/images/inc.gif">',
    'username' => "مدیریت",
    'time' => TIME_NOW,
     'info' => "خرید از درگاه زرین پال",
);
	
		if(!$bankadd)
		{
$add_money = array(
'uid' => intval($uid),
'username' => $db->escape_string($mybb->input['uname']),
'pey' => intval($bank) ,
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
$db->update_query("users", array("usergroup" => $gid), "`uid` = '$uid'");
$expdate = my_date($mybb->settings['dateformat'], $dateline).", ".my_date($mybb->settings['timeformat'], $dateline);
$profile_link = "[url={$mybb->settings['bburl']}/member.php?action=profile&uid={$uid}]{$username}[/url]";
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
				$grt[$gid],
				$trackid,
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
//Change UG

//Message
		flash_message("تراکنش مورد نظر با موفقیت افزوده شد.", 'success');
		admin_redirect("index.php?module=user-zarinpal&amp;action=tractions");
		}
}
	$form = new Form("index.php?module=user-zarinpal&amp;action=manual_add", "post");
		if($errors)
	{
		$page->output_inline_error($errors);
	}

	$form_container = new FormContainer("افزودن تراکنش به صورت دستی");
	$form_container->output_row("نام کاربری:<em>*</em>", "نام کاربری مورد نظر را وارد کنید.", $form->generate_text_box('uname', $mybb->input['uname'], array('id' => 'uname')), 'uname');	
	$form_container->output_row("مبلغ واریزی:", "مبلغ واریز شده توسط کاربر (<strong><font color=\"red\">تنها در صورتی که مبلغ واریزی با هزینه‌ی بسته یکسان نیست وارد شود</font></strong>).", $form->generate_text_box('payed', $mybb->input['payed'], array('id' => 'payed')), 'payed');	
	$form_container->output_row("بسته‌ی خریداری شده:<em>*</em>", "", $form->generate_select_box('packid', $packids, $mybb->input['packid'], array('id' => 'packid')), 'packid');
	$form_container->output_row("شماره‌ی تراکنش:<em>*</em>", "شماره‌ی تراکنش زرین پال را وارد کنید.", $form->generate_text_box('‌trackid', $mybb->input['trackid'], array('id' => '‌trackid')), '‌trackid');
	$form_container->end();
			echo '
		<script type="text/javascript" src="../jscripts/autocomplete.js?ver=1605"></script>
		<script type="text/javascript">
		<!--
			new autoComplete("uname", "../xmlhttp.php?action=get_users", {valueSpan: "username"});
		// -->
	</script>';
	
	$buttons[] = $form->generate_submit_button("افزودن تراکنش");
	$form->output_submit_wrapper($buttons);
	$form->end();
}
   $page->output_footer();
?>