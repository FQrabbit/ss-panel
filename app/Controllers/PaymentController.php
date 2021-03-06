<?php

namespace App\Controllers;

use App\Models\DonateLog;
use App\Models\PurchaseLog;
use App\Models\Shop;
use App\Models\User;
use App\Services\Auth;
use App\Services\Config;
use App\Services\DbConfig;
use App\Services\Mail;
use App\Utils\Tools;

/**
 * Payment Controller
 */
class PaymentController extends BaseController
{
    private $key, $apiid, $apikey;
    public $adminEmail, $alipayFeeRate, $wechatFeeRate;
    public $uid, $product_id, $total, $addnum, $payment_method, $feeRate;

    public function __construct()
    {
        $this->key            = DbConfig::get('apikey');
        $this->apiid          = DbConfig::get('apiid');
        $this->adminEmail     = Config::get('adminEmail');
        $this->payment_method = '';
        $this->feeRate        = 0;
        $this->alipayFeeRate  = 0.03;
        $this->wechatFeeRate  = 0.05;
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

    /**
     * Take out product id from addnum
     * @param  [char] $addnum [order number]
     * @return [int]          [product id]
     */
    public function takeProductId()
    {
        return intval(substr($this->addnum, 8, 2));
    }

    /**
     * Verify
     */
    public function verify()
    {
        if ($this->apikey != md5($this->key . $this->addnum . $this->uid . $this->total)) {
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

        $this->uid          = $q['uid'];
        $this->total        = $q['total'];
        $this->addnum       = $q['addnum'];
        $this->apikey       = $q['apikey'];
        $payment_method_num = $q['sort'];
        $this->product_id   = $this->takeProductId();

        switch ($payment_method_num) {
            case 1:
                $this->feeRate        = $this->alipayFeeRate;
                $this->payment_method = '支付宝';
                break;
            case 2:
                $this->feeRate        = $this->wechatFeeRate;
                $this->payment_method = '微信';
                break;
            case 3:
                $this->feeRate        = $this->wechatFeeRate;
                $this->payment_method = 'QQ';
                break;
            default:
                $this->feeRate        = 0;
                $this->payment_method = '';
                break;
        }

        if (!$this->verify()) {
            return $response->withStatus(302)->withHeader('Location', 'user');
        }

        if ($this->product_id == 0) {
            if (DonateLog::hasTransaction($this->addnum)) {
                return $response->withStatus(302)->withHeader('Location', 'user');
            } else {
                $this->doDonate();
                return '感谢您的捐助';
            }
        } else {
            if (PurchaseLog::hasTransaction($this->addnum)) {
                return $response->withStatus(302)->withHeader('Location', 'user');
            } else {
                $this->doPay();
                return '购买成功';
            }
        }
    }

    public function addPurchaseLog($array)
    {
        $log                 = new PurchaseLog();
        $log->uid            = $array['uid'];
        $log->product_id     = $array['product_id'];
        $log->body           = $array['body'];
        $log->price          = $array['price'];
        $log->buy_date       = Tools::toDateTime(time());
        $log->out_trade_no   = $array['out_trade_no'];
        $log->fee            = $array['fee'];
        $log->payment_method = $array['payment_method'];
        $log->save();
    }

    public function addDonateLog($array)
    {
        $log                 = new DonateLog();
        $log->uid            = $array['uid'];
        $log->money          = $array['money'];
        $log->datetime       = Tools::toDateTime(time());
        $log->trade_no       = $array['trade_no'];
        $log->fee            = $array['fee'];
        $log->payment_method = $array['payment_method'];
        $log->save();
    }

    public function doDonate()
    {
        $user = User::find($this->uid);
        // 添加购买记录
        $donate_log_arr = array(
            'uid'            => $this->uid,
            'money'          => $this->total,
            'trade_no'       => $this->addnum,
            'fee'            => $this->total * $this->feeRate,
            'payment_method' => $this->payment_method,
        );
        $this->addDonateLog($donate_log_arr);
        $user->becomeDonator();
        $user->addMoney($this->total);
        $user->activate();

        try {
            $arr1 = [
                'user_name'   => $user->user_name,
                'money'       => $this->total,
                'total_money' => $user->money,
            ];
            Mail::send($user->email, 'Shadowsky', 'news/donate-report.tpl', $arr1, []);

            $content = "{$user->user_name}(uid: {$this->uid})捐助Shadowsky{$this->total}元。总捐助额：{$user->money}。";
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
     */
    public function doPay()
    {
        $user    = User::find($this->uid);
        $product = Shop::find($this->product_id);
        // 添加购买记录
        $purchase_log_arr = array(
            'uid'            => $this->uid,
            'product_id'     => $this->product_id,
            'body'           => $product->name,
            'price'          => $product->price,
            'out_trade_no'   => $this->addnum,
            'fee'            => $this->feeRate * $product->price,
            'payment_method' => $this->payment_method,
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
            if ($user->product && $user->product->isByTime()) {
                $user->updateExpireDate($product->id);
            } else {
                $user->addTraffic($transfer_to_add);
                $user->updateExpireDate($product->id);
            }
        } elseif ($product->isByMete()) {
            // 流量套餐
            if ($user->isExpire()) {
                $user->addTraffic($transfer_to_add);
            } else {
                if ($user->product && $user->product->name == '试玩') {
                    $user->addTraffic($transfer_to_add);
                } else {
                    $user->updateEnableTransfer($pre['used_traffic_in_GB'] + $transfer_to_add);
                }
            }
            $user->resetExpireDate();
        } elseif ($product->type == 'C') {
            // 流量加油包
            $user->addTraffic($transfer_to_add);
        } else {
            // 试用套餐
            $user->addTraffic($transfer_to_add);
            $user->updateExpireDate($product->id);
        }

        // 更新用户信息
        $user->plan       = 'B';
        $user->product_id = $product->id;
        $user->type       = $product->name;
        $user->buy_date   = Tools::toDateTime(time());
        $user->user_type  = $product->price;

        /**
         * 如果购买加油包套餐则不改变原套餐
         */
        if ($product->type == 'C') {
            $user->product_id = $pre['product_id'];
            $user->type       = $pre['type'];
        }
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
                'user'    => $user,
                'pre'     => $pre,
                'product' => $product,
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
        $user       = Auth::getUser();
        $q          = $this->getRequestBodyArray($request);
        $product_id = intval($q['product_id']);
        $uid        = $user->id;
        $total      = $q['total'];
        if (empty($total)) {
            return 'Empty price';
        }
        if (!is_numeric($total)) {
            return 'Price is not a number';
        }
        if ($total <= '0') {
            return 'Invalid price value';
        }
        if ($product_id < 0) {
            return 'Invalid input';
        }
        if ($product_id > 0) {
            $product = Shop::find($product_id);
            if ($product) {
                if ($total != $product->price) {
                    return 'Price do not match';
                }
            } else {
                return 'Could\'t find this product';
            }

            if ($product->isByTime() && $user->product_id && $user->product->isByTime() && $user->product->transfer != $product->transfer && (strtotime($user->expire_date) - time()) > 86400 * 30) {
                return '更换套餐需要在过期前一个月内进行。';
            }

            /**
             * 加油包
             */
            if ($product->type == 'C' && (($user->product_id) ? (!$user->product->isByTime()) : true)) {
                return '当前套餐无法购买加油包';
            }
        }
        $product_id = sprintf('%02d', $product_id);
        $apiid      = $this->apiid;
        $apikey     = md5($this->key);
        $showurl    = Config::get('baseUrl') . "/dopay";
        // $showurl    = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/dopay";
        $addnum = 'pay' . $apiid . $product_id . $user->port . time();
        return "
        <form name='form1' action='https://api.jsjapp.com/pay/syt.php' method='POST'>
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
