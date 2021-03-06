<?php

namespace App\Controllers;

use App\Controllers\PaymentController;
use App\Controllers\UserController;
use App\Models\Ann;
use App\Models\AnnLog;
use App\Models\CheckInLog;
use App\Models\DonateLog;
use App\Models\ExpenditureLog;
use App\Models\InviteCode;
use App\Models\Music;
use App\Models\Node;
use App\Models\PurchaseLog;
use App\Models\Shop;
use App\Models\TrafficLog;
use App\Models\User;
use App\Models\UserDailyTrafficLog;
use App\Models\Vote;
use App\Models\VpsMerchant;
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

        $yearlyIncome  = PurchaseLog::where('buy_date', '>', date('Y'))->sum('price');
        $monthlyIncome = PurchaseLog::where('buy_date', '>', date('Y-m'))->sum('price');
        $dailyIncome   = PurchaseLog::where('buy_date', '>', date('Y-m-d'))->sum('price');

        $income['all']     = PurchaseLog::sum('price');
        $income['yearly']  = $yearlyIncome;
        $income['monthly'] = $monthlyIncome;
        $income['daily']   = $dailyIncome;

        /**
         * 使用支付接口的手续费
         */
        $yearlyFee  = PurchaseLog::where('buy_date', '>', date('Y'))->sum('fee');
        $monthlyFee = PurchaseLog::where('buy_date', '>', date('Y-m'))->sum('fee');
        $dailyFee   = PurchaseLog::where('buy_date', '>', date('Y-m-d'))->sum('fee');

        /**
         * vps成本
         */
        $yearlyVpsCost  = ExpenditureLog::where('date', '>', date('Y'))->sum('price');
        $monthlyVpsCost = ExpenditureLog::where('date', '>', date('Y-m'))->sum('price');
        $dailyVpsCost   = ExpenditureLog::where('date', '>', date('Y-m-d'))->sum('price');

        $cost['fee']['yearly']    = $yearlyFee;
        $cost['fee']['monthly']   = $monthlyFee;
        $cost['fee']['daily']     = $dailyFee;
        $cost['vps']['yearly']    = $yearlyVpsCost;
        $cost['vps']['monthly']   = $monthlyVpsCost;
        $cost['vps']['daily']     = $dailyVpsCost;
        $cost['total']['yearly']  = $cost['fee']['yearly'] + $cost['vps']['yearly'];
        $cost['total']['monthly'] = $cost['fee']['monthly'] + $cost['vps']['monthly'];
        $cost['total']['daily']   = $cost['fee']['daily'] + $cost['vps']['daily'];

        $half_year_ago_date = date('Y-m-d', strtotime(date('Y-m') . '-01 -12 months'));
        $someMonth          = $half_year_ago_date;
        while ($someMonth <= date('Y-m-d')) {
            $nextMonth                            = date('Y-m-d', strtotime($someMonth . ' +1 month'));
            $monthIncome                          = PurchaseLog::whereBetween('buy_date', [$someMonth, $nextMonth])->sum('price');
            $monthly_income_for_chart['labels'][] = date('Y-m', strtotime($someMonth));
            $monthly_income_for_chart['datas'][]  = $monthIncome;
            $someMonth                            = $nextMonth;
        }
        $monthly_income_for_chart = json_encode($monthly_income_for_chart);

        /**
         * 一周收入 chart
         */
        $someDay               = date('Y-m-d', strtotime("-6 days"));
        $last_week_income_logs = PurchaseLog::where('buy_date', '>', $someDay)->get();
        while ($someDay <= date('Y-m-d')) {
            $weekly_income[date('m-d', strtotime($someDay))] = 0;
            $someDay                                         = date('Y-m-d', strtotime($someDay . ' +1 day'));
        }
        foreach ($last_week_income_logs as $log) {
            $weekly_income[date('m-d', strtotime($log->buy_date))] += $log->price;
        }
        foreach ($weekly_income as $k => $v) {
            $weekly_income_for_chart['labels'][] = $k;
            $weekly_income_for_chart['datas'][]  = $v;
        }
        $weekly_income_for_chart['total'] = array_sum($weekly_income_for_chart['datas']);
        $weekly_income_for_chart          = json_encode($weekly_income_for_chart);

        /**
         * 当日收入 chart
         * @var [type]
         */
        $daily_income_logs = PurchaseLog::where('buy_date', '>', date('Y-m-d'))->get();
        for ($i = 0; $i <= (int) date('H'); $i++) {
            $eachHour_income[date('H a', strtotime("$i:00"))] = 0;
        }
        foreach ($daily_income_logs as $log) {
            $eachHour_income[date('H a', strtotime($log->buy_date))] += $log->price;
        }
        foreach ($eachHour_income as $k => $v) {
            $eachHour_income_for_chart['labels'][] = $k;
            $eachHour_income_for_chart['datas'][]  = $v;
        }
        $eachHour_income_for_chart['total'] = array_sum($eachHour_income_for_chart['datas']);
        $eachHour_income_for_chart          = json_encode($eachHour_income_for_chart);

        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        $products = Shop::where('status', 1)->get();
        return $this->view()->
            assign('logs', $logs)->
            assign('q', $q)->
            assign('income', $income)->
            assign('cost', $cost)->
            assign('products', $products)->
            assign('eachHour_income_for_chart', $eachHour_income_for_chart)->
            assign('weekly_income_for_chart', $weekly_income_for_chart)->
            assign('monthly_income_for_chart', $monthly_income_for_chart)->
            display('admin/purchaselog/index.tpl');
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
        if ($q['product_id'] == '') {
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
        $product = Shop::find($q['product_id']);

        $pay             = new PaymentController();
        $pay->uid        = $user->id;
        $pay->product_id = $q['product_id'];
        $pay->total      = $product->price;
        $pay->addnum     = 'p' . $user->port . 't' . time();
        $rs              = $pay->doPay();
        return $response->getBody()->write(json_encode($rs));
    }

    public function editPurchaseLog($request, $response, $args)
    {
        $log             = PurchaseLog::find($args['id']);
        $products        = Shop::all();
        $payment_methods = ['QQ', '微信', '支付宝'];
        return $this->view()->assign('log', $log)->assign('products', $products)->assign('payment_methods', $payment_methods)->display('admin/purchaselog/edit.tpl');
    }

    public function updatePurchaseLog($request, $response, $args)
    {
        $log_id              = $args['id'];
        $q                   = $request->getParams();
        $log                 = PurchaseLog::find($log_id);

        $log->uid            = $q['uid'];
        $log->product_id     = $q['product_id'];
        $log->out_trade_no   = $q['out_trade_no'];
        $log->payment_method = $q['payment_method'];
        $log->fee            = $q['fee'];

        try {
            $log->save();
            $rs['ret'] = 1;
            $rs['msg'] = '更新购买日志成功！';
        } catch (\Exception $e) {
            $rs['ret'] = 0;
            $rs['msg'] = $e->getMessage();
        }
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

        $pay             = new PaymentController();
        $pay->uid        = $user->id;
        $pay->product_id = 0;
        $pay->total      = $q['money'];
        $pay->addnum     = 'p' . $user->port . 't' . time();
        $rs              = $pay->doDonate();
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

    public function expenditureLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($q['page'])) {
            $q['page'] = 1;
        }
        $logs = ExpenditureLog::where('id', '>', 0);
        $path = '/admin/expenditurelog?';
        foreach ($q as $k => $v) {
            if ($v != '' && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k . '=' . $v . '&';
            }
        }
        $path = substr($path, 0, strlen($path) - 1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);

        $yearlyExpenditure  = ExpenditureLog::where('date', '>', date('Y'))->sum('price');
        $monthlyExpenditure = ExpenditureLog::where('date', '>', date('Y-m'))->sum('price');

        $expenditure['yearly']  = $yearlyExpenditure;
        $expenditure['monthly'] = $monthlyExpenditure;
        $expenditure['all']     = ExpenditureLog::sum('price');

        $vpsMerchants = VpsMerchant::rightJoin('ss_node', 'vps_merchant.id', '=', 'ss_node.vps')->select('vps_merchant.id', 'vps_merchant.name')->groupBy('name')->orderBy('name')->get();
        $nodes        = Node::orderBy('sort')->get();

        return $this->view()->
            assign('logs', $logs)->
            assign('vpsMerchants', $vpsMerchants)->
            assign('nodes', $nodes)->
            assign('expenditure', $expenditure)->
            display('admin/expenditurelog.tpl');
    }

    public function addVpsMerchant($request, $response, $args)
    {
        $q = $request->getParsedBody();
        if ($q['name'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '输入不完整';
            return $response->getBody()->write(json_encode($rs));
        }
        $log          = new VpsMerchant();
        $log->name    = $q['name'];
        $log->api     = $q['api'];
        $log->website = $q['website'];
        if ($log->save()) {
            $rs['ret'] = 1;
            $rs['msg'] = 'vps供应商添加成功！';
        } else {
            $rs['ret'] = 0;
            $rs['msg'] = '操作失败！';
        }
        return $response->getBody()->write(json_encode($rs));
    }

    public function addExpenditure($request, $response, $args)
    {
        $q = $request->getParsedBody();
        if ($q['vps_merchant_id'] == '' || $q['node_id'] == '' || $q['price'] == '') {
            $rs['ret'] = 0;
            $rs['msg'] = '输入不完整';
            return $response->getBody()->write(json_encode($rs));
        }
        $log                  = new ExpenditureLog();
        $log->vps_merchant_id = $q['vps_merchant_id'];
        $log->node_id         = $q['node_id'];
        $log->price           = $q['price'];
        $log->date            = date('Y-m-d H:i:s');
        if ($log->save()) {
            $rs['ret'] = 1;
            $rs['msg'] = '支出记录添加成功！';
        } else {
            $rs['ret'] = 0;
            $rs['msg'] = '操作失败！';
        }
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteExpenditureLog($request, $response, $args)
    {
        $id     = $args["id"];
        $record = ExpenditureLog::find($id);
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

    public static function mergeUsersTrafficLogs($logs)
    {
        $users_traffic = [];
        foreach ($logs as $log) {
            if (isset($users_traffic[$log->user_id])) {
                $users_traffic[$log->user_id] += ($log->d + $log->u);
            } else {
                $users_traffic[$log->user_id] = 0;
            }
        }
        return $users_traffic;
    }

    public function trafficLog($request, $response, $args)
    {
        $users_traffic                     = [];
        $eachHour_traffic                  = [];
        $nodes_traffic                     = [];
        $users_traffic_for_chart           = ['labels' => array(), 'datas' => array(), 'total' => 0];
        $nodes_traffic_for_chart           = ['labels' => array(), 'datas' => array(), 'total' => 0];
        $eachHour_traffic_for_chart        = ['labels' => array(), 'datas' => array(), 'total' => 0];
        $users_traffic_thisMonth_for_chart = ['labels' => array(), 'datas' => array()];
        $nodes                             = Node::select(['id', 'name'])->orderBy('sort')->get();

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
        $logs_a = TrafficLog::groupBy('user_id')->selectRaw('user_id, node_id, sum(u+d) as traffic')->orderBy('traffic', 'desc')->take(10);
        if ($node_id) {
            $logs_a = $logs_a->where('node_id', $node_id)->get();
        } else {
            $logs_a = $logs_a->get();
        }
        foreach ($logs_a as $log) {
            $users_traffic_for_chart['total'] += $log->traffic;
            array_push($users_traffic_for_chart['labels'], $log->user_id);
            array_push($users_traffic_for_chart['datas'], round(Tools::flowToGB($log->traffic), 2));
        }
        // return json_encode($logs_a);
        $users_traffic_for_chart['total'] = round(Tools::flowToGB($users_traffic_for_chart['total']), 2);
        $users_traffic_for_chart          = json_encode($users_traffic_for_chart);

        /**
         * 各节点流量、各小时（某个用户或所有用户）使用情况 chart 2, 3
         */
        if ($user_id) {
            $logs_for_nodes_traffic_chart = TrafficLog::where('user_id', $user_id)->groupBy('node_id')->selectRaw('node_id, sum(u+d) as traffic')->orderBy('traffic', 'desc')->get();
        } else {
            $logs_for_nodes_traffic_chart = TrafficLog::groupBy('node_id')->selectRaw('node_id, sum(u+d) as traffic')->orderBy('traffic', 'desc')->get();
        }

        foreach ($logs_for_nodes_traffic_chart as $log) {
            $nodes_traffic_for_chart['labels'][] = Node::find($log->node_id)->name . ' (id: ' . $log->node_id . ')';
            $nodes_traffic_for_chart['datas'][]  = round(Tools::flowToGB($log->traffic), 2);
            $nodes_traffic_for_chart['total'] += $log->traffic;
        }

        // for ($i = 0; $i <= 23; $i++) {
        for ($i = 0; $i <= (int) date('H'); $i++) {
            $a_time = strtotime(date('Y-m-d') . " $i:00:00");
            $b_time = strtotime('+1 hour', $a_time);
            if ($user_id) {
                $log = TrafficLog::where('user_id', $user_id);
            } else {
                if ($node_id) {
                    $log = TrafficLog::where('node_id', $node_id);
                } else {
                    $log = TrafficLog::where('id', '>', 1);
                }
            }
            $log     = $log->whereBetween('log_time', [$a_time, $b_time])->selectRaw('sum(u+d) as traffic')->get()->first();
            $traffic = $log ? ($log->traffic) : 0;

            $eachHour_traffic_for_chart['labels'][] = date('H a', strtotime("$i:00:00"));
            $eachHour_traffic_for_chart['datas'][]  = round(Tools::flowToGB($traffic), 2);
        }
        $nodes_traffic_for_chart['total'] = round(Tools::flowToGB($nodes_traffic_for_chart['total']), 2);
        $eachHour_traffic_for_chart['total'] += $nodes_traffic_for_chart['total'];

        $nodes_traffic_for_chart    = json_encode($nodes_traffic_for_chart);
        $eachHour_traffic_for_chart = json_encode($eachHour_traffic_for_chart);

        /**
         * 用户本月流量使用降序排名 chart 4
         */
        $traffic_logs = UserDailyTrafficLog::where('date', '>=', date('Y-m') . '-1')->groupBy('uid')->selectRaw('uid, sum(traffic) as traffic')->orderBy('traffic', 'desc')->take(30)->get();
        foreach ($traffic_logs as $log) {
            $users_traffic_thisMonth_for_chart['labels'][] = $log->uid;
            $users_traffic_thisMonth_for_chart['datas'][]  = round(Tools::flowToGB($log->traffic), 2);
        }
        $users_traffic_thisMonth_for_chart = json_encode($users_traffic_thisMonth_for_chart);
        return $this->view()->
            assign('nodes', $nodes)->
            assign('q', $q)->
            assign('logs', $logs)->
            assign('users_traffic_for_chart', $users_traffic_for_chart)->
            assign('nodes_traffic_for_chart', $nodes_traffic_for_chart)->
            assign('eachHour_traffic_for_chart', $eachHour_traffic_for_chart)->
            assign('users_traffic_thisMonth_for_chart', $users_traffic_thisMonth_for_chart)->
            display('admin/trafficlog.tpl');
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
