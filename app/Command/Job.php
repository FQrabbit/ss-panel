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
        if (!$users->isEmpty()) {
            $log = '';
            foreach ($users as $user) {
                $utype = $user->type;
                //发送邮件
                $to      = $user->email;
                $subject = Config::get('appName') . ' - 会员到期提醒';
                try {
                    Mail::send($to, $subject, 'news/plan-reset-report.tpl', ['user' => $user], []);
                } catch (\Exception $e) {
                    $log .= $e->getMessage();
                }
                if ($user->product && $user->product->isByTime()) {
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
                $log .= "$date 会员到期 $uid(套餐:$utype, $uname)" . PHP_EOL;
            }
            $log .= PHP_EOL;
            self::appendLog($log, 'resetlog.log');
        }
    }

    /**
     * 重置用户流量,每分钟执行一次
     */
    public static function resetUserTransfer()
    {
        /**
         * 查找本日流量需要重置的用户
         */
        $product_ids = Shop::where('type', 'A')->pluck('id')->toArray();
        $users       = User::where('expire_date', '>=', date('Y-m-d', strtotime('+1 month')))->
            where('buy_date', '<', date('Y-m-d H:i:s'))->
            whereIn('product_id', $product_ids)->
            orderBy('expire_date', 'asc')->
            get();
// $users = User::where('id', 1)->get();
        if (!$users->isEmpty()) {
            $flag = false;
            $log = '';
            foreach ($users as $user) {
                if (!$user->haveResetTransferToday() && $user->isTransferResetDay()) {
                    $flag = true;
                    // $to      = $user->email;
                    // $subject = Config::get('appName') . " - 流量报告";
                    // try {
                    //     Mail::send($to, $subject, 'news/daily-traffic-report.tpl', ["user" => $user], []);
                    // } catch (\Exception $e) {
                    //     echo $e->getMessage();
                    // }
                    $user->u                        = 0;
                    $user->d                        = 0;
                    $user->transfer_enable          = Tools::toGB($user->product->transfer);
                    $user->last_transfer_reset_time = time();
                    $user->save();

                    // 输出日志
                    $date        = date('Y-m-d H:i:s');
                    $expire_date = $user->expire_date;
                    $uname       = $user->user_name;
                    $uid         = $user->id;
                    $utype       = $user->type;
                    $log .= "$date 流量重置 $uid(过期日期:$expire_date, 套餐:$utype, $uname)" . PHP_EOL;
                }
            }
            if ($flag) {
                $log .= PHP_EOL;
                self::appendLog($log, 'resetlog.log');                
            }
        }
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
            $log = "$date 删除以下长时间未使用用户 sum: $users_count" . PHP_EOL;
            $log .= "uid\tPlan\t注册时间\t上次签到时间\t上次使用时间(s)\t流量" . PHP_EOL;
            foreach ($users as $user) {
                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = date('Y-m-d', strtotime($user->reg_date));
                $user_last_check_in_time = date('Y-m-d', $user->last_check_in_time);
                $user_t                  = date('Y-m-d', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                $log .= "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic" . PHP_EOL;
            }
            $log .= PHP_EOL;
            self::appendLog($log, 'resetlog.log');
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
            $log = "$date 删除以下长时间未签到用户 sum: $users_count" . PHP_EOL;
            $log .= "uid\tPlan\t注册时间\t上次签到时间(s)\t上次使用时间\t流量" . PHP_EOL;
            foreach ($users as $user) {
                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = date('Y-m-d', strtotime($user->reg_date));
                $user_last_check_in_time = date('Y-m-d', $user->last_check_in_time);
                $user_t                  = date('Y-m-d', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                $log .= "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic" . PHP_EOL;
            }
            $log .= PHP_EOL;
            self::appendLog($log, 'resetlog.log');
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
            $log = "$date 冻结以下30天未使用用户 sum: $users_count" . PHP_EOL;
            $log .= "uid\tPlan\t注册时间\t上次签到时间\t上次使用时间(s)\t流量" . PHP_EOL;
            foreach ($users as $user) {
                // 发送邮件
                $arr['user_name'] = $user->user_name;
                try {
                    Mail::send($user->email, '账号冻结提醒 - Shadowsky', 'news/freeze-report.tpl', $arr, []);
                } catch (\Exception $e) {
                    $log .= $e->getMessage() . PHP_EOL;
                }

                $user_id                 = $user->id;
                $user_plan               = $user->plan;
                $user_reg_date           = date('Y-m-d', strtotime($user->reg_date));
                $user_last_check_in_time = date('Y-m-d', $user->last_check_in_time);
                $user_t                  = date('Y-m-d', $user->t);
                $user_used_traffic       = $user->usedTraffic();
                $user_enable_traffic     = $user->enableTraffic();
                $log .= "$user_id\t$user_plan\t$user_reg_date\t$user_last_check_in_time\t$user_t\t$user_used_traffic / $user_enable_traffic" . PHP_EOL;
            }
            $log .= PHP_EOL;
            self::appendLog($log, 'resetlog.log');
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
        $log   = date('Y-m-d H:i:s', time()) . PHP_EOL;
        $nodes = Node::all();
        foreach ($nodes as $node) {
            $usage = $node->getTrafficUsage();
            $log .= $node->name . ":\t" . $usage . PHP_EOL;
            $node->node_usage = $usage;
            $node->save();
        }
        $log .= "from api:" . PHP_EOL;
        // bandwagon-us1,us3
        $Bnodes = Node::where('vps', 1)->get();
        foreach ($Bnodes as $node) {
            try {
                $request = $node->api;
                $result  = json_decode(file_get_contents((string) $request));
                $usage   = round(($result->data_counter / $result->plan_monthly_data) * 100, 2);
                if ($usage > 100) {
                    $usage = 100;
                }
                $node->node_usage = $usage;
                $node->save();
                $log .= $node->name . ":\t" . $usage . PHP_EOL;
            } catch (\Exception $e) {
                $log .= $e->getMessage() . PHP_EOL;
            }
        }
        // vultr - jp1,jp2,jp4
        $Vnodes = Node::where('vps', 12)->get();
        foreach ($Vnodes as $node) {
            try {
                $request = $node->api;
                $result  = json_decode(file_get_contents((string) $request));
                $subid   = $node->subid;
                $usage   = round(($result->$subid->current_bandwidth_gb / $result->$subid->allowed_bandwidth_gb) * 100, 2);
                if ($usage > 100) {
                    $usage = 100;
                }
                $node->node_usage = $usage;
                $node->save();
                $log .= $node->name . ":\t" . $usage . PHP_EOL;
            } catch (\Exception $e) {
                $log .= $e->getMessage() . PHP_EOL;
            }
        }
        $log .= PHP_EOL;
        self::appendLog($log, 'nodelog.log');
    }

    public static function arrangeTrafficLog()
    {
        $t    = strtotime('-1 day');
        $date = date('Y-m-d', $t);

        /**
         * 记录节点每日总流量
         */
        $traffic_logs = TrafficLog::where('log_time', '>=', $t)->where('log_time', '<', strtotime(date('Y-m-d')))->groupBy('node_id')->selectRaw('node_id, sum(u)+sum(d) as traffic')->get();
// print_r($traffic_logs);return;
        foreach ($traffic_logs as $log) {
            try {
                NodeDailyTrafficLog::create(['node_id' => $log->node_id, 'traffic' => $log->traffic, 'date' => $date]);
            } catch (\Exception $e) {
// echo $log->node_id. ': ' . Tools::flowAutoShow($log->traffic) . PHP_EOL;
                echo $e->getMessage() . PHP_EOL . PHP_EOL;
            }
        }

        /**
         * 记录用户每日总流量
         */
        $traffic_logs = TrafficLog::where('log_time', '>=', $t)->where('log_time', '<', strtotime(date('Y-m-d')))->groupBy('user_id')->selectRaw('user_id, sum(u)+sum(d) as traffic')->get();
        foreach ($traffic_logs as $log) {
            try {
                UserDailyTrafficLog::create(['uid' => $log->user_id, 'traffic' => $log->traffic, 'date' => $date]);
            } catch (\Exception $e) {
// echo $log->user_id. ': ' . Tools::flowAutoShow($log->traffic) . PHP_EOL;
                echo $e->getMessage() . PHP_EOL . PHP_EOL;
            }
        }
    }

    public static function clearLog()
    {
        $log = date('Y-m-d H:i:s', time()) . PHP_EOL;

        try {
            self::arrangeTrafficLog();
            $log .= 'Arranged TrafficLog' . PHP_EOL;

            UserDailyTrafficLog::where('date', '<', date('Y-m-d', strtotime('-1 month')))->delete();
            $log .= "clear old UserDailyTrafficLog" . PHP_EOL;

            TrafficLog::truncate();
            $log .= "clear TrafficLog" . PHP_EOL;

            EmailVerify::truncate();
            $log .= "clear EmailVerifyLog" . PHP_EOL;

            NodeInfoLog::truncate();
            $log .= "clear NodeInfoLog" . PHP_EOL;

            CheckInLog::where('checkin_at', '<', strtotime('-1 month'))->delete();
            $log .= "clear CheckinLog" . PHP_EOL;

            PasswordReset::truncate();
            $log .= "clear PasswordResetLog" . PHP_EOL;

            NodeOnlineLog::truncate();
            $log .= "clear NodeOnlineLog" . PHP_EOL;

            AnnLog::where('ann_id', '<', Ann::orderBy('id', 'desc')->first()->id)->delete();
            $log .= "clear AnnLog" . PHP_EOL . PHP_EOL;
        } catch (\Exception $e) {
            $log .= $e->getMessage();
        }
        self::appendLog($log, 'site.log');
    }

    public static function appendLog($txt, $file)
    {
        $file = BASE_PATH . 'public/tools/' . $file;
        $txt = iconv(mb_detect_encoding($txt), "GBK", $txt) . file_get_contents($file);
        file_put_contents($file, $txt);
    }
}
