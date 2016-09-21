<?php

namespace App\Command;

use App\Models\User;
use App\Models\DelUser;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\CheckInLog;
use App\Models\EmailVerify;
use App\Models\PasswordReset;
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
                echo "已更新用户".$user->user_name."(id:".$user->id.")的plan为A</br>";
            }
        }
    }

    public static function getNoTransferUser()
    {
        $day = 24*3600;
        $period = date("Y-m-d H:i:s",(time() - 30*$day));
        $users = User::where("plan", "A")
                        ->where("ref_by", "!=", 3)
                        ->where("reg_date", "<", $period)
                        ->where("t", "<", (time() - 60*$day))
                        ->orderBy("t")
                        ->get();
        if (!$users->isEmpty()) {
            echo date("Y-m-d H:i:s",time())." 删除以下长时间未使用用户：</br>";
            echo "sum:".count($users)."\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {
                echo "<tr><td>".$user->id."</td><td>".$user->user_name."</td><td>".$user->reg_date."</td><td>".date("Y-m-d H:i:s", $user->last_check_in_time)."</td><td>".date("Y-m-d H:i:s", $user->t)."</td><td>".$user->usedTraffic()."/".$user->enableTraffic()."</td></tr>\n";
            }
            echo "</tbody></table></br>";
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
                    ->where("buy_date", ">", "0000-00-00 00:00:00")
                    ->where("reg_date", "<", $last_three_week_date)
                    ->get();
        if (!$users->isEmpty()) {
            echo date("Y-m-d H:i:s",time())." 删除以下长时间未签到用户：</br>";
            echo "sum:".count($users)."\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {
                echo "<tr><td>".$user->id."</td><td>".$user->user_name."</td><td>".$user->reg_date."</td><td>".date("Y-m-d H:i:s", $user->last_check_in_time)."</td><td>".date("Y-m-d H:i:s", $user->t)."</td><td>".$user->usedTraffic()."/".$user->enableTraffic()."</td></tr>\n";
            }
            echo "</tbody></table></br>";
        }
        return $users;
    }

    public static function freezeuser()
    {
        $day = 24*3600;
        $t = time() - 30*$day;
        $period = date("Y-m-d H:i:s",$t);
        $users = User::where("t", "<", $t)
                        ->where("reg_date", "<", $period)
                        ->where("plan", "!=", "C")
                        ->where("enable", 1)
                        ->orderBy("t")
                        ->get();
        User::where("t", "<", $t)
            ->where("reg_date", "<", $period)
            ->where("plan", "!=", "C")
            ->where("enable", 1)
            ->orderBy("t")
            ->update(['enable' => 0]);
        if (!$users->isEmpty()) {
            echo date("Y-m-d H:i:s",time())." 冻结以下用户：</br>";
            echo "sum:".count($users)."\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {
                echo "<tr><td>".$user->id."</td><td>".$user->user_name."</td><td>".$user->reg_date."</td><td>".date("Y-m-d H:i:s", $user->last_check_in_time)."</td><td>".date("Y-m-d H:i:s", $user->t)."</td><td>".$user->usedTraffic()."/".$user->enableTraffic()."</td></tr>\n";
            }
            echo "</tbody></table></br>";
        }
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
        // bandwagon-us1
        try {
            $node = Node::find(21);
            $request = $node->api;
            $serviceInfo = json_decode (file_get_contents ($request));
            $usage = round(($serviceInfo->data_counter / $serviceInfo->plan_monthly_data) * 100, 2);
            $node->node_usage = $usage;
            $node->save();
            echo $node->name.":".$usage."\n";
        } catch (Exception $e) {
            echo $e->getMessage()."\n";
        }
        // vultr
        $nodes = Node::where('vps', 'vultr')->get();
        foreach ($nodes as $node) {
            try {
                $request = $node->api;
                $out = json_decode (file_get_contents ($request));
                $subid = $node->subid;
                $name_usage = round(($out->$subid->current_bandwidth_gb / $out->$subid->allowed_bandwidth_gb) * 100 ,2);
                $node->node_usage = $name_usage;
                $node->save();
                echo $node->name.":".$name_usage."\n";
            } catch (Exception $e) {
                echo $e->getMessage()."\n";
            }
        }
    }

    public static function clearLog()
    {
        echo date("Y-m-d H:i:s",time())."\n";

        TrafficLog::truncate();
        echo "clear TrafficLog\n";

        EmailVerify::truncate();
        echo "clear EmailVerifyLog\n";

        NodeInfoLog::where("log_time", "<", (time()-120))->delete();
        echo "clear NodeInfoLog\n";

        CheckInLog::where("checkin_at", "<", (time()-30*12*3600))->delete();
        echo "clear CheckinLog\n";

        PasswordReset::truncate();
        echo "clear PasswordResetLog\n";
        
        NodeOnlineLog::where("log_time", "<", (time()-120))->delete();
        echo "clear NodeOnlineLog\n\n";
    }

}
