<?php


namespace App\Models;

class NodeDailyTrafficLog extends Model
{
    protected $table = "node_daily_traffic_log";
    protected $fillable = ['node_id','traffic','date'];
}