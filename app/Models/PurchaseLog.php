<?php

namespace App\Models;

class PurchaseLog extends Model
{
    protected $table = "purchase_info";
    public $fillable = ["uid", "body", "price", "buy_date", "out_trade_no"];

    public static function hasTransaction($out_trade_no)
    {
        if (self::where('out_trade_no', $out_trade_no)->first()) {
            return true;
        } else {
            return false;
        }
    }
}
