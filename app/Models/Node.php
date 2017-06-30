<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Models\NodeDailyTrafficLog;
use App\Utils\Tools;

class Node extends Model
{
    protected $table = "ss_node";

    public static function getAllMethod()
    {
        return [
            'none',
            'aes-128-cfb',
            'aes-192-cfb',
            'aes-256-cfb',
            'aes-128-ctr',
            'aes-192-ctr',
            'aes-256-ctr',
            'bf-cfb',
            'camellia-128-cfb',
            'camellia-192-cfb',
            'camellia-256-cfb',
            'rc4-md5',
            'rc4-md5-6',
            'aes-128-cfb8',
            'aes-192-cfb8',
            'aes-256-cfb8',
            'salsa20',
            'chacha20',
            'chacha20-ietf',
        ];
    }

    public static function getAllObfs()
    {
        return [
            'http_simple',
            'http_simple_compatible',
            'http_post',
            'http_post_compatible',
            'tls1.2_ticket_auth',
            'tls1.2_ticket_auth_compatible',
            'tls1.2_ticket_fastauth',
            'tls1.2_ticket_fastauth_compatible',
        ];
    }

    public static function getAllProtocol()
    {
        return [
            'auth_sha1_v4',
            'auth_sha1_v4_compatible',
            'auth_aes128_md5',
            'auth_aes128_sha1',
            'auth_chain_a',
        ];
    }

    public function vpsMerchant()
    {
        return $this->belongsTo('App\Models\VpsMerchant', 'vps');
    }

    public function getLastNodeInfoLog()
    {
        $id  = $this->attributes['id'];
        $log = NodeInfoLog::where('node_id', $id)->orderBy('id', 'desc')->first();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    public function getNodeUptime()
    {
        $log = $this->getLastNodeInfoLog();
        if ($log == null) {
            return "暂无数据";
        }
        return Tools::secondsToTime((int) $log->uptime);
    }

    public function getNodeLoad()
    {
        $log = $this->getLastNodeInfoLog();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->load;
    }

    public function getLastNodeOnlineLog()
    {
        $id  = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where("log_time", ">", (time() - 600))->orderBy('id', 'desc')->first();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    public function getNodeIp()
    {
        $name = $this->attributes['server'];
        $ip   = gethostbyname($name);
        return $ip;
    }

    public function getOnlineUserCount()
    {
        $log = $this->getLastNodeOnlineLog();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->online_user;
    }

    public function getTrafficFromLogs()
    {
        $traffic = $this->getTotalTraffic();
        if ($traffic == 0) {
            return "暂无数据";
        }
        return Tools::flowAutoShow($traffic);
    }

    public function getTotalTraffic()
    {
        $id      = $this->attributes['id'];
        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');
        return $traffic;
    }

    public function getPollCount($p)
    {
        $id = $this->attributes['id'];
        $c  = Vote::where('nodeid', $id)->where('poll', $p)->count();
        return $c;
    }

    public function getSSUrl($arr)
    {
        $ssurl = $arr['method'] . ':' . $arr['password'] . '@' . $arr['server'] . ':' . $arr['server_port'];
        return 'ss://' . Tools::base64_url_encode($ssurl);
    }

    public function getNewSSUrl($arr)
    {
        $ssurl = Tools::base64_url_encode($arr['method'] . ':' . $arr['password']) . '@' . $arr['server'] . ':' . $arr['server_port'] . '#' . $this->attributes['name'];
        return 'ss://' . $ssurl;
    }

    public function getSSRUrl($arr)
    {
        $ssurl = $arr['server'] . ':' . $arr['server_port'] . ':' . $arr['protocol'] . ':' . $arr['method'] . ':' . $arr['obfs'] . ':' . Tools::base64_url_encode($arr['password']) . '/?obfsparam=' . Tools::base64_url_encode($arr['obfs_param']) . '&remarks=' . Tools::base64_url_encode($this->attributes['name']) . '&group=' . Tools::base64_url_encode('shadowsky');
        return 'ssr://' . Tools::base64_url_encode($ssurl);
    }

    public function isFreeNode()
    {
        return $this->attributes['type'] == 0;
    }

    public function isPaidNode()
    {
        return $this->attributes['type'] == 1;
    }

    public function getTrafficUsage()
    {
        $id       = $this->attributes['id'];
        $transfer = $this->attributes['transfer'];
        if ($transfer == 0) {
            return 0;
        }
        $last_reset_date = $this->lastResetDate();
        $traffic         = NodeDailyTrafficLog::where('node_id', $id)->where('date', '>=', $last_reset_date)->sum('traffic');
        $traffic += $this->getTotalTraffic();
        $traffic_in_GB = Tools::flowToGB($traffic);
        $usage = round($traffic_in_GB / $transfer, 4) * 100;
        if ($usage > 100) {
            return 100;
        }
        return $usage;
    }

    public function lastResetDate()
    {
        $id        = $this->attributes['id'];
        $reset_day = $this->attributes['transfer_reset_day'];
        $reset_date_this_month = date('Y-m-d', strtotime(date('Y-m') . "-$reset_day"));
        if ($reset_date_this_month <= date('Y-m-d')) {
            $last_reset_day = $reset_date_this_month;
        } else {
            $last_reset_day = date('Y-m-d', strtotime($reset_date_this_month . ' -1 month'));
        }
        return $last_reset_day;
    }

    public function nextResetDate()
    {
        $id        = $this->attributes['id'];
        $reset_day = $this->attributes['transfer_reset_day'];
        $reset_day = date('Y-m-d', strtotime(date('Y-m') . "-$reset_day"));
        if ($reset_day <= date('Y-m-d')) {
            $next_reset_day = date('Y-m-d', strtotime($reset_day . ' +1 month'));
        } else {
            $next_reset_day = $reset_day;
        }
        return $next_reset_day;
    }

    /**
     * 距离下次重置日天数
     */
    public function daysUntilNextResetDate()
    {
        $secInADay      = 24 * 3600;
        $next_reset_day = $this->nextResetDate();
        $remain_days    = round((strtotime($next_reset_day) - time()) / $secInADay);
        if ($remain_days == 0) {
            $remain_days = 1;
        }
        return $remain_days;
    }

    /**
     * 获取剩余流量(GB)
     */
    public function transferLeft()
    {
        $total_transfer = $this->attributes['transfer'];
        if ($this->getTrafficUsage() > 100) {
            return 0;
        }
        return $total_transfer * ((100 - $this->node_usage) / 100);
    }

    public function avrTrafAvaTdInGB()
    {
        $traffic = round($this->transferLeft() / $this->daysUntilNextResetDate(), 2);
        return $traffic;
    }

    /**
     * 平均每日可用流量
     */
    public function showAverageTrafficAvailableToday()
    {
        $traffic = $this->avrTrafAvaTdInGB();
        if ($this->transfer) {
            return $traffic . 'GB';
        } else {
            return 'Unlimited';
        }
    }

    public function trafficOverusage()
    {
        if ($this->attributes['transfer'] == 0) {
            return false;
        }
        if ($this->getTotalTraffic() > Tools::toGB($this->avrTrafAvaTdInGB())) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * format the value of day of month to a string with 'am' or 'pm' suffix.
     * @return string. '5 am', ex.
     */
    public function formateResetDay()
    {
        return date('jS', strtotime((date('Y-m-') . $this->transfer_reset_day)));
    }
}
