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
  <p>{$user->user_name}(uid:{$user->id}, port:{$user->port})已购买{$user->type}套餐.</p>
  <p>金额: {$user->user_type}元.</p>
  <p>原plan: {$pre['plan']}.</p>
  <p>原套餐: {$pre['type']}.</p>
  <p>原购买时间: {$pre['buy_date']}.</p>
  <p>原过期时间: {$pre['expire_date']}.</p>
  <p>现过期时间: {$user->expire_date}.</p>
  <p>原流量: {$pre['used_traffic_in_GB']} G / {$pre['transfer_eanble_in_GB']} G.</p>
</body>
</html>