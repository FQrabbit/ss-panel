<?php 
require_once '/var/www/shadowsky.xyz/ss-panel/lib/config.php';
$nodes = $db->select("ss_node","field_name");
$array = $nodes;
array_push($array, "id", "t");
$datas = $db->select("user",$array,[
	"ORDER" => "id"
]);
foreach ($datas as $data) {
	foreach ($nodes as $node) {
		if($data[$node]>$data['t']){
			$db->update("user",[
				"t" => $data[$node]
			],[
				"id" => $data["id"]
			]);
		}
	}
}
 ?>

