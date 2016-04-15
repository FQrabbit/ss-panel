<?php
//设置编码
header("content-type:text/html;charset=utf-8");
require_once '../lib/config.php';
//mailgun
require '../vendor/autoload.php';
use Mailgun\Mailgun;
$mg = new Mailgun($mailgun_key);
$domain = $mailgun_domain;
//
$two_days = 2*24*3600;
$week = 3*7*24*3600;
$last_two_days = time() - $two_days;
$last_week = time() - $week;
$last_two_days_date = date("Y-m-d H:i:s",$last_two_days);
$last_week_date = date("Y-m-d H:i:s",$last_week);


###给长时间没签到的用户发送一封邮件
$users = $db->select("user","*",[
	"AND" => [
		"last_check_in_time[<]" => $last_week,
		"reg_date[<]" => $last_week_date,
		"plan" => "A",
		"ref_by[!]" => 3
	]
]);
echo '人数：'.count($users).'<br>';

foreach ($users as $user) {
	$mg->sendMessage($domain, array('from' => "Shadowsky<no-reply@shadowsky.xyz>",
	            'to'      => $user['email'],
	            'subject' => '来自Shadowsky的问候',
	            'text'    => "I found that you haven't checkin for a long time, I hope you sign in and checkin as soon as possible. Or your account will possibly be deleted--You can click here to sign in https://www.shadowsky.xyz/user/index.php"
	));
	echo 'id:'.$user['id'].' status:'.$user['status'].' enable:'.$user['enable'].' plan:'.$user['plan'].' 注册时间:'.$user['reg_date'].' 上次签到时间:'.date("Y-m-d H:i:s",$user['last_check_in_time']).' 端口:'.$user['port'].' 邮箱:'.$user['email'].' 名称:'.$user['user_name'].' ref_by:'.$user['ref_by'].'<br>';
}

$mg->sendMessage($domain, array('from' => "Shadowsky<no-reply@shadowsky.xyz>",
            'to'      => 'zhwalker20@gmail.com',
            'subject' => '来自Shadowsky的问候',
            'text'    => "I found that you haven't checkin for a long time, I hope you sign in and checkin as soon as possible. Or your account will possibly be deleted--You can click here to sign in https://www.shadowsky.xyz/user/index.php"
));
####################