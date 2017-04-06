<?php

namespace App\Models;

class DonateLog extends Model
{
    protected $table = "donate_log";

    public $timestamps = false;

    public static function hasTransaction($trade_no)
    {
        if (self::where('trade_no', $trade_no)->first()) {
            return true;
        } else {
            return false;
        }
    }
}
