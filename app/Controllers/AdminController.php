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
use App\Models\Node;
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

        $Y             = date('Y');
        $m             = date('m');
        $yearlyIncome  = DonateLog::where('datetime', '>', $Y)->sum('money');
        $monthlyIncome = DonateLog::where('datetime', '>', $Y . '-' . $m)->sum('money');

        /**
         * 使用支付接口的手续费
         * @var float
         */
        $yearlyFee  = DonateLog::where('datetime', '>', $Y)->sum('fee');
        $monthlyFee = DonateLog::where('datetime', '>', $Y . '-' . $m)->sum('fee');

        $income['yearly']     = $yearlyIncome;
        $income['monthly']    = $monthlyIncome;
        $income['yearlyFee']  = $yearlyFee;
        $income['monthlyFee'] = $monthlyFee;
        $income['all']        = DonateLog::sum('money');
        return $this->view()->assign('logs', $logs)->assign('income', $income)->display('admin/donatelog.tpl');
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
        $users_traffic              = [];
        $eachHour_traffic           = [];
        $nodes_traffic              = [];
        $users_traffic_for_chart    = ['labels' => array(), 'datas' => array(), 'total' => 0];
        $nodes_traffic_for_chart    = ['labels' => array(), 'datas' => array(), 'total' => 0];
        $eachHour_traffic_for_chart = ['labels' => array(), 'datas' => array(), 'total' => 0];

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
        if (isset($q['user_id']) && $q['user_id'] != '') {
            $user_id = $q['user_id'];
        } else {
            $user_id      = '';
            $q['user_id'] = '';
        }
        if (isset($q['node_id']) && $q['node_id'] != '') {
            $node_id = $q['node_id'];
        } else {
            $node_id      = '';
            $q['node_id'] = '';
        }
        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);

        /**
         * 用户本日流量使用(某个节点或所有节点)降序排名 chart 1
         */
        if ($node_id) {
            $logs_for_users_traffic_ranking_chart = TrafficLog::where('node_id', $node_id)->get();
        } else {
            $logs_for_users_traffic_ranking_chart = TrafficLog::all();
        }
        $aDayAgo = time() - 86400;
        // $aDayAgo = time() - 86400*50;
        $users   = User::where('enable', '1')->where('t', '>', $aDayAgo)->select(['id'])->get();
        foreach ($users as $user) {
            $users_traffic[$user->id] = 0;
        }
        foreach ($logs_for_users_traffic_ranking_chart as $log) {
            $users_traffic[$log->user_id] += ($log->d + $log->u);
        }
        arsort($users_traffic);
        $most_users_traffic = array_slice($users_traffic, 0, 15, true);
        reset($most_users_traffic);
        $most_traffic_user_id = key($most_users_traffic);
        foreach ($most_users_traffic as $k => $v) {
            array_push($users_traffic_for_chart['labels'], User::find($k)->user_name." (id: $k)");
            array_push($users_traffic_for_chart['datas'], round(Tools::flowToGB($v), 2));
        }
        $users_traffic_for_chart['total'] = round(Tools::flowToGB(array_sum($users_traffic)), 2);
        $users_traffic_for_chart          = json_encode($users_traffic_for_chart);

        /**
         * 各节点流量、各小时（某个用户或所有用户）使用情况 chart 2, 3
         */
        if ($user_id) {
            $logs_for_nodes_traffic_chart    = TrafficLog::where('user_id', $user_id)->get();
            $logs_for_eachHour_traffic_chart = $logs_for_nodes_traffic_chart;
        } else {
            $logs_for_nodes_traffic_chart = TrafficLog::all();
            if ($node_id) {
                $logs_for_eachHour_traffic_chart = TrafficLog::where('node_id', $node_id)->get();
            } else {
                $logs_for_eachHour_traffic_chart = $logs_for_nodes_traffic_chart;
            }
        }

        // for ($i = 0; $i <= 24; $i++) {
        for ($i = 0; $i <= (int) date('H'); $i++) {
            $eachHour_traffic[date('H a', strtotime("$i:00:00"))] = 0;
        }
        $nodes = Node::select(['id'])->get();
        foreach ($nodes as $node) {
            $nodes_traffic[$node->id] = 0;
        }

        foreach ($logs_for_nodes_traffic_chart as $log) {
            $nodes_traffic[$log->node_id] += ($log->d + $log->u);
        }
        foreach ($logs_for_eachHour_traffic_chart as $log) {
            $eachHour_traffic[date('H a', $log->log_time)] += ($log->d + $log->u);
        }
        /**
         * 去掉值为0的元素
         * @var array
         */
        // $nodes_traffic = array_filter($nodes_traffic);
        $eachHour_traffic = array_filter($eachHour_traffic);
        foreach ($nodes_traffic as $k => $v) {
            array_push($nodes_traffic_for_chart['labels'], Node::find($k)->name." (id: $k)");
            array_push($nodes_traffic_for_chart['datas'], round(Tools::flowToGB($v), 2));
        }
        foreach ($eachHour_traffic as $k => $v) {
            array_push($eachHour_traffic_for_chart['labels'], $k);
            array_push($eachHour_traffic_for_chart['datas'], round(Tools::flowToGB($v), 2));
        }
        $nodes_traffic_for_chart['total']    = round(array_sum($nodes_traffic_for_chart['datas']), 2);
        $eachHour_traffic_for_chart['total'] = round(array_sum($eachHour_traffic_for_chart['datas']), 2);
        $nodes_traffic_for_chart             = json_encode($nodes_traffic_for_chart);
        $eachHour_traffic_for_chart          = json_encode($eachHour_traffic_for_chart);

        return $this->view()->assign('q', $q)->assign('logs', $logs)->assign('users_traffic_for_chart', $users_traffic_for_chart)->assign('nodes_traffic_for_chart', $nodes_traffic_for_chart)->assign('eachHour_traffic_for_chart', $eachHour_traffic_for_chart)->display('admin/trafficlog.tpl');
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

    public function email($request, $response, $args)
    {
        return $this->view()->display('admin/email.tpl');
    }

    public function sendEmail($request, $response, $args)
    {
        $q = $request->getParsedBody();
        if ($q['id'] != '') {
            $user = User::find($q['id']);
        }
        if ($q['port'] != '') {
            $user = User::where('port', $q['port'])->first();
        }
        if ($q['email'] != '') {
            $user = User::where('email', $q['email'])->first();
        }
        if (!$user) {
            $res['ret'] = 0;
            $res['msg'] = '未找到该用户。';
            return $response->getBody()->write(json_encode($res));
        }
        if ($q['title'] == '') {
            $res['ret'] = 0;
            $res['msg'] = '请输入标题。';
            return $response->getBody()->write(json_encode($res));
        }
        if ($q['content'] == '') {
            $res['ret'] = 0;
            $res['msg'] = '请输入内容。';
            return $response->getBody()->write(json_encode($res));
        }
        try {
            Mail::send($user->email, $q['title'], 'news/announcement.tpl', ['content' => $q['content'], 'user_name' => $user->user_name], []);
            $res['ret'] = 1;
            $res['msg'] = "发送邮箱公告成功";
        } catch (\Exception $e) {
            $res['ret'] = 0;
            $res['msg'] = $e->getMessage();
        }
        return $response->getBody()->write(json_encode($res));
    }

    public function sendEmails($request, $response, $args)
    {
        $q      = $request->getParsedBody();
        $users  = User::where('id', '>', 0);
        $search = ['plan', 'enable', 'status', 'type'];
        foreach ($q as $k => $v) {
            if (in_array($k, $search) && $v != '') {
                $users = $users->where($k, $v);
            }
        }
        $users = $users->get();
        if (!$users) {
            $res['ret'] = 0;
            $res['msg'] = '未找到任何用户。';
            return $response->getBody()->write(json_encode($res));
        }
        if ($q['title'] == '') {
            $res['ret'] = 0;
            $res['msg'] = '请输入标题。';
            return $response->getBody()->write(json_encode($res));
        }
        if ($q['content'] == '') {
            $res['ret'] = 0;
            $res['msg'] = '请输入内容。';
            return $response->getBody()->write(json_encode($res));
        }
        $res['ret']   = 1;
        $res['msg']   = "发送邮箱公告成功";
        $res['count'] = count($users);
        $res['users'] = array();
        foreach ($users as $user) {
            $res['users'][$user->id]              = array();
            $res['users'][$user->id]['email']     = $user->email;
            $res['users'][$user->id]['user_name'] = $user->user_name;
            // $res['users'][$user->id]['time'] = Tools::toDateTime($user->t);
            try {
                Mail::send($user->email, $q['title'], 'news/announcement.tpl', ['content' => $q['content'], 'user_name' => $user->user_name], []);
            } catch (\Exception $e) {
                $res['ret'] = 0;
                $res['msg'] = $e->getMessage();
            }
        }
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

    public function sendAnnounEmail($request, $response, $args)
    {
        $q     = $request->getParsedBody();
        $users = User::all();
        $ann   = Ann::orderBy("id", "desc")->get()->first();

        /**
         * 传递到邮件模板中的数据
         * @var array
         */
        $arr = [
            "content"   => $ann->content,
            "user_name" => "",
        ];

        /**
         * 构造回应数据
         */
        $res['ret']   = 1;
        $res['msg']   = "发送邮箱公告成功";
        $res['users'] = array();
        $res['total'] = count($users);

        if ($users && $ann->title && $ann->content) {
            foreach ($users as $user) {
                $res['users'][$user->id]          = array();
                $res['users'][$user->id]['email'] = $user->email;
                $res['users'][$user->id]['name']  = $user->user_name;

                /**
                 * 发送邮件
                 */
                $arr["user_name"] = $user->user_name;
                try {
                    Mail::send($user->email, $ann->title, 'news/announcement.tpl', $arr, []);
                } catch (\Exception $e) {
                    $res['ret'] = 0;
                    $res['msg'] = $e->getMessage();
                }
            }
        } else {
            $res['ret'] = 0;
            $res['msg'] = "无符合条件的用户或无公告";
        }
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
