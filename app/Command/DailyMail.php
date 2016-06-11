<?php


namespace App\Command;

use App\Models\User;
use App\Services\Config;
use App\Services\Mail;

class DailyMail
{

    public static function sendDailyMail()
    {
        $users = User::all();
        foreach ($users as $user) {
            echo "Send daily mail to user: " . $user->id;
            $subject = Config::get('appName') . "-每日流量报告";
            $to = $user->email;
            try {
                Mail::send($to, $subject, 'news/daily-traffic-report.tpl', [
                    "user" => $user
                ], [
                ]);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public static function sendDbMail()
    {
        try {
            $to = "zhwalker20@gmail.com";
            $subject = "备份数据库";
            $file = ["/root/backup/database.sql"];
            Mail::send($to, $subject, 'news/backup-report.tpl', [], $file);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo date("Y-m-d H:i:s",time())."\n";
        echo "Send database backup successful\n\n";
    }

    public static function sendSiteMail()
    {
        try {
            $to = "zhwalker20@gmail.com";
            $subject = "备份网站";
            $file = ["/root/backup/site.tgz"];
            Mail::send($to, $subject, 'news/backup-report.tpl', [], $file);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo date("Y-m-d H:i:s",time())."\n";
        echo "Send website backup successful\n\n";
    }

    public static function sendGeneralEmail()
    {
        $users = User::all();
        // $users = User::where("id", 1)->get();
        foreach ($users as $user) {
            try {
                $to = $user->email;
                $subject = "Shadowsky";
                Mail::send($to, $subject, 'news/general-report.tpl', [], []);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            echo "Send to " . $user->user_name . " successfully\n";
        }
    }
}