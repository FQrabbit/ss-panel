<?php

namespace App\Models;

class ExpenditureLog extends Model
{
    protected $table = "expenditure_log";

    public $timestamps = false;

    public function node()
    {
        return $this->belongsTo('App\Models\Node', 'node_id');
    }

    public function vpsMerchant()
    {
        return $this->belongsTo('App\Models\VpsMerchant', 'vps_merchant_id');
    }
}
