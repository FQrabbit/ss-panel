<?php
require_once '/var/www/shadowsky.xyz/ss-panel/lib/config.php';
$two_days = 2*24*3600;
$week = 7*24*3600;
$three_week = 3*$week;
$last_two_days = time() - $two_days;
$last_week = time() - $week;
$last_three_week = time() - $three_week;
$last_two_days_date = date("Y-m-d H:i:s",$last_two_days);
$last_week_date = date("Y-m-d H:i:s",$last_week);
$last_three_week_date = date("Y-m-d H:i:s",$last_three_week);
echo($last_week_date." ".$last_week."<br>");
echo($last_three_week_date." ".$last_three_week."<br>");
$users = $db->select("user","*",[
	"AND" => [
		"OR"=>[
			"AND"=>[
				"last_check_in_time[<]" => $three_week,
				"reg_date[<]" => $last_three_week_date
			],
			"AND"=>[
				"enable" => 0,
				"reg_date[<]" => $last_week_date
			]
		],
		"plan" => "A",
		"ref_by[!]" => 3
	]
	// "ORDER" => "last_check_in_time ASC"
]);
echo '人数：'.count($users).'<br>';
foreach ($users as $key => $value) {
	echo 'id:'.$value['id'].' status:'.$value['status'].' enable:'.$value['enable'].' plan:'.$value['plan'].' 注册时间:'.$value['reg_date'].' 上次签到时间:'.date("Y-m-d H:i:s",$value['last_check_in_time']).' 端口:'.$value['port'].' 邮箱:'.$value['email'].' 名称:'.$value['user_name'].' ref_by:'.$value['ref_by'].'<br>';
};

// print_r($users);

if(!empty($users)){
	foreach ($users as $user=>$keys) {
		$db->insert("del_user",[
			"id" => $keys["id"]
		]);
		foreach ($keys as $key => $value) {
			$db->update("del_user",[
				$key => $value
			],[
				"id" => $keys["id"]
			]);
		}
		$db->update("del_user",[
			"#del_date" => "NOW()"
		],[
			"id" => $keys["id"]
		]);
		$db->delete("user",[
			"id" => $keys["id"]
		]);
		echo "已删除 ".$keys['user_name']."<br>";
	}
}

// restore user////
// 
// if(!empty($users)){
// 	foreach ($users as $user=>$keys) {
// 		$db->insert("user",[
// 			"id" => $keys["id"]
// 		]);
// 		foreach ($keys as $key => $value) {
// 			$db->update("user",[
// 				$key => $value
// 			],[
// 				"id" => $keys["id"]
// 			]);
// 		}
// 		echo "已恢复 ".$keys['user_name']."<br>";
// 	}
// }

?>
