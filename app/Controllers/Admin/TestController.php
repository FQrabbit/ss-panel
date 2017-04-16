<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Services\Mail;
use App\Utils\Tools;
use App\Models\PurchaseLog;
use App\Models\DonateLog;

class TestController extends AdminController
{
    public function sendMail($request, $response, $args)
    {
        return $this->view()->display('admin/test/sendmail.tpl');
    }

    public function sendMailPost($request, $response, $args)
    {
        $to = $request->getParam('email');
        try {
            Mail::send($to, "Test", 'test.tpl', [
                'time' => Tools::toDateTime(time())
            ], [
                BASE_PATH . '/LICENSE'
            ]);
            $res = [
                "ret" => 1,
                "msg" => "ok"
            ];
        } catch (\Exception $e) {
            $res = [
                "ret" => 0,
                "msg" => $e->getMessage()
            ];
        }
        return $this->echoJson($response, $res);
    }

    public function doSomeJobs()
    {
        // $logs = DonateLog::where('trade_no','not like','%alip%')->where('trade_no','not like','%2016%')->get();
        // foreach ($logs as $log) {
        //     echo $new_trade_no = strtotime($log->datetime).$log->uid;
        //     echo "<br/>";
        //     $log->trade_no = $new_trade_no;
        //     $log->save();
        //     echo "更新".$log->uid."<br>";
        // }
        // return 'finished';
        
    	/**
    	 * 清除重复交易记录
    	 */
        // $logs = DonateLog::all();
        // foreach ($purchase_logs as $log) {
        //     if (DonateLog::where('trade_no', $log->trade_no)->count()>1) {
        //         $log->delete();
        //         echo $log->out_trade_no."重复，已删除此条记录。<br>";
        //     }
        // }

        // 
        /**
         * [$feeRate description]
         * @var float
         */
        $feeRate = 0.03;
        $logs = DonateLog::where('trade_no', 'like', '%alip%')->get();
        foreach ($logs as $log) {
        	$log->fee = $log->money * $feeRate;
        	$log->save();
        }
        echo count($logs);
        echo "Hello";
    }
}