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
                    $user->transfer_enable = 104857600;
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
                echo "已更新用户".$user->user_name."(id:".$user->id.")的plan为A\n\n";
            }
        }
    }

    public static function getNoTransferUser()
    {
        $day = 24*3600;
        $last_three_week_date = date("Y-m-d H:i:s",(time() - 21*$day));
        $users = User::where("d", 0)
                    ->where("plan", "A")
                    ->where("ref_by", "!=", 3)
                    ->where("reg_date", "<", $last_three_week_date)
                    ->get();
        if (!$users->isEmpty()) {
            echo date("Y-m-d H:i:s",time())." 删除以下未使用用户：\n";
            echo "sum:".count($users)."\n";
            echo "uid\t"."用户名\t\t"."流量\t\t\t"."上次签到时间\t\t"."注册时间\n";
            foreach ($users as $user) {
                echo $user->id."\t".$user->user_name."\t".$user->usedTraffic()."/".$user->enableTraffic()."\t".date("Y-m-d H:i:s", $user->last_check_in_time)."\t".$user->reg_date."\n";
            }
            echo "\n";
        }
        return $users;
    }

    public static function getUncheckinUser()
    {
        $day = 24*3600;
        $last_three_week_date = date("Y-m-d H:i:s",(time() - 21*$day));
        $users = User::where("last_check_in_time", "<", (time()-21*$day))
                    ->where("plan", "A")
                    ->where("ref_by", "!=", 3)
                    ->where("reg_date", "<", $last_three_week_date)
                    ->get();
        if (!$users->isEmpty()) {
            echo date("Y-m-d H:i:s",time())." 删除以下未签到用户：\n";
            echo "sum:".count($users)."\n";
            echo "uid\t"."用户名\t\t"."流量\t\t\t"."上次签到时间\t\t"."注册时间\n";
            foreach ($users as $user) {
                echo $user->id."\t".$user->user_name."\t".$user->usedTraffic()."/".$user->enableTraffic()."\t".date("Y-m-d H:i:s", $user->last_check_in_time)."\t".$user->reg_date."\n";
            }
            echo "\n";
        }
        return $users;
    }

    public static function delete($users)
    {
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
            $user->delete();
        }
    }

    public static function delUncheckinUser()
    {
    	$users = self::getUncheckinUser();
    	self::delete($users);
    }

    public static function delNoTransferUser()
    {
    	$users = self::getNoTransferUser();
    	self::delete($users);
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

        //jp1,jp4
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

        //jp2,jp3
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