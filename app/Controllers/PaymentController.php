<?php

namespace App\Controllers;

use App\Models\DonateLog;
use App\Models\PurchaseLog;
use App\Models\Shop;
use App\Models\User;
use App\Services\Config;
use App\Services\DbConfig;
use App\Services\Mail;
use App\Utils\Tools;

/**
 * Payment Controller
 */
class PaymentController extends BaseController
{
    private $key, $apiid;
    public $adminEmail, $feeRate = 0.03;

    public function __construct()
    {
        $this->key        = DbConfig::get('apikey');
        $this->apiid      = DbConfig::get('apiid');
        $this->adminEmail = Config::get('adminEmail');
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
        if ($apikey != md5($this->key . $alino)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * get notify 同步、异步通知
     * @return string 返回提示
     */
    public function doReturn($request, $response, $args)
    {
        $q = $this->getRequestBodyArray($request);

        $alino  = $q['addnum'];
        $uid    = $q['uid'];
        $price  = $q['total'];
        $apikey = $q['apikey'];

        if (!$this->verify($apikey, $alino)) {
            return $response->withStatus(302)->withHeader('Location', 'user');
        }

        /**
         * 商品id, 捐助 id 为 0
         * @var int
         */
        $product_id = intval(substr($alino, 9, 2));

        if ($product_id == 0) {
            if (DonateLog::hasTransaction($alino)) {
                return $response->withStatus(302)->withHeader('Location', 'user');
            } else {
                $this->doDonate($uid, $price, $alino, $this->feeRate);
                return '感谢您的捐助';
            }
        } else {
            if (PurchaseLog::hasTransaction($alino)) {
                return $response->withStatus(302)->withHeader('Location', 'user');
            } else {
                $this->doPay($uid, $product_id, $alino, $this->feeRate);
                return '购买成功';
            }
        }
    }

    /**
     * Get Request Body Array. GET and POST
     * @param  $request
     * @return array
     */
    public function getRequestBodyArray($request)
    {
        if ($request->isGet()) {
            $q = $request->getQueryParams();
        }
        if ($request->isPost()) {
            $q = $request->getParsedBody();
        }
        return $q;
    }

    public function addPurchaseLog($array)
    {
        $log               = new PurchaseLog();
        $log->uid          = $array['uid'];
        $log->product_id   = $array['product_id'];
        $log->body         = $array['body'];
        $log->price        = $array['price'];
        $log->buy_date     = Tools::toDateTime(time());
        $log->out_trade_no = $array['out_trade_no'];
        $log->fee          = $array['fee'];
        $log->save();
    }

    public function addDonateLog($array)
    {
        $log           = new DonateLog();
        $log->uid      = $array['uid'];
        $log->money    = $array['money'];
        $log->datetime = Tools::toDateTime(time());
        $log->trade_no = $array['trade_no'];
        $log->fee      = $array['fee'];
        $log->save();
    }

    public function doDonate($uid, $money, $alino, $feeRate)
    {
        $user = User::find($uid);
        // 添加购买记录
        $donate_log_arr = array(
            'uid'      => $uid,
            'money'    => $money,
            'trade_no' => $alino,
            'fee'      => $money * $feeRate,
        );
        $this->addDonateLog($donate_log_arr);
        $user->becomeDonator();
        $user->addMoney($money);
        $user->activate();

        try {
            $arr1 = [
                'user_name'   => $user->user_name,
                'money'       => $money,
                'total_money' => $user->money,
            ];
            Mail::send($user->email, 'Shadowsky', 'news/donate-report.tpl', $arr1, []);

            $content = "{$user->user_name}(uid: {$uid})捐助Shadowsky{$money}元。总捐助额：{$user->money}。";
            Mail::send($this->adminEmail, 'Shadowsky - 用户捐助通知', 'news/general-report.tpl', ['content' => $content], []);

            $rs['ret'] = 1;
            $rs['msg'] = '发送邮箱通知成功。';
        } catch (\Exception $e) {
            $rs['ret'] = 0;
            $rs['msg'] = $e->getMessage();
        }
        return $rs;
    }

    /**
     * Do logic
     *
     * param: $uid, $product_id, $alino
     */
    public function doPay($uid, $product_id, $alino, $feeRate)
    {
        $user    = User::find($uid);
        $product = Shop::find($product_id);
        // 添加购买记录
        $purchase_log_arr = array(
            'uid'          => $uid,
            'product_id'   => $product_id,
            'body'         => $product->name,
            'price'        => $product->price,
            'out_trade_no' => $alino,
            'fee'          => $feeRate * $product->price,
        );
        $this->addPurchaseLog($purchase_log_arr);

        // 购买之前的用户状态
        $pre['plan']                  = $user->plan;
        $pre['type']                  = $user->type;
        $pre['product_id']            = $user->product_id;
        $pre['transfer_eanble_in_GB'] = round($user->enableTrafficInGB(), 3);
        $pre['used_traffic_in_GB']    = round($user->usedTrafficInGB(), 3);
        $pre['buy_date']              = $user->buy_date;
        $pre['expire_date']           = $user->expire_date;

        /**
         * 处理流量和过期日期
         */
        $transfer_to_add = $product->transfer;
        if ($product->isByTime()) {
            // 时间套餐
            $user->addTraffic($transfer_to_add);
            $user->updateExpireDate($product->id);
        } elseif ($product->isByMete()) {
            // 流量套餐
            if ($user->isExpire()) {
                $user->addTraffic($transfer_to_add);
            } else {
                if ($user->product_id && $user->product->name == '试玩') {
                    $user->addTraffic($transfer_to_add);
                } else {
                    $user->updateEnableTransfer($pre['used_traffic_in_GB'] + $transfer_to_add);
                }
            }
            $user->resetExpireDate();
        } else {
            //试用套餐
            $user->addTraffic($transfer_to_add);
            $user->updateExpireDate($product->id);
        }

        // 更新用户信息
        $user->plan       = 'B';
        $user->product_id = $product->id;
        $user->type       = $product->name;
        $user->buy_date   = Tools::toDateTime(time());
        $user->user_type  = $product->price;
        $user->save();

        try {
            $arr1 = [
                'user'    => $user,
                'product' => $product,
            ];
            Mail::send($user->email, 'Shadowsky', 'news/purchase-report.tpl', $arr1, []);

            $title = 'Shadowsky - 用户购买通知';
            $tpl   = 'news/new-purchase.tpl';
            $arr2  = [
                'user' => $user,
                'pre'  => $pre,
            ];
            // return $user;
            Mail::send($this->adminEmail, $title, $tpl, $arr2, []);

            $rs['ret'] = 1;
            $rs['msg'] = '发送邮箱通知成功。';
        } catch (\Exception $e) {
            $rs['ret'] = 0;
            $rs['msg'] = $e->getMessage();
        }
        return $rs;
    }

    public function prepay($request, $response, $args)
    {
        $q = $this->getRequestBodyArray($request);
        if (empty($q['total'])) {
            return 'Empty price';
        }
        if (!is_numeric($q['total'])) {
            return 'Price is not a number';
        }
        if ($q['total'] <= '0') {
            return 'Invalid price value';
        }
        if (!is_numeric($q['product_id']) || $q['product_id'] < 0) {
            return 'Invalid input';
        }
        if ($q['product_id'] > 0) {
            $product = Shop::find($q['product_id']);
            if ($product) {
                if ($q['total'] != $product->price) {
                    return 'Price do not match';
                }
            } else {
                return 'Could\'t find this product';
            }
        }
        $q['product_id'] = sprintf('%02d', $q['product_id']);
        $total   = $q['total'];
        $uid     = $q['uid'];
        $apiid   = $this->apiid;
        $apikey  = md5($this->key);
        $showurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/dopay";
        $addnum  = 'alip' . $apiid . $q['product_id'] . User::find($uid)->port . time();
        return "
        <form name='form1' action='https://api.jsjapp.com/plugin.php?id=add:alipay' method='POST'>
            <input type='hidden' name='uid' value='" . $uid . "'>
            <input type='hidden' name='total' value='" . $total . "'>
            <input type='hidden' name='apiid' value='" . $apiid . "'>
            <input type='hidden' name='showurl' value='" . $showurl . "'>
            <input type='hidden' name='apikey' value='" . $apikey . "'>
            <input type='hidden' name='addnum' value='" . $addnum . "'>
        </form>
        <script>window.onload=function(){document.form1.submit();}</script>
        ";
    }

}
