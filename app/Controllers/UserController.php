<?php

namespace App\Controllers;

use App\Models\Ann;
use App\Models\AnnLog;
use App\Models\CheckInLog;
use App\Models\DelUser;
use App\Models\InviteCode;
use App\Models\Music;
use App\Models\Node;
use App\Models\PurchaseLog;
use App\Models\Shop;
use App\Models\TrafficLog;
use App\Models\User;
use App\Models\UserDailyTrafficLog;
use App\Models\Vote;
use App\Services\Auth;
use App\Services\Auth\EmailVerify;
use App\Services\Config;
use App\Services\DbConfig;
use App\Utils\Check;
use App\Utils\Hash;
use App\Utils\Tools;

/**
 *  HomeController
 */
class UserController extends BaseController
{

    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    public function view()
    {
        $userFooter = DbConfig::get('user-footer');
        $menuList   = [[
                'name' => '用户中心',
                'uri'  => '/user',
                'icon' => 'dashboard',],
                [
                'name' => '节点列表',
                'uri'  => '/user/node',
                'icon' => 'sitemap',],
                [
                'name' => '我的信息',
                'uri'  => '/user/profile',
                'icon' => 'user',],
                [
                'name' => '历史公告',
                'uri'  => '/user/announcement',
                'icon' => 'list-alt',],
                [
                'name' => '流量记录',
                'uri'  => '/user/trafficlog',
                'icon' => 'history',],
                [
                'name' => '购买记录',
                'uri'  => '/user/purchaselog',
                'icon' => 'archive',],
                [
                'name' => '系统信息',
                'uri'  => '/user/sys',
                'icon' => 'align-left',],
                [
                'name' => '问题反馈',
                'uri'  => '/user/qna',
                'icon' => 'question-circle',],
                [
                'name' => '购买',
                'uri'  => '/user/purchase',
                'icon' => 'shopping-cart',]
        ];
        $uri = strtok($_SERVER["REQUEST_URI"], '?');
        $pageTitle = $menuList[array_search($uri, array_column($menuList, 'uri'))]['name'];

        $pagesThatRequireChartjs         = ['/user', '/user/trafficlog', '/user/node', '/admin/trafficlog', '/admin/purchaselog', '/admin/node'];
        $pagesThatRequireJQueryDatatable = ['/user/sys'];
        $pagesThatRequireJQueryConfirm   = ['/admin/user', '/admin/node', '/admin/purchaselog', '/admin/donatelog', '/admin/expenditurelog'];
        $pagesThatRequireWYSI            = ['/admin/config', '/admin/email'];

        $requireChartjs         = in_array($uri, $pagesThatRequireChartjs);
        $requireJQueryDatatable = in_array($uri, $pagesThatRequireJQueryDatatable);
        $requireJQueryConfirm   = in_array($uri, $pagesThatRequireJQueryConfirm);
        $requireWYSI            = in_array($uri, $pagesThatRequireWYSI);

        // 闲聊么
        $xlm = array();
        $xlm['id'] = DbConfig::get('xlm_id');
        $xlm['sso_key'] = DbConfig::get('xlm_sso_key');
        $xlm['hash'] = hash('sha512', $xlm['id'] . '_' . $this->user->id . '_' . time() . '_' . $xlm['sso_key']);
        $xlm['mobile_url'] = "http://xianliao.me/website/" . $xlm['id'] . "?mobile=1&uid=1&username=" . urlencode($this->user->user_name) . "&avatar=" . urlencode($this->user->gravatar) . "&ts=" . time() . "&token=" . $xlm['hash'];
        return parent::view()->
            assign('xlm', $xlm)->
            assign('menuList', $menuList)->
            assign('uri', $uri)->
            assign('pageTitle', $pageTitle)->
            assign('userFooter', $userFooter)->
            assign('requireChartjs', $requireChartjs)->
            assign('requireJQueryDatatable', $requireJQueryDatatable)->
            assign('requireJQueryConfirm', $requireJQueryConfirm)->
            assign('requireWYSI', $requireWYSI);
    }

    public function index($request, $response, $args)
    {
        $msg = DbConfig::get('user-index');
        if ($msg == null) {
            $msg = "在后台修改用户中心公告...";
        }
        $new_ann = Ann::orderBy('id', 'DESC')->first();
        $title   = $this->user->port . "0";
        $music   = Music::orderByRaw("RAND()")->first();
        $music->count += 1;
        $mid = $music->mid;
        $music->save();
        return $this->view()->
            assign('msg', $msg)->
            assign('new_ann', $new_ann)->
            assign('title', $title)->
            assign('mid', $mid)->
            display('user/index.tpl');
    }

    public function announcement($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $anns = Ann::orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $anns->setPath('/user/announcement');
        return $this->view()->
            assign('anns', $anns)->
            display('user/announcement.tpl');
    }

    public function readAnn($request, $response, $args)
    {
        $ann_id = $args['id'];
        $log    = AnnLog::where('user_id', $this->user->id)->where('ann_id', $ann_id)->first();
        if (!$log) {
            $log              = new AnnLog();
            $log->ann_id      = $ann_id;
            $log->user_id     = $this->user->id;
            $log->read_status = 1;
            $log->save();
        } else {
            $log->read_status = 1;
            $log->save();
        }
        return "Read";
    }

    public function node($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }

        $msg              = DbConfig::get('user-node');
        $user             = Auth::getUser();
        $android_add      = "";
        $android_n_add    = "";
        $android_add_new  = "";
        $ssqrs            = array();
        $ssqrs_new        = array();
        $all_nodes        = Node::where('type', '>=', '0')->orderBy('sort')->get();
        $all_nodes_toShow = Node::where('type', '>=', '0')->orderBy('sort')->paginate(15, ['*'], 'page', $pageNum);
        $all_nodes_toShow->setPath('/user/node');
        $free_nodes = Node::where('type', 0)->orderBy('sort')->get();
        if ($user->isFreeUser()) {
            $nodes_available = $free_nodes;
        } else {
            $nodes_available = $all_nodes;
        }
        foreach ($nodes_available as $node) {
            $ary['server']      = $node->server;
            $ary['server_port'] = $user->port;
            $ary['password']    = $user->passwd;
            $ary['method']      = ($node->custom_method == 1 ? $user->method : $node->method);

            $ary['obfs']       = str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->obfs : $node->obfs));
            $ary['obfs_param'] = str_replace("_compatible", "", (($node->custom_rss == 1 && $user->obfs_param != null) ? $user->obfs_param : $node->obfs_param));
            $ary['protocol']   = str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->protocol : $node->protocol));

            $ssqr = $node->getSSUrl($ary); //旧版SS
            $android_add .= $ssqr . '|';

            $ssnqr = $node->getNewSSUrl($ary); //最新原版SS
            $android_n_add .= $ssnqr . ' ';
            // $android_n_add .= $ssnqr . "%0A";
            // $android_n_add = rawurldecode($android_n_add);

            $ssqr_new = $node->getSSRUrl($ary); //SSR 新版(3.8.3之后)
            $android_add_new .= $ssqr_new . ' ';
        }

        return $this->view()->
            assign('nodes', $all_nodes_toShow)->
            assign('msg', $msg)->
            assign('android_add', $android_add)->
            assign('android_n_add', $android_n_add)->
            assign('android_add_new', $android_add_new)->
            assign('nodes_available', $nodes_available)->
            display('user/node.tpl');
    }

    public function getNodesTraffic($request, $response, $args)
    {
        $nodes = Node::where('type', '>=', 0)->get();
        foreach ($nodes as $node) {
            $nodes_traffic[$node->id] = 0;
        }
        $logs_for_nodes_traffic_chart = TrafficLog::all();
        foreach ($logs_for_nodes_traffic_chart as $log) {
            $nodes_traffic[$log->node_id] += ($log->d + $log->u);
        }
        arsort($nodes_traffic);
        foreach ($nodes_traffic as $k => $v) {
            $nodes_traffic_for_chart['labels'][] = Node::find($k)->name;
            $nodes_traffic_for_chart['data'][]   = round(Tools::flowToGB($v), 2);
        }
        $nodes_traffic_for_chart['total'] = round(array_sum($nodes_traffic_for_chart['data']), 2);
        return $this->echoJson($response, $nodes_traffic_for_chart);
    }

    public function nodeInfo($request, $response, $args)
    {
        $user    = Auth::getUser();
        $node_id = $args['id'];
        $node    = Node::find($node_id);

        if ($node == null) {
            return 'access to node that doesn\'t exist';
        }
        $ary['server']      = $node->server;
        $ary['server_port'] = $user->port;
        $ary['password']    = $user->passwd;
        $ary['method']      = ($node->custom_method == 1 ? $user->method : $node->method);
        $ary['obfs']        = str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->obfs : $node->obfs));
        $ary['obfs_param']  = (($node->custom_rss == 1 && $user->obfs_param != null) ? $user->obfs_param : $node->obfs_param);
        $ary['protocol']    = str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->protocol : $node->protocol));
        if ($node->custom_method) {
            $ary['method'] = $user->method;
        }
        $json      = json_encode($ary);
        $json_show = json_encode($ary, JSON_PRETTY_PRINT);

        $ssqr_old = $node->getSSUrl($ary); //原版（旧）
        $ssqr     = $node->getNewSSUrl($ary); //原版
        $ssqr_new = $node->getSSRUrl($ary); //SSR 新版(3.8.3之后)

        $surge_base  = Config::get('baseUrl') . "/downloads/ProxyBase.conf";
        $surge_proxy = "#!PROXY-OVERRIDE:ProxyBase.conf\n";
        $surge_proxy .= "[Proxy]\n";
        $surge_proxy .= "Proxy = custom," . $ary['server'] . "," . $ary['server_port'] . "," . $ary['method'] . "," . $ary['password'] . "," . Config::get('baseUrl') . "/downloads/SSEncrypt.module";
        return $this->view()->
            assign('node', $node)->
            assign('json', $json)->
            assign('json_show', $json_show)->
            assign('ssqr_old', $ssqr_old)->
            assign('ssqr', $ssqr)->
            assign('ssqr_new', $ssqr_new)->
            assign('surge_base', $surge_base)->
            assign('surge_proxy', $surge_proxy)->
            display('user/nodeinfo.tpl');
    }

    public function getconf($request, $response, $args)
    {
        $user = Auth::getUser();
        if ($user->plan == "A") {
            $nodes = Node::where('type', 0)->orderBy('sort')->get();
            $index = 1;
        } else {
            $nodes = Node::where('type', ">=", 0)->orderBy('sort')->get();
            $index = mt_rand(2, count($nodes));
        }
        $feedUrl = $this->getFeedUrl();
        $config_arr = [
            'index' => $index,
            'random' => true,
            'sysProxyMode' => 3,
            'shareOverLan' => false,
            'localPort' => 1080,
            'localAuthPassword' => '',
            'dnsServer' => '',
            'reconnectTimes' => 2,
            'randomAlgorithm' => 3,
            'randomInGroup' => false,
            'TTL' => 0,
            'connectTimeout' => 5,
            'proxyRuleMode' => 2,
            'proxyEnable' => false,
            'pacDirectGoProxy' => false,
            'proxyType' => 0,
            'proxyHost' => null,
            'proxyPort' => 0,
            'proxyAuthUser' => null,
            'proxyAuthPass' => null,
            'proxyUserAgent' => null,
            'authUser' => null,
            'authPass' => null,
            'autoBan' => false,
            'sameHostForSameTarget' => false,
            'keepVisitTime' => 180,
            'isHideTips' => false,
            'nodeFeedAutoUpdate' => true,
            'serverSubscribes' => [
                [
                    'URL' => 'https://raw.githubusercontent.com/breakwa11/breakwa11.github.io/master/free/freenodeplain.txt',
                    'Group' => 'FreeSSR-public'
                ],
                [
                    'URL' => $feedUrl,
                    'Group' => 'shadowsky'
                ]
            ],
            'token' => [],
            'portMap' => []
        ];

        $temparray = array();
        foreach ($nodes as $node) {
            array_push($temparray, array(
                "remarks"         => $node->name,
                "server"          => $node->server,
                "server_port"     => $user->port,
                "server_udp_port" => 0,
                "password"        => $user->passwd,
                "method"          => ($node->custom_method == 1 ? $user->method : $node->method),
                "obfs"            => str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->obfs : $node->obfs)),
                "obfsparam"       => (($node->custom_rss == 1 && $user->obfs_param != null) ? $user->obfs_param : $node->obfs_param),
                "remarks_base64"  => base64_encode($node->name),
                "group"           => "shadowsky",
                "udp_over_tcp"    => false,
                "protocol"        => str_replace("_compatible", "", ($node->custom_rss == 1 ? $user->protocol : $node->protocol)),
                "enable"          => true,
            ));
        }
        $config_arr["configs"] = $temparray;
        $config_json            = json_encode($config_arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $newResponse     = $response->withHeader('Content-type', ' application/octet-stream')->withHeader('Content-Disposition', ' attachment; filename=gui-config.json');
        $newResponse->getBody()->write($config_json);
        return $newResponse;
    }

    public function profile($request, $response, $args)
    {
        $methods   = Node::getAllMethod();
        $obfses    = Node::getAllObfs();
        $protocols = Node::getAllProtocol();

        $feedUrl = $this->getFeedUrl();
        return $this->view()->
            assign('methods', $methods)->
            assign('obfses', $obfses)->
            assign('protocols', $protocols)->
            assign('feedUrl', $feedUrl)->
            display('user/profile.tpl');
    }

    public function edit($request, $response, $args)
    {
        return $this->view()->display('user/edit.tpl');
    }

    public function invite($request, $response, $args)
    {
        $codes = $this->user->inviteCodes();
        return $this->view()->assign('codes', $codes)->display('user/invite.tpl');
    }

    public function doInvite($request, $response, $args)
    {
        $n = $this->user->invite_num;
        if ($n < 1) {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }
        for ($i = 0; $i < $n; $i++) {
            $char          = Tools::genRandomChar(32);
            $code          = new InviteCode();
            $code->code    = $char;
            $code->user_id = $this->user->id;
            $code->save();
        }
        $this->user->invite_num = 0;
        $this->user->save();
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function sys($request, $response, $args)
    {
        $users           = User::select("*")->get();
        $u               = User::sum("u");
        $d               = User::sum("d");
        $usedTransfer    = Tools::flowAutoShow($u + $d);
        $allUserCount    = count($users);
        $paidUserCount   = User::where("plan", "=", "B")->where("enable", 1)->count();
        $activeUserCount = User::where("d", "!=", 0)->count();
        $checkinCount    = User::where("last_check_in_time", ">", (time() - 24 * 3600))->count();
        $donateUserCount = User::where("ref_by", "=", 3)->count();
        $ana             = array('allUserCount' => $allUserCount, 'paidUserCount' => $paidUserCount, 'donateUserCount' => $donateUserCount, 'usedTransfer' => $usedTransfer, 'activeUserCount' => $activeUserCount, "checkinCount" => $checkinCount);
        return $this->view()->assign('ana', $ana)->assign('users', $users)->display('user/sys.tpl');
    }

    public function purchase()
    {
        $msg           = DbConfig::get('user-purchase');
        $products      = Shop::where('status', 1)->orderBy('sort')->get();
        return $this->view()->assign('products', $products)->assign('msg', $msg)->display('user/purchase.tpl');
    }

    public function qna()
    {
        return $this->view()->display('user/qna.tpl');
    }

    public function updatePassword($request, $response, $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd    = $request->getParam('pwd');
        $repwd  = $request->getParam('repwd');
        $user   = $this->user;
        if (!Hash::checkPassword($user->pass, $oldpwd)) {
            $res['ret'] = 0;
            $res['msg'] = "旧密码错误";
            return $response->getBody()->write(json_encode($res));
        }
        if ($pwd != $repwd) {
            $res['ret'] = 0;
            $res['msg'] = "两次输入不符合";
            return $response->getBody()->write(json_encode($res));
        }

        if (strlen($pwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = "密码太短啦";
            return $response->getBody()->write(json_encode($res));
        }
        $hashPwd    = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "ok";
        return $this->echoJson($response, $res);
    }

    public function updateEmail($request, $response, $args)
    {
        $verifycode = $request->getParam('verifycode');
        $email      = $request->getParam('email');
        $reemail    = $request->getParam('reemail');
        $user       = $this->user;

        // check email format
        if (!Check::isEmailLegal($email)) {
            $res['ret'] = 0;
            $res['msg'] = "邮箱无效";
            return $response->getBody()->write(json_encode($res));
        }

        // check email
        $userexist = User::where('email', $email)->first();
        if ($userexist != null) {
            $res['ret'] = 0;
            $res['msg'] = "此邮箱已存在";
            return $response->getBody()->write(json_encode($res));
        }

        // verify email
        if (!EmailVerify::checkVerifyCode($email, $verifycode)) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱验证代码不正确';
            return $response->getBody()->write(json_encode($res));
        }

        $user->email = $email;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "下次请用新邮箱登陆。";
        return $this->echoJson($response, $res);
    }

    public function updateSsConfig($request, $response, $args)
    {
        $user = Auth::getUser();

        $method = $request->getParam('method');
        $method = strtolower($method);

        $protocol = $request->getParam('protocol');
        $protocol = strtolower($protocol);
        if (in_array($protocol, ['auth_chain_a','auth_chain_b'])) {
            $method = 'none';
        } else {
            if ($method == 'none') {
            	$method = 'aes-256-cfb';
            }
        }
        $obfs = $request->getParam('obfs');
        $obfs = strtolower($obfs);

        $obfs_param = $request->getParam('obfs_param');
        if (strpos($obfs_param, '@') !== false) {
            $res['ret'] = 0;
            $res['msg'] = "混淆参数无效。";
            return $this->echoJson($response, $res);
        }
        $obfs_param = strtolower($obfs_param);

        $user->passwd     = $request->getParam('sspwd');
        $user->method     = $method;
        $user->protocol   = $protocol;
        $user->obfs       = $obfs;
        $user->obfs_param = $obfs_param;
        $user->save();
        $res['ret'] = 1;
        $res['msg'] = "配置修改成功，请重新导入节点配置，新配置将在片刻后生效。";
        return $this->echoJson($response, $res);
    }

    public function ResetPort($request, $response, $args)
    {

        $user       = Auth::getUser();
        $user->port = Tools::getAvPort();
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "设置成功，新端口是" . $user->port;
        return $response->getBody()->write(json_encode($res));
    }

    public function logout($request, $response, $args)
    {
        Auth::logout();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
        return $newResponse;
    }

    public function activate($request, $response, $args)
    {
        $user = Auth::getUser();
        try {
            $user->enable = 1;
            $user->save();
            $res['msg'] = "激活成功，稍等片刻您的账号就可以正常使用了。";
            $res['ret'] = 1;
            return $this->echoJson($response, $res);
        } catch (\Exception $e) {
        }
    }

    public function doCheckIn($request, $response, $args)
    {
        if (!$this->user->isAbleToCheckin()) {
            $res['msg'] = "您似乎已经签到过了...";
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        } else {
            if ($this->user->ref_by == 3) {
                $money   = $this->user->money;
                $traffic = 200 * log10($money) + rand(100, 200);
            } else {
                $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
            }
            $trafficToAdd                   = Tools::toMB($traffic);
            $this->user->transfer_enable    = $this->user->transfer_enable + $trafficToAdd;
            $this->user->last_check_in_time = time();
            $this->user->save();
            $res['msg'] = sprintf("获得了 %d MB流量.", $traffic);
            $res['ret'] = 1;
            // checkin log
            try {
                $log             = new CheckInLog();
                $log->user_id    = Auth::getUser()->id;
                $log->traffic    = $trafficToAdd;
                $log->checkin_at = time();
                $log->save();
            } catch (\Exception $e) {
                $res['msg'] = $e;
                $res['ret'] = 0;
            }
            return $this->echoJson($response, $res);
        }
    }

    public function vote($request, $response, $args)
    {
        $user   = $this->user;
        $uid    = $this->user->id;
        $nodeid = $request->getParam('nodeid');
        $node   = Node::find($nodeid);
        $poll   = $request->getParam('poll');
        if (!$user->isAbleToVote($node)) {
            $res['ret'] = 0;
            $res['msg'] = 'You can\'t vote this node.';
            return $this->echoJson($response, $res);
        }
        $f = Vote::where("uid", $uid)->where("nodeid", $nodeid)->first();
        if ($f) {
            $f->poll = $poll;
            $f->save();
        } else {
            $v         = new Vote;
            $v->uid    = $uid;
            $v->nodeid = $nodeid;
            $v->poll   = $poll;
            $v->save();
        }
        $res['ret'] = 1;
        switch ($poll) {
            case '1':
                $res['msg'] = "give $node->name a thumbs up";
                break;
            case '-1':
                $res['msg'] = "give $node->name a thumbs down";
                break;
            case '0':
                $res['msg'] = "delete vote of $node->name";
                break;

            default:
                # code...
                break;
        }
        return $this->echoJson($response, $res);
    }

    public function kill($request, $response, $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    public function handleKill($request, $response, $args)
    {
        $user   = Auth::getUser();
        $passwd = $request->getParam('passwd');
        // check passwd
        $res = array();
        if (!Hash::checkPassword($user->pass, $passwd)) {
            $res['ret'] = 0;
            $res['msg'] = " 密码错误";
            return $this->echoJson($response, $res);
        }
        Auth::logout();
        $fields = array(
            "id",
            "user_name",
            "plan",
            "port",
            "last_check_in_time",
            "reg_date",
            "email",
            "pass",
            "passwd",
            "u",
            "d",
            "user_type",
            "transfer_enable",
        );
        $u = new DelUser;
        foreach ($fields as $field) {
            $u->$field = $user->$field;
        }
        $u->save();
        $user->delete();
        $res['ret'] = 1;
        $res['msg'] = "GG!您的帐号已经从我们的系统中删除.";
        return $this->echoJson($response, $res);
    }

    public static function getTrafficInfoArrayForChart($uid)
    {
        $logs        = TrafficLog::where('user_id', $uid)->orderBy('id', 'ASC')->get();
        $labels_hour = []; // time odd hour
        $labels_node = []; // node name
        $datas       = [
            [], // Hours
            [] // Nodes
        ];

        for ($i = 0; $i <= intval(date('H')); $i++) {
            $label_name = date('G a', strtotime("$i:00:00"));
            array_push($labels_hour, $label_name);
        }

        foreach ($logs as $log) {
            /**
             * 用户每小时使用的流量 $datas[0][{小时}]
             */
            if (isset($datas[0][date('H', $log->log_time)])) {
                $datas[0][date('H', $log->log_time)] += round((($log->u + $log->d) / 1048576), 2);
            } else {
                $datas[0][date('H', $log->log_time)] = round((($log->u + $log->d) / 1048576), 2);
            }
            foreach ($datas[0] as $k => $v) {
                $datas[0][$k] = round($datas[0][$k], 2);
            }
            /**
             * 用户每个节点使用的流量 $datas[1][{节点名}]
             */
            if (isset($datas[1][Node::where('id', $log->node_id)->first()->name])) {
                $datas[1][Node::where('id', $log->node_id)->first()->name] += round((($log->u + $log->d) / 1048576), 2);
            } else {
                $datas[1][Node::where('id', $log->node_id)->first()->name] = round((($log->u + $log->d) / 1048576), 2);
            }
            foreach ($datas[1] as $k => $v) {
                $datas[1][$k] = round($datas[1][$k], 2);
            }
        }
        ksort($datas[1]);
        
        $d = array('0' => array(), '1' => array());

        for ($i = 0; $i <= date('H', time()); $i++) {
            if ($i < 10) {
                $n = "0" . $i;
            } else {
                $n = $i;
            }
            if (!isset($datas[0][$n])) {
                array_push($d[0], 0);
            } else {
                array_push($d[0], $datas[0][$n]);
            }
        }

        /**
         * 形成x轴数组和y轴数组
         */
        if (isset($datas[1])) {
            foreach ($datas[1] as $k => $v) {
                array_push($d[1], $v);
                array_push($labels_node, $k);
            }
        }

        $labels          = [$labels_hour, $labels_node];
        $datas           = $d;
        $array_for_chart = [$labels, $datas];

        return $array_for_chart;
    }

    public function trafficLog($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $traffic = TrafficLog::where('user_id', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $traffic->setPath('/user/trafficlog');

        $array_for_chart = UserController::getTrafficInfoArrayForChart($this->user->id);
        $array_for_chart = json_encode($array_for_chart);

        $users_weekly_traffic_logs = UserDailyTrafficLog::where('uid', $this->user->id)->where('date', '>=', date('Y-m-d', strtotime('-6 days')))->get();
        foreach ($users_weekly_traffic_logs as $log) {
            $users_weekly_traffic_for_chart['labels'][] = $log->date;
            $users_weekly_traffic_for_chart['datas'][]  = round(Tools::flowToGB($log->traffic), 3);
        }
        $users_weekly_traffic_for_chart['labels'][] = date('Y-m-d');
        $users_weekly_traffic_for_chart['datas'][]  = round(Tools::flowToGB($this->user->trafficToday()), 3);
        $users_weekly_traffic_for_chart             = json_encode($users_weekly_traffic_for_chart);
        return $this->view()->
            assign('logs', $traffic)->
            assign('array_for_chart', $array_for_chart)->
            assign('users_weekly_traffic_for_chart', $users_weekly_traffic_for_chart)->
            display('user/trafficlog.tpl');
    }

    public function purchaseLog($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $logs = PurchaseLog::where('uid', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $logs->setPath('/user/purchaselog');
        return $this->view()->assign('logs', $logs)->display('user/purchaselog.tpl');
    }

    public function getFeedUrl()
    {
        $user = $this->user;
        $feedToken = $user->feedToken();
        $feedUrl = Config::getPublicConfig()['baseUrl'] . '/feed?token=' . $feedToken . '&uid=' . $user->id;
        return $feedUrl;
    }
}
