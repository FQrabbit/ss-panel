<?php


namespace App\Models;

use App\Utils\Tools;

class TrafficLog extends Model
{
    protected $table = "user_traffic_log";

    public function node()
    {
        return $this->belongsTo('APP\Models\Node');
    }

    public function user()
    {
        return $this->belongsTo('APP\Models\User');
    }

    public function totalUsed()
    {
        return Tools::flowAutoShow($this->attributes['u'] + $this->attributes['d']);
    }

    public function logTime()
    {
        return Tools::toDateTime($this->attributes['log_time']);
    }
}