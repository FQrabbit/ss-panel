<?php


	/* *
 	* 功能：支付宝、微信、QQ即时到账免签约接口处理聚合页面.本文件需要在服务器上测试。
 	* 有问题请在 http://api.web567.net/forum.php?mod=forumdisplay&fid=36 页面提交
 	* 以下代码只是为了方便测试而提供的样例代码，您可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 	* 技术文档请参阅下载页面。
 	*/




	/************★★★★★★★★★★★★★★★★★★★★★  传递到我平台整理至支付宝支付代码  ★★★★★★★★★★★★★★★★★★★★★★★************/



if(!$_GET['act']){//提交地址
	if(empty($_POST['total']) || empty($_POST['uid'])){
		echo "输入的信息不全！";exit;
	}
	if(!is_numeric($_POST['total'])){
		echo "输入的金额不是数字！";exit;
	}else if($_POST['total']<='0'){
		echo "输入的金额小于等于0！";exit;
	}
					
	$total = $_POST['total'];					//post得到的支付金额 		【必须为整数或者带小数点的数字】
	$uid = intval($_POST['uid']);					//post得到的支付会员编号	【支付成功要给此会员操作增加现金、道具、积分、提示等操作】
	$apiid = '13014';							//单引号''内填写您的apiid 	【用于给您的账户增加收入】
	$apikey = md5('f0a109d8ab8abc847aa16a5da4e407c8');						//单引号''内填写您的apikey	【用于支付安全验证】
	$showurl = 'http://此页在浏览器的地址/pay.php?act=return';	//回调地址，见下		【用于反馈支付状态到贵站】


	$addnum =  'pay'.$apiid.'12345054321';				//自定义订单编号。组合方式为 pay + 您的apiid + 自定义数字（自定义数字不可超过20位）
									//如果不使用，平台会自动生成。不会也不能重复。


	if(!$total || !$apiid || !$apikey || !$showurl){
		echo "<font color=red>管理员测试前，请先填写本文件目录下 pay.php 中的各项参数</font>";exit;
	}

	/***------------------建议此处把提交的订单记入MySQL数据库-------------------***/

	echo "
		<form name='form1' action='https://api.jsjapp.com/pay/syt.php' method='POST'>
			<input type='hidden' name='uid' value='".$uid."'>
			<input type='hidden' name='total' value='".$total."'>
			<input type='hidden' name='apiid' value='".$apiid."'>
			<input type='hidden' name='showurl' value='".$showurl."'>
			<input type='hidden' name='apikey' value='".$apikey."'>
			<input type='hidden' name='addnum' value='".$addnum."'>
		</form>
		<script>window.onload=function(){document.form1.submit();}</script> 
	";//信息已传入金沙江收银台。

	/***------------------也可以GET传入数据。不过我们建议POST-------------------***/

}



	/************★★★★★★★★★★★★★★★★★★★★★  传递代码已完，下面是接收支付回调代码  ★★★★★★★★★★★★★★★★★★★★★★★************/
	/************★★★★★★★★★★★★★★★  以下可以也可以独立为一文件  主要判断和执行会员支付是否正确及成功的后续操作  ★★★★★★★★★★***********/




if($_GET['act']=='return'){	//回调地址

	//以下四行无需更改		
	$addnum = $_POST['addnum'];		//接收到的订单编号
	$uid = $_POST['uid'];			//接收到的支付会员编号
	$total = $_POST['total'];		//接收到的支付金额
	$apikey = $_POST['apikey'];		//接收到的验证加密字串 ★★★收银台的新组合方式： md5(你的apikey.订单编号.uid.价格)


		/*****注意：请将本行前面的◆◆◆符号换成在我站申请的apikey。请注意单引号' ********/
	
		if($apikey != md5('◆◆◆'.$addnum.$uid.$total)){	//此处代码的作用是：验证加密字串是否正确。		
								
			header('location:/index.php');			//如果验证加密字串不正确的话就是非法回调，存在资金安全问题。让他跳转到首页去或者其他提示。
			exit;						//不在往下执行操作。

		}else{

				//这里写贵站的支付成功后续处理代码。(详情见下面示范)

				echo "支付成功!";
				
		}
}





















	/************★★★★★★★★★★★★★★★★★★★★★ 下面代码只是示范，不会在此php页面运行 ★★★★★★★★★★★★★★★★★★★★★★★************


第一步：创建数据表：---------------------创建数据表文件还可以参照 mysql.php

			*	需要根据业务逻辑来编写相应的程序代码。建议：
			*	1、先判断下 $addnum 订单编号是否存在数据库中，以防止刷新！
			*	2、如果订单编号不存在数据库中，编写入数据库文件，给会员增加金钱或道具或发货
			*	3、注意：$addnum 订单编号唯一的、不会重复。

			*	在此插件运行之前，您需要在数据库中创建一个MySQL数据表来判断是否重复操作，表字段结构建议如下：

			*		`id` int(10) NOT NULL AUTO_INCREMENT,
  					`uid` int(10) NOT NULL,
			*		`total` char(8) NOT NULL,
					`addnum` char(32) NOT NULL,
			*		`time` int(10) NOT NULL, 
					`status` int(2) NOT NULL, 

			*	表解释：id:数据序列编号  uid:支付的会员编号 total:支付的金额 addnum:订单编号 time:时间 status:处理状态1为已处理0为未处理

		--------------------------------------------------↑↑↑↑↑↑↑↑↑↑↑以上为创建数据表文件，如果已有数据表请忽略。
		--------------------------------------------------------------------------------------------------------------------
		--------------------------------------------------↓↓↓↓↓↓↓↓↓↓↓以下为在有数据表的基础上，支付记录入库和判断。

第二步：数据入库和判断：

			$con = mysql_connect("localhost","数据库用户名","数据库密码");
			if (!$con){
				die('Could not connect: ' . mysql_error());
			}
			mysql_select_db("数据库名", $con);

			//★★★★★★★★★★以下的数据库表名称请填写正确。比如：table_jsjpay

	$query = mysql_query("SELECT * FROM ★数据表名称★ where addnum='$addnum' LIMIT 1 "); 
	$rs = mysql_fetch_array($query);
	$status = $rs['status'];			//假设status是该表中支付成功后操作状态的字段 【0表示未处理，其他比如1为已处理】
	$xxid 	= $rs['id'];				//假设id是该数据表中记载的记录id编号


	if(empty($xxid)){				//如果数据库没有该订单的记录，支付完成并已经回调来了，就进行创建记录，并增加积分操作
		$status = '1';				//如果您想设置1为已处理
		$time = time();				//获取当前时间戳
		mysql_query("INSERT INTO ★数据表名称★ (uid, total, addnum ,status ,time) VALUES ($uid, $total, $addnum, $status, $time)");
			//★★★这里根据网站，给您对应的uid编号会员增加相应的积分、道具、其他操作。
		echo "支付成功";
		exit;		
	}


	if($status=='0'){				//有订单，但是订单未处理状态。需要修改订单状态并处理
		mysql_query("UPDATE ★数据表名称★ SET status = '1' WHERE id = $xxid ");
			//★★★这里根据网站，给您对应的uid编号会员增加相应的积分、道具、其他操作
		echo "支付成功";
		exit;
	}else{						//有订单，但是订单已经处理，不需要做任何处理
		echo "<script>alert('支付成功');window.top.location.href='/index.php';</script>";
	}


	*如果您能提交成功，但是回调页面老是测试出问题，为减少您的测试开支，您可以登录下面网址进行模拟支付成功后，平台发送的回调代码。
	*模拟回调网址：http://api.web567.net/plugin.php?id=add:demo&act=run

	***************************★★★★★★★★★★★★★★★★★★示范代码完毕★★★★★★★★★★★★★★★★★★★★★★★★★*****************/


	/************+++++++++++++++++++++++++++++++++++++  所有代码已完，请多理解和测试，合作愉快!  ++++++++++++++++++++++++++++++++++++++++++++++************/

?>