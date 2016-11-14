<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body>
  <p>Hi, {$user->user_name}</p>
  <p>您购买的{$user->type}套餐已到期，您的账户类型已改为免费用户，如果想继续使用本站的服务，请前往网站购买。</p>
  <p>感谢您的支持 -- <a href="{$config["baseUrl"]}" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #348eda; text-decoration: underline; margin: 0;">{$config["appName"]}</a></p>
</body>
</html>