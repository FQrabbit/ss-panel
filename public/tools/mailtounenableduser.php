<?php
//设置编码
header("content-type:text/html;charset=utf-8");
require_once '../pay/config.php';
// //
// $email = $_GET['email'];
// $id = $_GET['id'];
// $U = new \Ss\User\UserInfo($id);
// $status = $U->GetUserStatus();
// $username = $U->GetUserName();
// $passwd = $U->GetPasswd();
// $reg_date = $U->RegDate();
// $regtime = strtotime($reg_date);
// $token = md5($username.$passwd.$regtime);
$token_exptime = time()+60*60*24*7;
// echo $token;
// if(!$status){


	$users = $db->select("user","*",[
		"AND" => [
			// "token_exptime" => 0,
			"enable" => 0
		]
	]);

	echo "总人数：".count($users)."<br>";

	foreach ($users as $user) {

		$email = $user['email'];
		$id = $user['id'];
		$token = md5($user['user_name'].$user['$pass'].strtotime($user['reg_date']));

		$db->update("user",[
				"token" => $token,
				"token_exptime" => $token_exptime
			],[
				"id" => $id
		]);

		$mg->sendMessage($domain, array('from'    => $sender,
		            'to'      => $email,
		            'subject' => $site_name."激活账号",
		            'text'    => '欢迎注册Shadowsky,请访问此链接激活账号'.$site_url."activate.php?verify=".$token
		));

		echo "已给".$user['id']."发送验证邮件"."<br>";
	}



	// $db->update("user",[
	// 		"token" => $token,
	// 		"token_exptime" => $token_exptime
	// 	],[
	// 		"id" => $id
	// ]);
	// $a['token']=$token;
	// $a['email']=$email;
	// $a['id']=$id;
	// $mg->sendMessage($domain, array('from' => "Shadowsky<no-reply@shadowsky.xyz>",
	//             'to'      => $email,
	//             'subject' => $site_name."邮箱验证",
	//             'text'    => '欢迎注册Shadowsky,请访问此链接激活账号'.$site_url."active.php?verify=".$token));
	// 			$a['code'] = '1';
	// 	        $a['ok'] = '1';
	// 	        $a['msg']  =  "请登录邮箱及时激活您的账号,激活有效期为24小时。如果没收到邮件请查看垃圾箱。";
// }
// echo json_encode($a);