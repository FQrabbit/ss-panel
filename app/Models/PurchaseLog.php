<?php


namespace App\Models;

class PurchaseLog extends Model
{
    protected $table = "purchase_info";
    public $fillable = ["uid", "body", "price", "buy_date", "out_trade_no"];
}