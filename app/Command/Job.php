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
                $to      = $user->email;
                $subject = Config::get('appName') . '-会员到期提醒';

                //发送邮件
                try {
                    Mail::send($to, $subject, 'news/plan-reset-report.tpl', ['user' => $user,], []);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                if ($user->product_id && $user->getProduct()->isByTime()) {
                    $user->transfer_enable = Tools::toMB(100);
                    $user->u               = 0;
                    $user->d               = 0;
                }
                if (in_array($user->type, ['包月', '包季', '包年'])) {
                    $user->transfer_enable = Tools::toMB(100);
                    $user->u               = 0;
                    $user->d               = 0;
                }
                $user->plan = 'A';
                $user->product_id = 0;
                $user->type = 1;
                $user->save();

                echo date('Y-m-d H:i:s') . "\n";
                echo $user->user_name . '(id:' . $user->id . ')的会员已到期</br>';
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
        if (!$users->isEmpty()) {
            echo date('Y-m-d H:i:s', time()) . ' 删除以下长时间未使用用户：</br>';
            echo 'sum:' . count($users) . "\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>plan</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {
                echo '<tr><td>' . $user->id . '</td><td>' . $user->user_name . '</td><td>' . $user->plan . '</td><td>' . $user->reg_date . '</td><td>' . date('Y-m-d H:i:s', $user->last_check_in_time) . '</td><td>' . date('Y-m-d H:i:s', $user->t) . '</td><td>' . $user->usedTraffic() . '/' . $user->enableTraffic() . "</td></tr>\n";
            }
            echo '</tbody></table></br>';
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
            ->get();
        if (!$users->isEmpty()) {
            echo date('Y-m-d H:i:s', time()) . ' 删除以下长时间未签到用户：</br>';
            echo 'sum:' . count($users) . "\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>plan</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {
                echo '<tr><td>' . $user->id . '</td><td>' . $user->user_name . '</td><td>' . $user->plan . '</td><td>' . $user->reg_date . '</td><td>' . date('Y-m-d H:i:s', $user->last_check_in_time) . '</td><td>' . date('Y-m-d H:i:s', $user->t) . '</td><td>' . $user->usedTraffic() . '/' . $user->enableTraffic() . "</td></tr>\n";
            }
            echo '</tbody></table></br>';
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
            User::where('t', '<', $t)
                ->where('reg_date', '<', $period)
                ->where('plan', '!=', 'C')
                ->where('enable', 1)
                ->orderBy('t')
                ->update(['enable' => 0]);
            echo date('Y-m-d H:i:s', time()) . ' 冻结以下用户：</br>';
            echo 'sum:' . count($users) . "\n";
            echo "<table><thead><tr><th>uid</th><th>用户名</th><th>plan</th><th>注册时间</th><th>上次签到时间</th><th>上次使用时间(sort)</th><th>流量</th></tr></thead><tbody>\n";
            foreach ($users as $user) {

                $arr['user_name'] = $user->user_name;
                try {
                    Mail::send($user->email, '账号冻结提醒 - Shadowsky', 'news/freeze-report.tpl', $arr, []);
                } catch (\Exception $e) {
                    echo $e->getMessage() . "\n";
                }

                echo '<tr><td>' . $user->id . '</td><td>' . $user->user_name . '</td><td>' . $user->plan . '</td><td>' . $user->reg_date . '</td><td>' . date('Y-m-d H:i:s', $user->last_check_in_time) . '</td><td>' . date('Y-m-d H:i:s', $user->t) . '</td><td>' . $user->usedTraffic() . '/' . $user->enableTraffic() . "</td></tr>\n";
            }
            echo '</tbody></table></br></br>';
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
        $Bnodes = Node::where('vps', 'bandwagon')->get();
        foreach ($Bnodes as $node) {
            try {
                $request          = $node->api;
                $result           = json_decode(file_get_contents($request));
                $usage            = round(($result->data_counter / $result->plan_monthly_data) * 100, 2);
                $node->node_usage = $usage;
                $node->save();
                echo $node->name . ":\t" . $usage . "\n";
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
            }
        }
        // vultr - jp1,jp2,jp4
        $Vnodes = Node::where('vps', 'vultr')->get();
        foreach ($Vnodes as $node) {
            try {
                $request          = $node->api;
                $result           = json_decode(file_get_contents($request));
                $subid            = $node->subid;
                $usage            = round(($result->$subid->current_bandwidth_gb / $result->$subid->allowed_bandwidth_gb) * 100, 2);
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

        NodeInfoLog::where('log_time', '<', strtotime('-2 minutes'))->delete();
        echo "clear NodeInfoLog\n";

        CheckInLog::where('checkin_at', '<', strtotime('-1 month'))->delete();
        echo "clear CheckinLog\n";

        PasswordReset::truncate();
        echo "clear PasswordResetLog\n";

        NodeOnlineLog::where('log_time', '<', strtotime('-2 minutes'))->delete();
        echo "clear NodeOnlineLog\n\n";

        AnnLog::where('ann_id', '<', Ann::orderBy('id', 'desc')->first()->id)->delete();
        echo "clear AnnLog\n\n";
    }

}
