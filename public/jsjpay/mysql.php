<?php
/* *
 * 功能：以供mysql初学者快速创建一个数据表，以便能有效、准确的判断订单是否重复处理。
 * 有问题请在 http://api.jsjapp.com/forum.php?mod=forumdisplay&fid=36 页面提交
 * 以下代码只是为了方便测试而提供的样例代码，您可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 将本页面上传到php网站根目录，运行 http://您的网址/mysql.php 按要求填写。
 */


if($_GET[act]=='ok'){
	
	$con = mysql_connect($_POST['ip'],$_POST['username'],$_POST['userpass']);
	if(!$con){
		echo "<font color=red>链接数据库出错，请检查数据库IP地址、数据库用户名、数据库密码！</font>";
	}


	$sql = "CREATE TABLE IF NOT EXISTS `".$_POST['dbbiao']."` (
		`id` int(10) NOT NULL AUTO_INCREMENT,
		`uid` int(10) NOT NULL,
		`total` char(8) NOT NULL,
		`addnum` char(32) NOT NULL,
		`time` int(10) NOT NULL,
		`status` int(2) NOT NULL, 
		PRIMARY KEY  (`id`)
	)";
	mysql_select_db(''.$_POST['dbname'].'');
	if(mysql_query($sql)){
		echo "创建数据表成功！请牢记数据表名：<font color=red>".$_POST['dbbiao']."</font>";
	}else{
		echo "<font color=red>创建数据表失败了，请重新创建！请使用贵站程序所在的数据库名！</font>";
	}

	mysql_close($con);


}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<title>在线快速创建数据表--By jsj.pw</title>
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<style>
*{
	margin:0;
	padding:0;
}
ul,ol{
	list-style:none;
}
.title{
    color: #ADADAD;
    font-size: 14px;
    font-weight: bold;
    padding: 8px 16px 5px 10px;
}
.hidden{
	display:none;
}

.new-btn-login-sp{
	border:1px solid #D74C00;
	padding:1px;
	display:inline-block;
}

.new-btn-login{
    background-color: #ff8c00;
	color: #FFFFFF;
    font-weight: bold;
	border: medium none;
	width:82px;
	height:28px;
}
.new-btn-login:hover{
    background-color: #ffa300;
	width: 82px;
	color: #FFFFFF;
    font-weight: bold;
    height: 28px;
}
.bank-list{
	overflow:hidden;
	margin-top:5px;
}
.bank-list li{
	float:left;
	width:153px;
	margin-bottom:5px;
}

#main{
	width:750px;
	margin:0 auto;
	font-size:14px;
	font-family:'宋体';
}
#logo{
	background-color: transparent;
    background-image: url("images/new-btn-fixed.png");
    border: medium none;
	background-position:0 0;
	width:166px;
	height:35px;
    float:left;
}
.red-star{
	color:#f00;
	width:10px;
	display:inline-block;
}
.null-star{
	color:#fff;
}
.content{
	margin-top:5px;
}

.content dt{
	width:160px;
	display:inline-block;
	text-align:right;
	float:left;
	
}
.content dd{
	margin-left:100px;
	margin-bottom:5px;
}
#foot{
	margin-top:10px;
}
.foot-ul li {
	text-align:center;
}
.note-help {
    color: #999999;
    font-size: 12px;
    line-height: 130%;
    padding-left: 3px;
}

.cashier-nav {
    font-size: 14px;
    margin: 15px 0 10px;
    text-align: left;
    height:30px;
    border-bottom:solid 2px #CFD2D7;
}
.cashier-nav ol li {
    float: left;
}
.cashier-nav li.current {
    color: #AB4400;
    font-weight: bold;
}
.cashier-nav li.last {
    clear:right;
}
.alipay_link {
    text-align:right;
}
.alipay_link a:link{
    text-decoration:none;
    color:#8D8D8D;
}
.alipay_link a:visited{
    text-decoration:none;
    color:#8D8D8D;
}
</style>
</head>
<body text=#000000 bgColor=#ffffff leftMargin=0 topMargin=4>
	<div id="main">
        <div class="cashier-nav">
            <ol>
				<li class="current">1、填写数据库信息 →</li>
				<li>2、点击确认 →</li>
				<li class="last">3、创建完成</li>
            </ol>
        </div>
        <form name="alipayment" action="mysql.php?act=ok" method="post">
            <div id="body" style="clear:left">
                <dl class="content">
                    <dt>数据库IP地址：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        <input size="30" name="ip" id="ip" value="localhost" />
                        <span>一般为 localhost</span>
                    </dd>

                    <dt>数据库用户名：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        <input size="30" name="username" id="username" />
                        <span>如 root</span>
                    </dd>

                    <dt>数据库密码：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        <input size="30" name="userpass" id="userpass" />
                        <span></span>
                    </dd>

                    <dt>数据库名：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        <input size="30" name="dbname" id="dbname" />
                        <span>如 jsj</span>
                    </dd>

                    <dt><font color=red>数据表</font>：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        <input size="30" name="dbbiao" id="dbbiao" />
                        <span>如 table_jsjpay</span>
                    </dd>

                    <dt><font color=red>注意：</dt>
                    <dd>
                        <span class="null-star">*</span>
                        为了便于管理和整合，请在相同数据库下，创建相同前缀的数据表</font>
                        <span></span>
                    </dd>

		<dt></dt>
                    <dd>
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                    </dd>
                </dl>
            </div>
	</form>
</body>
</html>
