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
  <p>Hi, {$user_name}：</p>
  <p>感谢您对Shadowsky的捐助。</p>
  <p>您此次的捐助金额为 {$money} 元。</p>
  <p>目前您一共捐助了Shadowsky {$total_money} 元。</p>
  <p>Have a nice day.</p>
  <p>Sent from — <a href="{$config['baseUrl']}">{$config["appName"]}</a></p>
</body>
</html>