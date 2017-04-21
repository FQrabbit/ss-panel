<?php


namespace App\Command;

use App\Models\User;
use App\Models\Ann;
use App\Services\Config;
use App\Services\Mail;

class DailyMail
{
    public $adminEmail;

    function __construct()
    {
        $this->adminEmail = Config::get('adminEmail');
    }

    public static function sendDailyMail()
    {
        // $users = User::where("id", 1)->get(); //test
        $users = User::all();
        if ($users) {
            echo "Sent monthly mail to users, count: " . count($users) . "\n";
            foreach ($users as $user) {
                $subject = Config::get('appName') . "-月流量报告";
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
    }

    public static function sendDbMail()
    {
        try {
            $to = $this->adminEmail;
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
            $to = $this->adminEmail;
            $subject = "备份网站";
            $file = ["/root/backup/site.tgz"];
            Mail::send($to, $subject, 'news/backup-report.tpl', [], $file);
            echo date("Y-m-d H:i:s",time())."\n";
            echo "Send website backup successful\n\n";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function sendAnnMail()
    {
        // $users = User::all();
        $ann = Ann::orderBy('id', 'desc')->get()->first();
        if ($ann->title) {
            $users = User::where("id", 1)->get();
            $title = $ann->title;
            $content = $ann->content;
            $arr = [
                "title" => $title,
                "content" => $content,
                "user" => ""
            ];
            foreach ($users as $user) {
                $arr["user"] = $user;
                try {
                    $to = $user->email;
                    $subject = "Shadowsky - ".$title;
                    Mail::send($to, $subject, 'news/announcement.tpl', $arr, []);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
                echo "Sent to " . $user->user_name . "\n";
            }
        }else {
            echo "空\n";
        }
    }
}