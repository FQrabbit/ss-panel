<?php


namespace App\Models;

use App\Utils\Tools;


class CheckInLog extends Model
{
    protected $table = "ss_checkin_log";

    /**
     * @return string
     */
    public function traffic()
    {
        return Tools::flowAutoShow($this->attributes['traffic']);
    }

    /**
     * @return mixed
     */
    public function CheckInTime()
    {
        return Tools::toDateTime($this->attributes['checkin_at']);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}