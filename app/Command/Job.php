<?php

namespace App\Command;

use App\Controllers\AdminController;
use App\Models\Ann;
use App\Models\AnnLog;
use App\Models\CheckInLog;
use App\Models\DelUser;
use App\Models\EmailVerify;
use App\Models\Node;
use App\Models\NodeDailyTrafficLog;
use App\Models\NodeInfoLog;
use App\Models\NodeOnlineLog;
use App\Models\PasswordReset;
use App\Models\Shop;
use App\Models\TrafficLog;
use App\Models\User;
use App\Models\UserDailyTrafficLog;
use App\Services\Config;
use App\Services\Mail;
use App\Utils\Tools;

class Job
{

    public static function resetUserPlan()
    {
        // $users = User::where('id', 1)->get();
        $users = User::where('expire_date', '>', '0000-00-00 00:00:00')->where('expire_date', '<', date('Y-m-d H:i:s'))->where('plan', 'B')->get();
        if ($users) {
            foreach ($users as $user) {
                $utype = $user->type;
                //发送邮件
                $to      = $user->email;
                $subject = Config::get('appName') . ' - 会员到期提醒';
                try {
                    Mail::send($to, $subject, 'news/plan-reset-report.tpl', ['user' => $user], []);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                if ($user->product_id && $user->product->isByTime()) {
                    $user->transfer_enable = Tools::toMB(100);
                    $user->u               = 0;
                    $user->d               = 0;
                }
                $user->plan       = 'A';
                $user->product_id = 0;
                $user->type       = 1;
                $user->save();

                // 输出日志
                $date  = date('Y-m-d H:i:s');
                $uname = $user->user_name;
                $uid   = $user->id;
                echo "$date 会员到期 $uid(套餐:$utype, $uname)\n";
            }
        }
    }

    /**
     * 重置用户流量，每天 00:01 am 执行
     */
    public static function resetUserTransfer()
    {
        /**
         * 查找本日流量需要重置的用户
         */
        $product_ids = Shop::where('type', 'A')->pluck('id')->toArray();
        $users       = User::where('expire_date', '>=', date('Y-m-d', strtotime('+1 month')))->
            where('buy_date', '<', date('Y-m-d'))->
            whereIn('product_id', $product_ids)->
            orderBy('expire_date', 'asc')->
            get();

        foreach ($users as $user) {
            if ($user->isTransferResetDay()) {
                // $to      = $user->email;
                // $subject = Config::get('appName') . " - 流量报告";
                // try {
                //     Mail::send($to, $subject, 'news/daily-traffic-report.tpl', ["user" => $user], []);
                // } catch (Exception $e) {
                //     echo $e->getMessage();
                // }
                $user->u               = 0;
                $user->d               = 0;
                $user->transfer_enable = Tools::toGB($user->product->transfer);
                $user->save();

                // 输出日志
                $date  = date('Y-m-d H:i:s');
                $uname = $user->user_name;
                $uid   = $user->id;
                $utype = $user->type;
                echo "$date 流量重置 $uid(套餐:$utype, $uname)\n";
            }
        }
        echo "\n";
    }

    public static function getNoTransferUser()
    {
        $day    = 24 * 3600;
        $period = date('Y-m-d H:i:s', (time() - 30 * $day));
        $users  = User::where('plan', 'A')
            ->where('ref_by', '!=', 3)
            ->where('reg_date', '<', $period)
            ->where('t', '<', (time() - 60 * $day))
            ->orderBy('t')
            ->get();
        // 输出日志
        if (!$users->isEmpty()) {
            $date        = date('Y-m-d H:i:s');
            $users_count = count($users);
            echo "$date 删除以下长时间未使用用户 sum: $users_count\n";
            echo "uid\tPlan\t注册时间\t\t上次签到时间\t\t上次使用时间(sort)\t流量\n";
            foreach ($users as $user) {
                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = $user->reg_date;
                $user_last_check_in_time = date('Y-m-d H:i:s', $user->last_check_in_time);
                $user_t                  = date('Y-m-d H:i:s', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                echo "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic\n";
            }
            echo "\n";
        }

        return $users;
    }

    public static function getUncheckinUser()
    {
        $day                  = 24 * 3600;
        $last_three_week_date = date('Y-m-d H:i:s', (time() - 21 * $day));
        $users                = User::where('last_check_in_time', '<', (time() - 21 * $day))
            ->where('plan', 'A')
            ->where('ref_by', '!=', 3)
            ->where('buy_date', '0000-00-00 00:00:00')
            ->where('reg_date', '<', $last_three_week_date)
            ->orderBy('last_check_in_time')
            ->get();

        // 输出日志
        if (!$users->isEmpty()) {
            $date        = date('Y-m-d H:i:s');
            $users_count = count($users);
            echo "$date 删除以下长时间未签到用户 sum: $users_count\n";
            echo "uid\tPlan\t注册时间\t\t上次签到时间(sortort)\t上次使用时间\t流量\n";
            foreach ($users as $user) {
                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = $user->reg_date;
                $user_last_check_in_time = date('Y-m-d H:i:s', $user->last_check_in_time);
                $user_t                  = date('Y-m-d H:i:s', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                echo "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic\n";
            }
            echo "\n";
        }

        return $users;
    }

    public static function freezeuser()
    {
        $day    = 24 * 3600;
        $t      = time() - 30 * $day;
        $period = date('Y-m-d H:i:s', $t);
        $users  = User::where('t', '<', $t)
            ->where('reg_date', '<', $period)
            ->where('plan', '!=', 'C')
            ->where('enable', 1)
            ->orderBy('t')
            ->get();
        // $users = User::find(1);
        if (!$users->isEmpty()) {
            // update 'enable' to 0
            User::where('t', '<', $t)
                ->where('reg_date', '<', $period)
                ->where('plan', '!=', 'C')
                ->where('enable', 1)
                ->orderBy('t')
                ->update(['enable' => 0]);

            // 输出日志
            $date        = date('Y-m-d H:i:s');
            $users_count = count($users);
            echo "$date 冻结以下30天未使用用户 sum: $users_count\n";
            echo "uid\tPlan\t注册时间\t\t上次签到时间\t\t上次使用时间(sort)\t流量\n";
            foreach ($users as $user) {
                // 发送邮件
                $arr['user_name'] = $user->user_name;
                try {
                    Mail::send($user->email, '账号冻结提醒 - Shadowsky', 'news/freeze-report.tpl', $arr, []);
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                }

                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = $user->reg_date;
                $user_last_check_in_time = date('Y-m-d H:i:s', $user->last_check_in_time);
                $user_t                  = date('Y-m-d H:i:s', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                echo "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic\n";
            }
            echo "\n";
        }
    }

    public static function delete($users)
    {
        foreach ($users as $user) {
            $fields = array(
                'id',
                'user_name',
                'plan',
                'port',
                'last_check_in_time',
                'reg_date',
                'email',
                'pass',
                'passwd',
                'u',
                'd',
                'user_type',
                'transfer_enable',
            );
            // 备份用户
            $u = new DelUser;
            foreach ($fields as $field) {
                $u->$field = $user->$field;
            }
            $u->save();

            // 删除用户相关记录
            AdminController::clearUserLogs($user->id);

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
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $usage = $node->getTrafficUsage();
            echo $node->name . ":\t" . $usage . "\n";
            $node->node_usage = $usage;
            $node->save();
        }
        echo "from api:\n";
        // bandwagon-us1,us3
        $Bnodes = Node::where('vps', 1)->get();
        foreach ($Bnodes as $node) {
            try {
                $request          = $node->api;
                $result           = json_decode(file_get_contents((string) $request));
                $usage            = round(($result->data_counter / $result->plan_monthly_data) * 100, 2);
                if ($usage > 100) {
                    $usage = 100;
                }
                $node->node_usage = $usage;
                $node->save();
                echo $node->name . ":\t" . $usage . "\n";
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
        // vultr - jp1,jp2,jp4
        $Vnodes = Node::where('vps', 12)->get();
        foreach ($Vnodes as $node) {
            try {
                $request          = $node->api;
                $result           = json_decode(file_get_contents((string) $request));
                $subid            = $node->subid;
                $usage            = round(($result->$subid->current_bandwidth_gb / $result->$subid->allowed_bandwidth_gb) * 100, 2);
                if ($usage > 100) {
                    $usage = 100;
                }
                $node->node_usage = $usage;
                $node->save();
                echo $node->name . ":\t" . $usage . "\n";
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
        echo date('Y-m-d H:i:s', time()) . "\n\n";
    }

    public static function clearLog()
    {
        echo date('Y-m-d H:i:s', time()) . "\n";

        $traffic_logs = TrafficLog::all();
        $date         = date('Y-m-d', strtotime('-1 day'));

        /**
         * 记录节点每日总流量
         */
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $nodes_traffic[$node->id] = 0;
        }
        foreach ($traffic_logs as $log) {
            $nodes_traffic[$log->node_id] += ($log->d + $log->u);
        }
        foreach ($nodes_traffic as $node_id => $traffic) {
            // echo "$node_id\t$traffic\n";
            NodeDailyTrafficLog::create(['node_id' => $node_id, 'traffic' => $traffic, 'date' => $date]);
        }

        /**
         * 记录用户每日总流量
         */
        $merged_users_traffic_logs = AdminController::mergeUsersTrafficLogs($traffic_logs);
        foreach ($merged_users_traffic_logs as $uid => $traffic) {
            // echo "$uid\t$traffic\n";
            if ($traffic > 0) {
                UserDailyTrafficLog::create(['uid' => $uid, 'traffic' => $traffic, 'date' => $date]);
            }
        }

        UserDailyTrafficLog::where('date', '<', date('Y-m-d', strtotime('-1 month')))->delete();
        echo "clear old UserDailyTrafficLog\n";

        TrafficLog::truncate();
        echo "clear TrafficLog\n";

        EmailVerify::truncate();
        echo "clear EmailVerifyLog\n";

        NodeInfoLog::truncate();
        echo "clear NodeInfoLog\n";

        CheckInLog::where('checkin_at', '<', strtotime('-1 month'))->delete();
        echo "clear CheckinLog\n";

        PasswordReset::truncate();
        echo "clear PasswordResetLog\n";

        NodeOnlineLog::truncate();
        echo "clear NodeOnlineLog\n\n";

        AnnLog::where('ann_id', '<', Ann::orderBy('id', 'desc')->first()->id)->delete();
        echo "clear AnnLog\n\n";
    }

}
