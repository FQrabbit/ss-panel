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
        $id = $this->attributes['id'];
    }

    function getTrafficFromLogs()
    {
        $id = $this->attributes['id'];
        $traffic = TrafficLog::where('node_id', $id)->sum('u') + TrafficLog::where('node_id', $id)->sum('d');
        if ($traffic == 0) {
            return "暂无数据";
        }
        return Tools::flowAutoShow($traffic);
    }

    function getPollCount($p)
    {
        $id = $this->attributes['id'];
        $c = Vote::where('nodeid', $id)->where('poll', $p)->count();
        return $c;
    }

}
