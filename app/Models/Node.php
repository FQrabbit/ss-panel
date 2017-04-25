<?php

namespace App\Models;

/**
 * Node Model
 */

use App\Utils\Tools;

class Node extends Model

{
    protected $table = "ss_node";

    public static function getAllMethod()
    {
        return [
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
            'chacha20-ietf'
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
            'tls1.2_ticket_auth_compatible'
        ];
    }

    public static function getAllProtocol()
    {
        return [
            'auth_sha1_v4',
            'auth_sha1_v4_compatible',
            'auth_aes128_md5',
            'auth_aes128_sha1'
        ];
    }

    public function getLastNodeInfoLog()
    {
        $id = $this->attributes['id'];
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
        return Tools::secondsToTime((int)$log->uptime);
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
        $id = $this->attributes['id'];
        $log = NodeOnlineLog::where('node_id', $id)->where("log_time", ">", (time()-300))->orderBy('id', 'desc')->first();
        if ($log == null) {
            return null;
        }
        return $log;
    }

    public function getNodeIp()
    {
        $name = $this->attributes['server'];
        $ip = gethostbyname($name);
        return $ip;
    }

    function getOnlineUserCount()
    {
        $log = $this->getLastNodeOnlineLog();
        if ($log == null) {
            return "暂无数据";
        }
        return $log->online_user;
    }

    function getTrafficFromLogs()
    {
        $traffic = $this->getTotalTraffic();
        if ($traffic == 0) {
            return "暂无数据";
        }
        return Tools::flowAutoShow($traffic);
    }

    public function getTotalTraffic()
    {
        $id = $this->attributes['id'];
        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');
        return $traffic;
    }

    function getPollCount($p)
    {
        $id = $this->attributes['id'];
        $c = Vote::where('nodeid', $id)->where('poll', $p)->count();
        return $c;
    }
    
    public function getSSUrl($arr)
    {
        $ssurl = $arr['method'] . ":" . $arr['password'] . "@" . $arr['server'] . ":" . $arr['server_port'];
        return "ss://" . base64_encode($ssurl);
    }
    
    public function getNewSSUrl($arr)
    {
        $ssurl = base64_encode($arr['method'] . ":" . $arr['password']) . "@" . $arr['server'] . ":" . $arr['server_port'];
        return "ss://" . $ssurl;
    }
    
    public function getSSRUrl($arr)
    {
        $ssurl = $arr['server'] . ":" . $arr['server_port'] . ":" . $arr['protocol'] . ":" . $arr['method'] . ":" . $arr['obfs'] . ":" . Tools::base64_url_encode($arr['password']) . "/?obfsparam=" . Tools::base64_url_encode($arr['obfs_param']) . "&remarks=" . Tools::base64_url_encode($this->attributes['name']) . "&group=" . Tools::base64_url_encode("shadowsky");
        return "ssr://" . Tools::base64_url_encode($ssurl);
    }

    public function isFreeNode()
    {
        return $this->attributes['type'] == 0;
    }

    public function isPaidNode()
    {
        return $this->attributes['type'] == 1;
    }
}
