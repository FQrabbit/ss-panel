<?php

namespace App\Controllers;

use App\Controllers\PaymentController;
use App\Controllers\UserController;
use App\Models\Ann;
use App\Models\AnnLog;
use App\Models\CheckInLog;
use App\Models\DonateLog;
use App\Models\InviteCode;
use App\Models\Music;
use App\Models\PurchaseLog;
use App\Models\Shop;
use App\Models\TrafficLog;
use App\Models\User;
use App\Models\Vote;
use App\Services\Analytics;
use App\Services\DbConfig;
use App\Services\Mail;
use App\Utils\Tools;

/**
 *  Admin Controller
 */
class AdminController extends UserController
{

    public function index($request, $response, $args)
    {
        $sts = new Analytics();
        return $this->view()->assign('sts', $sts)->display('admin/index.tpl');
    }

    public function invite($request, $response, $args)
    {
        $codes = InviteCode::where('user_id', '=', '0')->get();
        return $this->view()->assign('codes', $codes)->display('admin/invite.tpl');
    }

    public function addInvite($request, $response, $args)
    {
        $n      = $request->getParam('num');
        $prefix = $request->getParam('prefix');
        $uid    = $request->getParam('uid');
        if ($n < 1) {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }
        for ($i = 0; $i < $n; $i++) {
            $char          = Tools::genRandomChar(32);
            $code          = new InviteCode();
            $code->code    = $prefix . $char;
            $code->user_id = $uid;
            $code->save();
        }
        $res['ret'] = 1;
        $res['msg'] = "邀请码添加成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function checkInLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = CheckInLog::where('id', ">", 0);
        $path = '/admin/checkinlog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k . '=' . $v . '&';
            }
        }
        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/checkinlog.tpl');
    }

    public function purchaseLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($q['page'])) {
            $q['page'] = 1;
        }
        if (!isset($q['uid'])) {
            $q['uid'] = '';
        }
        if (!isset($q['port'])) {
            $q['port'] = '';
        }
        if (!isset($q['out_trade_no'])) {
            $q['out_trade_no'] = '';
        }
        if ($q['port'] != '') {
            $user      = User::where('port', $q['port'])->first();
            $q['uid']  = $user->id;
            $q['port'] = '';
        }
        $logs = PurchaseLog::where('id', '>', 0);
        $path = '/admin/purchaselog?';
        foreach ($q as $k => $v) {
            if ($v != '' && $k != 'page') {
                if ($k == 'out_trade_no') {
                    $logs = $logs->where('out_trade_no', 'like', "%$v%");
                } else {
                    $logs = $logs->where($k, $v);
                }
                $path .= $k . '=' . $v . '&';
            }
        }

        $Y             = date('Y');
        $m             = date('m');
        $d             = date('d');
        $yearlyIncome  = PurchaseLog::where('buy_date', '>', $Y)->sum('price');
        $monthlyIncome = PurchaseLog::where('buy_date', '>', $Y . '-' . $m)->sum('price');
        $dailyIncome   = PurchaseLog::where('buy_date', '>', $Y . '-' . $m . '-' . $d)->sum('price');

        /**
         * 使用支付接口的手续费
         * @var float
         */
        $yearlyFee  = PurchaseLog::where('buy_date', '>', $Y)->sum('fee');
        $monthlyFee = PurchaseLog::where('buy_date', '>', $Y . '-' . $m)->sum('fee');
        $dailyFee   = PurchaseLog::where('buy_date', '>', $Y . '-' . $m . '-' . $d)->sum('fee');

        $income['yearly']     = $yearlyIncome;
        $income['monthly']    = $monthlyIncome;
        $income['daily']      = $dailyIncome;
        $income['yearlyFee']  = $yearlyFee;
        $income['monthlyFee'] = $monthlyFee;
        $income['dailyFee']   = $dailyFee;
        $income['all']        = PurchaseLog::sum('price');

        /**
         * x轴坐标标签数组,从一月到本月。
         * @var array
         */
        $monthscope = array();
        /**
         * 各月数值数组。
         * @var array
         */
        $monthdata = array();

        for ($i = 1; $i <= $m; $i++) {
            if ($i < 10) {
                $tm = '0' . $i;
            } else {
                $tm = $i;
            }
            $j = $i + 1;
            if ($j < 10) {
                $nm = '0' . $j;
            } else {
                $nm = $j;
            }
            $monthIncome         = PurchaseLog::where('buy_date', '>', $Y . '-' . $tm)->where('buy_date', '<', $Y . '-' . $nm)->sum('price');
            $income['month'][$i] = $monthIncome;
            array_push($monthscope, $i . '月');
            array_push($monthdata, $monthIncome);
        }
        $datasets = array(
            'monthscope' => $monthscope,
            'monthdata'  => $monthdata,
        );
        $datasets = json_encode($datasets);
        $path     = substr($path, 0, strlen($path) - 1);
        $logs     = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        $products = Shop::where('status', 1)->get();
        return $this->view()->assign('logs', $logs)->assign('q', $q)->assign('income', $income)->assign('datasets', $datasets)->assign('products', $products)->display('admin/purchaselog.tpl');
    }

    public function addPurchase($request, $response, $args)
    {
        $q         = $request->getParsedBody();
        $rs['ret'] = 0;
        if ($q['uid'] == '' && $q['port'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '请输入用户id或port。';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($q['body'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '请选择套餐。';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($q['port'] != '') {
            $user = User::where('port', $q['port'])->first();
            if (!$user) {
                $rs['ret'] = 0;
                $rs['msg'] = '未找到该用户。';
                return $response->getBody()->write(json_encode($rs));
            }
            $q['uid'] = $user->id;
        }
        if ($q['uid'] != '') {
            $user = User::where('id', $q['uid'])->first();
            if (!$user) {
                $rs['ret'] = 0;
                $rs['msg'] = '未找到该用户。';
                return $response->getBody()->write(json_encode($rs));
            }
        }
        if (empty($q['buy_date'])) {
            $q['buy_date'] = Tools::toDateTime(time());
        }

        $product_id = $q['body'];
        $pay        = new PaymentController();
        $rs         = $pay->doPay($q['uid'], $product_id, time() . $q['uid'], 0);
        return $response->getBody()->write(json_encode($rs));
    }

    public function deletePurchaseLog($request, $response, $args)
    {
        $id     = $args["id"];
        $record = PurchaseLog::find($id);
        if (!$record) {
            $rs['ret'] = 0;
            $rs['msg'] = "没找到此条记录";
            return $response->getBody()->write(json_encode($rs));
        }
        if (!$record->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function donateLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = DonateLog::where('id', ">", 0);
        $path = '/admin/donatelog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k . '=' . $v . '&';
            }
        }
        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/donatelog.tpl');
    }

    public function addDonate($request, $response, $args)
    {
        $q         = $request->getParsedBody();
        $rs['ret'] = 0;
        if ($q['uid'] == '' && $q['port'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '请输入用户id或port。';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($q['money'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '请输入金额。';
            return $response->getBody()->write(json_encode($rs));
        }
        if ($q['port'] != '') {
            $user = User::where('port', $q['port'])->first();
            if (!$user) {
                $rs['ret'] = 0;
                $rs['msg'] = '未找到该用户。';
                return $response->getBody()->write(json_encode($rs));
            }
            $q['uid'] = $user->id;
        }
        if ($q['uid'] != '') {
            $user = User::where('id', $q['uid'])->first();
            if (!$user) {
                $rs['ret'] = 0;
                $rs['msg'] = '未找到该用户。';
                return $response->getBody()->write(json_encode($rs));
            }
        }

        $pay = new PaymentController();
        $rs  = $pay->doDonate($q['uid'], $q['money'], time() . $q['uid'], 0);
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteDonateLog($request, $response, $args)
    {
        $id     = $args["id"];
        $record = DonateLog::find($id);
        if (!$record->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function music($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $music = Music::where('id', ">", 0);
        $path  = '/admin/music?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $music = $music->where($k, 'like', '%' . $v . '%');
                $path .= $k . '=' . $v . '&';
            }
        }
        $path  = substr($path, 0, strlen($path) - 1);
        $music = $music->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $music->setPath($path);
        return $this->view()->assign('music', $music)->display('admin/music.tpl');
    }

    public function addMusic($request, $response, $args)
    {
        $q             = $request->getParsedBody();
        $rs['ret']     = 1;
        $rs['msg']     = '添加成功';
        $music         = new Music();
        $music->mid    = $q['mid'];
        $music->name   = $q['name'];
        $music->author = $q['author'];
        $music->save();
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteMusic($request, $response, $args)
    {
        $mid = $args['mid'];
        $m   = Music::where('mid', $mid)->first();
        $m->delete();
        $rs['ret'] = 1;
        $rs['msg'] = '删除成功';
        return $response->getBody()->write(json_encode($rs));
    }

    public function trafficLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = TrafficLog::where('id', ">", 0)->orderBy('id', 'desc');
        $path = '/admin/trafficlog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k . '=' . $v . '&';
            }
        }

        foreach (TrafficLog::select('user_id', 'd', 'u')->get() as $log) {
            if (isset($users_transfer_array[$log->user_id])) {
                $users_transfer_array[$log->user_id] += ($log->d + $log->u);
            } else {
                $users_transfer_array[$log->user_id] = ($log->d + $log->u);
            }
        }
        arsort($users_transfer_array);
        $users_transfer_array = array_slice($users_transfer_array, 0, 15, true);
        reset($users_transfer_array);
        $first_user_id = key($users_transfer_array);
        if (!isset($q['user_id'])) {
            $q['user_id'] = $first_user_id;
        }
        if (!isset($q['node_id'])) {
            $q['node_id'] = '';
        }
        $labels = array();
        $datas  = array();
        foreach ($users_transfer_array as $k => $v) {
            array_push($labels, $k);
            array_push($datas, round(Tools::flowToGB($v), 2));
        }
        $users_transfer_array_for_chart = json_encode(array("labels" => $labels, "datas" => $datas));

        $array_for_chart = UserController::getTrafficInfoArrayForChart($q['user_id']);
        $array_for_chart = json_encode($array_for_chart);
        // return json_encode($array_for_chart);

        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('q', $q)->assign('logs', $logs)->assign('array_for_chart', $array_for_chart)->assign('users_transfer_array_for_chart', $users_transfer_array_for_chart)->display('admin/trafficlog.tpl');
    }

    public function config($request, $response, $args)
    {
        $conf = [
            "app-name"       => DbConfig::get('app-name'),
            "home-code"      => DbConfig::get('home-code'),
            "home-purchase"  => DbConfig::get('home-purchase'),
            "analytics-code" => DbConfig::get('analytics-code'),
            "user-index"     => DbConfig::get('user-index'),
            "user-node"      => DbConfig::get('user-node'),
            "user-purchase"  => DbConfig::get('user-purchase'),
        ];
        $ann = Ann::orderBy("id", "desc")->get()->first();
        return $this->view()->assign('conf', $conf)->assign('ann', $ann)->display('admin/config.tpl');
    }

    public function updateConfig($request, $response, $args)
    {
        $config = [
            "analytics-code" => $request->getParam('analyticsCode'),
            "home-code"      => $request->getParam('homeCode'),
            "home-purchase"  => $request->getParam('homePurchase'),
            "app-name"       => $request->getParam('appName'),
            "user-index"     => $request->getParam('userIndex'),
            "user-node"      => $request->getParam('userNode'),
            "user-purchase"  => $request->getParam('userPurchase'),
        ];
        foreach ($config as $key => $value) {
            DbConfig::set($key, $value);
        }
        $res['ret'] = 1;
        $res['msg'] = "更新成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function updateAnn($request, $response, $args)
    {
        $ann_title    = $request->getParam('ann_title');
        $ann_content  = $request->getParam('ann_content');
        $ann          = Ann::orderBy("id", "desc")->get()->first();
        $ann->title   = $ann_title;
        $ann->content = $ann_content;
        $ann->save();
        $res['ret'] = 1;
        $res['msg'] = "更新邮箱公告成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function createAnn($request, $response, $args)
    {
        $ann_title    = $request->getParam('ann_title');
        $ann_content  = $request->getParam('ann_content');
        $ann          = new Ann();
        $ann->title   = $ann_title;
        $ann->content = $ann_content;
        $ann->save();
        $res['ret'] = 1;
        $res['msg'] = "公告添加成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function sendMailPost($request, $response, $args)
    {
        $q     = $request->getParsedBody();
        $users = User::where('id', '>', 0);
        foreach ($q as $k => $v) {
            if ($v != "") {
                $users = $users->where($k, $v);
            }
        }
        $users = $users->get();

        $ann = Ann::orderBy("id", "desc")->get()->first();
        $arr = [
            "title"     => $ann->title,
            "content"   => $ann->content,
            "user_name" => "",
        ];

        $res['names'] = [];
        $i            = 0;
        if ($users && $ann->title && $ann->content) {
            foreach ($users as $user) {
                $arr["user_name"] = $user->user_name;
                try {
                    Mail::send($user->email, $ann->title, 'news/announcement.tpl', $arr, []);
                    $res['ret'] = 1;
                    $res['msg'] = "发送邮箱公告成功";
                } catch (\Exception $e) {
                    $res['ret'] = 0;
                    $res['msg'] = $e->getMessage();
                }
            }
        } else {
            $res['ret'] = 0;
            $res['msg'] = "无符合条件的用户或无公告";
        }
        $res['total'] = count($users);
        return $response->getBody()->write(json_encode($res));
    }

    public function deleteUserPolls($uid)
    {
        Vote::where('uid', $uid)->delete();
    }

    public function deleteUserAnnLogs($uid)
    {
        AnnLog::where('user_id', $uid)->delete();
    }

    public function clearUserLogs($uid)
    {
        self::deleteUserPolls($uid);
        self::deleteUserAnnLogs($uid);
    }
}
