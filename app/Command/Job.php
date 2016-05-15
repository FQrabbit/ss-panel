<?php

namespace App\Command;

use App\Models\User;
use App\Services\Config;
use App\Services\Mail;

class Job
{

    public static function resetUserPlan()
    {
        $users = User::where("expire_date", ">", "0000-00-00 00:00:00")->where("plan", "B")->get();
        foreach ($users as $user) {
        	echo $user->id."\n";
        }
    }

}