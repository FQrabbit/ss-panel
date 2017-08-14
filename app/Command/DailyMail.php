<?php

namespace App\Command;

use App\Models\Ann;
use App\Models\User;
use App\Services\Config;
use App\Services\Mail;

class DailyMail
{

    public static function sendDailyMail()
    {
        $users = User::all();
        // $users = User::where("id", 1)->get(); //test
        if ($users) {
            $log = '';
            $count = 0;
            foreach ($users as $user) {
                if ($user->product && $user->product->isByTime()) {
                    continue;
                } else {
                    $count++;
                    $subject = Config::get('appName') . ' - 月流量报告';
                    $to      = $user->email;
                    try {
                        // Mail::send($to, $subject, 'news/daily-traffic-report.tpl', ['user' => $user], []);
                        // echo "Sent Traffic Report Email to " . $user->user_name . "\n";
                    } catch (\Exception $e) {
                        $log .= $e->getMessage();
                    }
                }
            }
            
            $date = date('Y-m-d H:i:s');
            $log .= "$date Sent Monthly Traffic Report Email - Sum: $count\n\n";
            Job::appendLog($log, 'site.log');
        }
    }

    public static function sendDbMail()
    {
        $log = '';
        try {
            $to      = Config::get('adminEmail');
            $subject = '备份数据库';
            $file    = ['/root/backup/database.sql'];
            Mail::send($to, $subject, 'news/backup-report.tpl', [], $file);
        } catch (\Exception $e) {
            $log .= $e->getMessage();
        }

        $date = date('Y-m-d H:i:s');
        $log .= "$date Sent database backup\n\n";
        Job::appendLog($log, 'backup.log');
    }

    public static function sendSiteMail()
    {
        $log = '';
        try {
            $to      = Config::get('adminEmail');
            $subject = '备份网站';
            $file    = ['/root/backup/site.tgz'];
            Mail::send($to, $subject, 'news/backup-report.tpl', [], $file);

            $date = date('Y-m-d H:i:s');
            $log .= "$date Sent website backup\n\n";
        } catch (\Exception $e) {
            $log .= $e->getMessage();
        }
        Job::appendLog($log, 'backup.log');
    }

    public static function sendAnnMail()
    {
        // $users = User::all();
        $ann = Ann::orderBy('id', 'desc')->get()->first();
        if ($ann->title) {
            $users   = User::where("id", 1)->get();
            $title   = $ann->title;
            $content = $ann->content;
            $arr     = [
                "title"   => $title,
                "content" => $content,
                "user"    => "",
            ];
            foreach ($users as $user) {
                $arr["user"] = $user;
                try {
                    $to      = $user->email;
                    $subject = "Shadowsky - " . $title;
                    Mail::send($to, $subject, 'news/announcement.tpl', $arr, []);
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                echo "Sent to " . $user->user_name . "\n";
            }
        } else {
            echo "空\n";
        }
    }
}
