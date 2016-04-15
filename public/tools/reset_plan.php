<?php 
header("content-type:text/html;charset=utf-8");
require_once '/var/www/shadowsky.xyz/ss-panel/lib/config.php';
require '/var/www/shadowsky.xyz/ss-panel/vendor/autoload.php';
//mailgun
use Mailgun\Mailgun;
$mg = new Mailgun($mailgun_key);
$domain = $mailgun_domain;

$users = $db->select('user','*',[
	'AND' => [
		'expire_date[>]' => '0000-00-00 00:00:00',
		'plan' => 'B'
	]
]);
// foreach ($users as $key => $user) {
// 	if ($user['expire_date'] < date('Y-m-d H:i:s')) {
// 		echo $user['id'];
// 		echo "<br>";
// 	}
// }
foreach($users as $user){
	if($user['expire_date'] < date('Y-m-d H:i:s')){
		if(in_array($user['type'], ['包月','包季','包年'])){
			$db->update('user',[
				'plan' => 'A',
				'transfer_enable' => 524288000,
				'u' => 0,
				'd' => 0,
				'type' => 1
			],[
				'id' => $user['id']
			]);
		}else{
			$db->update('user',[
				'plan' => 'A',
				'type' => 1
			],[
				'id' => $user['id']
			]);
		}
		$mg->sendMessage($domain, array('from' => "Shadowsky<no-reply@shadowsky.xyz>",
		            'to'      => $user['email'],
		            'subject' => $site_name."-到期提醒",
		            'text'    => '尊敬的'.$user['user_name'].", 您购买的".$user['type']."套餐已到期，您的账户类型已改为免费用户，为避免您的账号因长时间未签到而被删除请及时登录网站签到，或重新购买本站套餐。感谢你的支持--Shadowsky"
		));
		echo '已更新用户'.$user['user_name'].'的plan为A</br>';
	}
}
?>