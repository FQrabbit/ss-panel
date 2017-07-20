<?php


namespace App\Command;

use App\Services\Mail;

class EmailDb
{
	public static function sendDb()
	{
		try {
			$to = "zhwalker20@gmail.com";
			$subject = "备份数据库";
			$file = ["/root/backup/v3_2016-05-30_12-00-01.sql"];
			Mail::send($to, $subject, 'news/daily-traffic-report.tpl', [], $file);
		} catch (\Exception $e) {
		    echo $e->getMessage();
		}
	}
}

EmailDb::sendDb();