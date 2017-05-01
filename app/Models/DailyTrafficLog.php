<?php


namespace App\Models;

class DailyTrafficLog extends Model
{
    protected $table = "user_daily_traffic_log";
    protected $fillable = ['uid','traffic','date'];
}