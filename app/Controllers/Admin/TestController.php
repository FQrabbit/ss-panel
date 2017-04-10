<?php


namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Services\Mail;
use App\Utils\Tools;
use App\Models\PurchaseLog;

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
    	/**
    	 * 清除重复交易记录
    	 */
        // $purchase_logs = PurchaseLog::all();
        // foreach ($purchase_logs as $log) {
        //     if (PurchaseLog::where('out_trade_no', $log->out_trade_no)->count()>1) {
        //         $log->delete();
        //         echo $log->out_trade_no."重复，已删除此条记录。<br>";
        //     }
        // }
        
        // $purchase_logs = PurchaseLog::where('out_trade_no','=','0000')->get();
        // foreach ($purchase_logs as $log) {
        //     $new_out_trade_no = strtotime($log->buy_date).$log->uid;
        //     $log->out_trade_no = $new_out_trade_no;
        //     $log->save();
        //     echo "更新".$log->uid."<br>";
        // }
        // return 'finished';
        // 
        /**
         * [$feeRate description]
         * @var float
         */
        // $feeRate = 0.03;
        // $purchase_logs = PurchaseLog::where('out_trade_no', 'not like', '%alip%')->get();
        // foreach ($purchase_logs as $log) {
        // 	$log->fee = $log->price * $feeRate;
        // 	$log->save();
        // }
        // echo count($purchase_logs);
        echo "Hello";
    }
}