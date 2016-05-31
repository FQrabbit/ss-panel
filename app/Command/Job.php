<?php

namespace App\Command;

use App\Models\User;
use App\Models\DelUser;
use App\Models\Node;
use App\Services\Config;
use App\Services\Mail;

class Job
{

    public static function resetUserPlan()
    {
        $users = User::where("expire_date", ">", "0000-00-00 00:00:00")->where("plan", "B")->get();
        foreach ($users as $user) {
            if ($user->expire_date < date('Y-m-d H:i:s')) {

                $to = $user->email;
                $subject = Config::get('appName') . "-会员到期提醒";
                try {
                    Mail::send($to, $subject, 'news/plan-reset-report.tpl', [
                        "user" => $user
                    ], [
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }

                if (in_array($user->type, ['包月','包季','包年'])) {
                    $user->plan = "A";
                    $user->transfer_enable = 524288000;
                    $user->u = 0;
                    $user->d = 0;
                    $user->type = 1;
                    $user->save();
                }else {
                    $user->plan = "A";
                    $user->type = 1;
                    $user->save();
                }

                echo date("Y-m-d H:i:s")."\n";
                echo "已更新用户".$user->user_name."的plan为A\n\n";
            }
        }
    }

    public function delUncheckinUser()
    {
        $week = 7*24*3600;
        $three_week = 3*$week;
        $last_week = time() - $week;
        $last_three_week = time() - $three_week;
        $last_week_date = date("Y-m-d H:i:s",$last_week);
        $users = User::where("last_check_in_time", "<", $last_three_week)
                    ->where("plan", "A")
                    ->where("ref_by", "!=", 3)
                    ->where("reg_date", "<", $last_week_date)
                    ->get();
        // $users = User::where("id", 2)->get();
        foreach ($users as $user) {
            $fields = array(
                "id",
                "user_name",
                "plan",
                "port",
                "last_check_in_time",
                "reg_date",
                "email",
                "pass",
                "passwd",
                "u",
                "d",
                "user_type",
                "transfer_enable"
            );
            $u = new DelUser;
            foreach ($fields as $field) {
                $u->$field = $user->$field;
            }
            $u->save();
            echo date("Y-m-d H:i:s",time())."\n";
            echo "已删除 ".$user->user_name."(id:".$user->id.") ".($user->transfer_enable/1073741824)." 上次签到时间:".date("Y-m-d H:i:s", $user->last_check_in_time)."\n\n";
            $user->delete();
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
            "jp3" => "3963638"
        ];
        foreach ($arr as $name => $subid) {
            $name_usage = ($out->$subid->current_bandwidth_gb / $out->$subid->allowed_bandwidth_gb) * 100;
            $node = Node::where("name", $name)->update(array("node_usage" => $name_usage));
            echo $name.":".$name_usage."\n";
        }
        echo date("Y-m-d H:i:s",time())."\n\n";
    }



}