<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
  *{
  	font-family: "Microsoft Yahei";
  	color: black;
  }
  a{
  	color: #348eda;
  	text-decoration: none;
  }
  </style>
</head>
<body>
  <p>Hi, {$user->user_name}：</p>
  <p>您已成功在Shadowsky购买 {$product->name} 套餐，流量已分发到您的账户中，所有节点已开通，可能要过几分钟才生效。如有任何问题，请<a href="mailto:{$config['adminEmail']}">联系站长</a>。</p>
  <p>Sent from — <a href="{$config['baseUrl']}">{$config["appName"]}</a></p>
</body>
</html>