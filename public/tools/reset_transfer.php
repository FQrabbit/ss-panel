<?php
require_once '/var/www/shadowsky.xyz/ss-panel/lib/config.php';
/*
 * 清空流量
 */
//定义清零日期,1为每月1号
$reset_date = '1';
//日期符合就清零 
$trans_limitless_plan_array = ['包月','包季','包年'];

if (date('d')==$reset_date && date('H')=='00'){
	$datas = $db->select("user","*",["ORDER"=>"id"]);
	foreach ($datas as $row) {
		echo $row["id"]." ";
		$oo = new Ss\User\Ss($row["id"]);
		$oo->resetTransfer($row["id"]);
		if(in_array($row['type'], $trans_limitless_plan_array) && $row['plan']=="B"){
			$transfer = 999*1024*1024*1024;
			$db->update("user",[
				"transfer_enable" => $transfer
			],[
				"id" => $row['id']
			]);
		}
	}
}else{
	echo "今天不是重置日。";
}
?>
