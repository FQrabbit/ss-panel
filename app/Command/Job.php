<?php

namespace App\Command;

use App\Models\User;
use App\Models\Node;
use App\Services\Config;
use App\Services\Mail;

class Job
{

    public static function resetUserPlan()
    {
        $users = User::where("expire_date", ">", "0000-00-00 00:00:00")->where("plan", "B")->get();
        foreach ($users as $user) {
            echo $user->id."\n";
        }
    }

    public static function updateNodeUsage()
    {
        //us1
        $request = "https://api.64clouds.com/v1/getServiceInfo?veid=149769&api_key=private_7pmMhX8OPI6jkof3U6bHxJT3";
        $serviceInfo = json_decode (file_get_contents ($request));
        $usage = ($serviceInfo->data_counter / $serviceInfo->plan_monthly_data) * 100;
        $node = Node::find(1);
        $node->node_usage = $usage;
        $node->save();
        echo "us1:".$usage."\n";

        //jp1,jp3,jp4
        $out = file_get_contents("https://api.vultr.com/v1/server/list?api_key=YH7LQH4WIRA2RSLCGHXSFH4E23XZ4L");
        $out = json_decode($out);
        $arr = [
            "jp1" => "3868748",
            "jp3" => "3614066",
            "jp4" => "3682976"
        ];
        foreach ($arr as $name => $subid) {
            $name_usage = ($out->$subid->current_bandwidth_gb / $out->$subid->allowed_bandwidth_gb) * 100;
            $node = Node::where("name", $name)->update(array("node_usage" => $name_usage));
            echo $name.":".$name_usage."\n";
        }

        //jp2,us2
        $out = file_get_contents("https://api.vultr.com/v1/server/list?api_key=WW62CTHYRPTBVWNNIBIGHOO2AFQLUB");
        $out = json_decode($out);
        $arr = [
            "jp2" => "3158192",
            "us2" => "3869089"
        ];
        foreach ($arr as $name => $subid) {
            $name_usage = ($out->$subid->current_bandwidth_gb / $out->$subid->allowed_bandwidth_gb) * 100;
            $node = Node::where("name", $name)->update(array("node_usage" => $name_usage));
            echo $name.":".$name_usage."\n";
        }
        echo date("Y-m-d H:i:s",time())."\n\n";
    }



}