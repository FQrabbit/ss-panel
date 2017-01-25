<?php 

namespace App\Controllers;

use App\Models\PurchaseLog;
use App\Models\DonateLog;
use App\Models\User;
use App\Models\Shop;
use App\Services\Config;
use App\Services\Mail;
use App\Services\DbConfig;
use App\Utils\Tools;

/**
 * Payment Controller
 */
class PaymentController extends BaseController
{
	private $key,$apiid;

	public function __construct()
	{
		$this->key = DbConfig::get('apikey');
		$this->apiid = DbConfig::get('apiid');
	}

    /**
     * Get key
     */
    public function getKey()
    {
    	return DbConfig::get('apikey');
    }

    /**
     * Verify
     */
    public function verify($apikey, $alino)
    {
        if($apikey!=md5($this->key.$alino)) {
        	return false;
        }else {
        	$exist = PurchaseLog::where('out_trade_no', $alino)->first();
        	if ($exist) {
        		return false;
        	}
	        return true;
        }
    }

    /**
     * Add purchase log
     * 
     * $array=array(['uid'=>//必须
     * 				 'body'=>必须
     * 				 'price'=>必须
     * 				 'buy_date'=>
     * 				 'out_trade_no'=>
     * ])
     */
    public function addPurchaseLog($array)
    {
    	if (!isset($array['buy_date'])) {
    		$array['buy_date'] = Tools::toDateTime(time());
    	}
    	if (!isset($array['out_trade_no'])) {
    		$array['out_trade_no'] = '0000';
    	}
    	$log = new PurchaseLog();
    	$log->uid = $array['uid'];
    	$log->body = $array['body'];
    	$log->price = $array['price'];
    	$log->buy_date = $array['buy_date'];
    	$log->out_trade_no = $array['out_trade_no'];
    	$log->save();
    }

    /**
     * Do logic
     *
     * param: $uid, $product_id, $alino
     */
    public function doPay($uid, $product_id, $alino = '0000')
    {
        $user = User::find($uid);
        $product = Shop::find($product_id);
        // 添加购买记录
        $purchase_log_arr = array(
        	'uid' => $uid,
        	'body' => $product->name,
        	'price' => $product->price,
        	'out_trade_no' => $alino
        	);
        $this->addPurchaseLog($purchase_log_arr);

        // 购买之前的用户状态
        $pre['plan'] = $user->plan;
        $pre['type'] = $user->type;
        $pre['transfer_eanble_in_GB'] = round($user->enableTrafficInGB(),3);
        $pre['used_traffic_in_GB'] = round($user->usedTrafficInGB(),3);
        $pre['buy_date'] = $user->buy_date;
        $pre['expire_date'] = $user->expire_date;

        // 更新用户信息
        $user->plan = 'B';
        $user->type = $product->name;
        $user->buy_date = Tools::toDateTime(time());
        $user->user_type = $product->price;

        $transfer_to_add = $product->transfer;
        if ($product->isByTime()) { // 时间套餐
            $user->updateEnableTransfer($transfer_to_add);
            $user->updateExpireDate($product->name);
        }elseif ($product->isByMete()) { // 流量套餐
            if ($user->isExpire()) {
                $user->addTraffic($transfer_to_add);
            }else {
                if ($pre['type']=='试玩') {
                    $user->addTraffic($transfer_to_add);
                }else {
                    $user->updateEnableTransfer($pre['used_traffic_in_GB'] + $transfer_to_add);
                }
            }
            $user->resetExpireDate();
        }else { //试用套餐
            $user->addTraffic($transfer_to_add);
            $user->updateExpireDate($product->name);
        }
    	// return json_encode($pre);

        try {
            $arr1 = [
                'user_name' => $user->user_name,
                'type' => $user->type
            ];
            Mail::send($user->email, 'Shadowsky', 'news/purchase-report.tpl', $arr1, []);

            $to = 'zhwalker20@gmail.com';
            $title = 'Shadowsky - 用户购买通知';
            $tpl = 'news/general-report.tpl';
            $content = $user->user_name . '（uid:' . $user->id . ', port:' . $user->port . '）已购买' . $user->type . '套餐。金额：' . $product->price . '元。之前plan：' . $pre['plan'] . '。之前套餐：' . $pre['type'] . '。之前购买时间：' . $pre['buy_date'] . '。之前过期时间：' . $pre['expire_date'] . '。之前流量：' . $pre['used_traffic_in_GB'] . ' G / ' . $pre['transfer_eanble_in_GB'].' G。';
            $arr2 = [
                'content' => $content
            ];
            Mail::send($to, $title, $tpl, $arr2, []);

            $rs['ret'] = 1;
            $rs['msg'] = '发送邮箱通知成功。';
        } catch (\Exception $e) {
            $rs['ret'] = 0;
            $rs['msg'] = $e->getMessage();
        }
        return $rs;
    }

    /**
     * Get notify
     */
    public function doReturn($request, $response, $args)
    {
    	if ($request->isGet()) {
	    	$q = $request->getQueryParams();
    	}
    	if ($request->isPost()) {
	    	$q = $request->getParsedBody();
    	}
		$alino = $q['addnum'];
		$uid = $q['uid'];
		$price = $q['total'];
		$apikey = $q['apikey'];
        if(!$this->verify($apikey, $alino)) {
        	return $response->withStatus(302)->withHeader('Location', 'user');
        }
		$product_id = substr($alino, 9, 1);
        $this->doPay($uid, $product_id, $alino);

        return '购买成功';
    }

    public function prepay($request, $response, $args)
    {
    	if ($request->isGet()) {
    		$q = $request->getQueryParams();
    	}
    	if ($request->isPost()) {
    		$q = $request->getParsedBody();
    	}
		if (empty($q['total'])) {
			return '输入的信息不全！';
		}
		if (!is_numeric($q['total'])) {
			return '输入的金额不是数字！';
		}
		if ($q['total']<='0') {
			return '输入的金额小于等于0！';
		}
		if ($q['total']!=Shop::find($q['product_id'])->price) {
			return '商品价格不符';
		}
		$total = $q['total'];
		$uid = $q['uid'];
		$apiid = $this->apiid;
		$apikey = md5($this->key);
		$showurl = Config::getPublicConfig()['baseUrl'] . '/dopay';
		$addnum = 'alip' . $apiid . $q['product_id'] . User::find($uid)->port . time();
    	return "
		<form name='form1' action='http://api.web567.net/plugin.php?id=add:alipay' method='POST'>
			<input type='hidden' name='uid' value='".$uid."'>
			<input type='hidden' name='total' value='".$total."'>
			<input type='hidden' name='apiid' value='".$apiid."'>
			<input type='hidden' name='showurl' value='".$showurl."'>
			<input type='hidden' name='apikey' value='".$apikey."'>
			<input type='hidden' name='addnum' value='".$addnum."'>
		</form>
		<script>window.onload=function(){document.form1.submit();}</script> 
	";
    }

}