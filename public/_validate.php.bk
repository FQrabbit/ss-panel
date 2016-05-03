<?php
header("content-type:text/html;charset=utf-8");
require_once "pay/config.php";
if (!empty($_GET)) {
	$email = $_GET['email'];
	$id = $_GET['id'];
	$status = $U->GetUserStatus();
	$passwd = $U->GetPasswd();
	$reg_date = $U->RegDate();
	$regtime = strtotime($reg_date);
	$token = md5($username.$passwd.$regtime);
	$token_exptime = time()+60*60*24;
	if(!$status){
		$db->update("user",[
				"token" => $token,
				"token_exptime" => $token_exptime
			],[
				"id" => $id
		]);
		$a['token']=$token;
		$a['email']=$email;
		$a['id']=$id;
		$mg->sendMessage($domain, array('from' => $sender,
		            'to'      => $email,
		            'subject' => $site_name."邮箱验证",
		            'text'    => '欢迎注册Shadowsky,请访问此链接激活账号'.$site_url."activate.php?verify=".$token));
		$a['code'] = '1';
        $a['ok'] = '1';
        $a['msg']  =  "请登录邮箱及时激活您的账号,激活有效期为24小时。如果没收到邮件请查看垃圾箱。";
	}
}
echo json_encode($a);