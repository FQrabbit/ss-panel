<?php

namespace App\Models;

/**
 * User Model
 */

use App\Services\Config;
use App\Utils\Hash;
use App\Utils\Tools;
use App\Models\Node;
use App\Models\CheckInLog;
use App\Models\Vote;
use App\Models\AnnLog;

class User extends Model

{
    protected $table = "user";

    public $isLogin;

    public $isAdmin;

    protected $casts = [
        "t" => 'int',
        "u" => 'int',
        "d" => 'int',
        "port" => 'int',
        "transfer_enable" => 'float',
        "enable" => 'int',
        'is_admin' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['pass', 'last_get_gift_time', 'last_rest_pass_time', 'reg_ip', 'is_email_verify', 'user_name', 'ref_by', 'is_admin'];

    public function getGravatarAttribute()
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://secure.gravatar.com/avatar/$hash";
    }

    public function isAdmin()
    {
        return $this->attributes['is_admin'];
    }

    public function isDonator()
    {
        return $this->attributes['ref_by'] == 3;
    }

    public function lastSsTime()
    {
        if ($this->attributes['t'] == 0) {
            return "从未使用喵";
        }
        return Tools::toDateTime($this->attributes['t']);
    }

    public function lastCheckInTime()
    {
        if ($this->attributes['last_check_in_time'] == 0) {
            return "从未签到";
        }
        return Tools::toDateTime($this->attributes['last_check_in_time']);
    }

    // 本月签到次数
    public function checkInTimes()
    {
        $m = date("m");
        $d = date("d");
        $Y = date("Y");
        $monthBeginTime = mktime(0, 0, 0, $m, 1, $Y);
        $num = CheckInLog::where("user_id", $this->attributes['id'])
                            ->where("checkin_at", ">", $monthBeginTime)
                            ->count();
        return $num;
    }

    public function regDate()
    {
        return $this->attributes['reg_date'];
    }

    public function updatePassword($pwd)
    {
        $this->pass = Hash::passwordHash($pwd);
        $this->save();
    }

    public function updateSsPwd($pwd)
    {
        $this->passwd = $pwd;
        $this->save();
    }

    public function updateMethod($method)
    {
        $this->method = $method;
        $this->save();
    }

    public function updatePlan($plan)
    {
        $this->plan = $plan;
        $this->save();
    }

    /*
     * @param traffic 单位 GB
     */
    public function updateEnableTransfer($traffic)
    {
        $this->transfer_enable = Tools::toGB($traffic);
        $this->save();
    }

    public function updateBuyDate($buy_date)
    {
        if (!$buy_date) {
            $buy_date = Tools::toDateTime();
        }
        $this->buy_date = $buy_date;
        $this->save();
    }

    public function addInviteCode()
    {
        $uid = $this->attributes['id'];
        $code = new InviteCode();
        $code->code = Tools::genRandomChar(32);
        $code->user = $uid;
        $code->save();
    }

    public function addManyInviteCodes($num)
    {
        for ($i = 0; $i < $num; $i++) {
            $this->addInviteCode();
        }
    }

    public function trafficUsagePercent()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transferEnable = $this->attributes['transfer_enable'];
        if ($transferEnable == 0) {
            return 0;
        }
        $percent = $total / $transferEnable;
        $percent = round($percent, 2);
        $percent = $percent * 100;
        return $percent;
    }

    public function enableTraffic()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable);
    }

    public function enableTrafficInGB()
    {
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowToGB($transfer_enable);
    }

    public function usedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        return Tools::flowAutoShow($total);
    }

    public function unusedTraffic()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transfer_enable = $this->attributes['transfer_enable'];
        return Tools::flowAutoShow($transfer_enable - $total);
    }

    public function unusedTrafficInB()
    {
        $total = $this->attributes['u'] + $this->attributes['d'];
        $transfer_enable = $this->attributes['transfer_enable'];
        return ($transfer_enable - $total);
    }

    public function isAbleToCheckin()
    {
        $last = $this->attributes['last_check_in_time'];
        $hour = Config::get('checkinTime');
        if ($last + $hour * 3600 < time()) {
            return true;
        }
        return false;
    }

    /*
     * @param traffic 单位 GB
     */
    public function addTraffic($traffic)
    {
        $this->transfer_enable += Tools::toGB($traffic);
        $this->save();
    }

    public function inviteCodes()
    {
        $uid = $this->attributes['id'];
        return InviteCode::where('user_id', $uid)->get();
    }
    
    public function getUserClassName()
    {
        $userplan = $this->attributes['plan'];
        switch ($userplan) {
                case 'B':
                    $class = "付费用户";
                    break;

                case 'C':
                    $class = "特殊用户";
                    break;

                default:
                    $class = "免费用户";
                    break;
            }
            return $class;
    }

    public function getPollOfNode($nodeid)
    {
        $uid = $this->attributes['id'];
        $v = Vote::where('uid', $uid)->where('nodeid', $nodeid)->first();
        if ($v) {
            return $v->poll;
        }
        return 0;
    }

    public function  get_expire_date(){
        return $this->attributes['expire_date'];
    }

    public function updateExpireDate($time){
        $expiredate = $this->get_expire_date();
        $expiretime = strtotime($expiredate);
        if($expiretime<time()){
            switch ($time) {
                case 'A':
                    $expiretime = strtotime("+1 Month",time());
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'B':
                    $expiretime = strtotime("+3 Months",time());
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'C':
                    $expiretime = strtotime("+1 Year",time());
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'D':
                    $expiretime = strtotime("+3 Days",time());
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                default:
                    break;
            }          
        }else{
            switch ($time) {
                case 'A':
                    $expiretime = strtotime("+1 Month",$expiretime);
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'B':
                    $expiretime = strtotime("+3 Months",$expiretime);
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'C':
                    $expiretime = strtotime("+1 Year",$expiretime);
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                case 'D':
                    $expiretime = strtotime("+3 Days",$expiretime);
                    $expiredate = date("Y-m-d H:i:s",$expiretime);
                    break;
                default:
                    break;
            }
        }
        $this->expire_date = $expiredate;
        $this->save();
    }

    public function resetExpireDate()
    {
        $this->expire_date = "0000-00-00 00:00:00";
        $this->save();
    }

    public function getFormatedDateTime($datetime) {
        return strftime('%Y-%m-%dT%H:%M:%S', strtotime($datetime));
    }

    public function getReadStatusOfAnn($ann_id)
    {
        return AnnLog::where('user_id', $this->id)->where('ann_id', $ann_id)->first()->read_status;
    }
}
