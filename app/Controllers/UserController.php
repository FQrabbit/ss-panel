<?php

namespace App\Controllers;

use App\Models\CheckInLog;
use App\Models\InviteCode;
use App\Models\User;
use App\Models\Ann;
use App\Models\AnnLog;
use App\Models\DelUser;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\Vote;
use App\Models\PurchaseLog;
use App\Models\Music;
use App\Models\Shop;
use App\Services\Auth;
use App\Services\Config;
use App\Services\DbConfig;
use App\Services\Auth\EmailVerify;
use App\Utils\Hash;
use App\Utils\Check;
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
        return parent::view()->assign('userFooter', $userFooter)->assign('url', strtok($_SERVER["REQUEST_URI"],'?'));
    }

    public function index($request, $response, $args)
    {
        $msg = DbConfig::get('user-index');
        if ($msg == null) {
            $msg = "在后台修改用户中心公告...";
        }
        $new_ann = Ann::orderBy('id', 'DESC')->first();
        $title = $this->user->port."0";
        $music = Music::orderByRaw("RAND()")->first();
        $music->count += 1;
        $mid = $music->mid;
        $music->save();
        return $this->view()->assign('msg', $msg)->assign('new_ann', $new_ann)->assign('title', $title)->assign('mid', $mid)->display('user/index.tpl');
    }

    public function readAnn($request, $response, $args)
    {
        $ann_id = $args['id'];
        $log = AnnLog::where('user_id', $this->user->id)->where('ann_id', $ann_id)->first();
        if (!$log) {
            $log = new AnnLog();
            $log->ann_id = $ann_id;
            $log->user_id = $this->user->id;
            $log->read_status = 1;
            $log->save();
        }else {
            $log->read_status = 1;
            $log->save();
        }
        return "Read";
    }

    public function node($request, $response, $args)
    {
        $msg = DbConfig::get('user-node');
        $user = Auth::getUser();
        $android_add = "";
        $android_add_new = "";
        $ssqrs = array();
        $ssqrs_new = array();
        $allnodes = Node::where('type', '>=', 0)->orderBy('sort')->get();
        $free_nodes = Node::where('type', 0)->orderBy('sort')->get();
        if ($user->plan == "A")
        {
            $nodes_available = $free_nodes;
        }
        else
        {
            $nodes_available = $allnodes;
        }
        foreach ($nodes_available as $node)
        {
            $ary['server'] = $node->server;
            $ary['server_port'] = $user->port;
            $ary['password'] = $user->passwd;
            $ary['method'] = ($node->custom_method==1?$user->method:$node->method);
            $ary['obfs'] = str_replace("_compatible", "", ($node->custom_rss==1?$this->user->obfs:$node->obfs));
            $ary['obfs_param'] = str_replace("_compatible", "", (($node->custom_rss==1&&$this->user->obfs_param!=NULL)?$this->user->obfs_param:$node->obfs_param));
            $ary['protocol'] = str_replace("_compatible", "", ($node->custom_rss==1?$this->user->protocol:$node->protocol));

            $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
            $ssqr = "ss://" . base64_encode($ssurl); //原版
            $android_add .= $ssqr."|";
            $ssqrs[$node->name] = $ssqr;

            $ssurl = $ary['obfs'].":".$ary['protocol'].":".$ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port']."/".base64_encode($ary['obfs_param']);
            $ssqr_s = "ss://" . base64_encode($ssurl);  //SSR (3.8.3之前)

            $ssurl = $ary['server']. ":" . $ary['server_port'].":".$ary['protocol'].":".$ary['method'].":".$ary['obfs'].":".Tools::base64_url_encode($ary['password'])."/?obfsparam=".Tools::base64_url_encode($ary['obfs_param'])."&remarks=".Tools::base64_url_encode($node->name)."&group=".Tools::base64_url_encode("shadowsky");
            $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);  //SSR 新版(3.8.3之后)
            $android_add_new .= $ssqr_s_new."|";
            $ssqrs_new[$node->name] = $ssqr_s_new;
        }
        return $this->view()->assign('nodes', $allnodes)->assign('user', $user)->assign('msg',$msg)->assign('android_add',$android_add)->assign('android_add_new',$android_add_new)->assign('ssqrs',$ssqrs)->assign('ssqrs_new',$ssqrs_new)->assign('nodes_available',$nodes_available)->display('user/node.tpl');
    }


    public function nodeInfo($request, $response, $args)
    {
        $id = $args['id'];
        $node = Node::find($id);

        if ($node == null) {

        }
        $ary['server'] = $node->server;
        $ary['server_port'] = $this->user->port;
        $ary['password'] = $this->user->passwd;
        $ary['method'] = ($node->custom_method==1?$user->method:$node->method);
        $ary['obfs'] = str_replace("_compatible", "", ($node->custom_rss==1?$this->user->obfs:$node->obfs));
        $ary['obfs_param'] = (($node->custom_rss==1&&$this->user->obfs_param!=NULL)?$this->user->obfs_param:$node->obfs_param);
        $ary['protocol'] = str_replace("_compatible", "", ($node->custom_rss==1?$this->user->protocol:$node->protocol));
        if ($node->custom_method) {
            $ary['method'] = $this->user->method;
        }
        $json = json_encode($ary);
        $json_show = json_encode($ary, JSON_PRETTY_PRINT);

        $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
        $ssqr = "ss://" . base64_encode($ssurl); //原版
        $ssurl = $ary['obfs'].":".$ary['protocol'].":".$ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port']."/".base64_encode($node->obfs_param);
        $ssqr_s = "ss://" . base64_encode($ssurl);  //SSR (3.8.3之前)
        $ssurl = $ary['server']. ":" . $ary['server_port'].":".$ary['protocol'].":".$ary['method'].":".$ary['obfs'].":".Tools::base64_url_encode($ary['password'])."/?obfsparam=".Tools::base64_url_encode($node->obfs_param)."&remarks=".Tools::base64_url_encode($node->name)."&group=".Tools::base64_url_encode("shadowsky");
        $ssqr_s_new = "ssr://" . Tools::base64_url_encode($ssurl);  //SSR 新版(3.8.3之后)

        $surge_base = Config::get('baseUrl') . "/downloads/ProxyBase.conf";
        $surge_proxy = "#!PROXY-OVERRIDE:ProxyBase.conf\n";
        $surge_proxy .= "[Proxy]\n";
        $surge_proxy .= "Proxy = custom," . $ary['server'] . "," . $ary['server_port'] . "," . $ary['method'] . "," . $ary['password'] . "," . Config::get('baseUrl') . "/downloads/SSEncrypt.module";
        return $this->view()->assign('node', $node)->assign('json', $json)->assign('json_show', $json_show)->assign('ssqr', $ssqr)->assign('ssqr_s', $ssqr_s)->assign('ssqr_s_new', $ssqr_s_new)->assign('surge_base', $surge_base)->assign('surge_proxy', $surge_proxy)->display('user/nodeinfo.tpl');
    }
    
    public function getconf($request, $response, $args){
        $user = Auth::getUser();
        if ($user->plan=="A") {
            $nodes = Node::where('type', 0)->orderBy('sort')->get();
        }else{
            $nodes = Node::where('type', ">=", 0)->orderBy('sort')->get();
        }
        $string='
{
    "index" : 1
}
        ';
        
        
        $json=json_decode($string,TRUE);
        $temparray=array();
        foreach($nodes as $node)
        {
            array_push($temparray,array(
                                        "remarks"=>$node->name,
                                        "server"=>$node->server,
                                        "server_port"=>$user->port,
                                        "server_udp_port"=>0,
                                        "password"=>$user->passwd,
                                        "method"=>($node->custom_method==1?$user->method:$node->method),
                                        "obfs"=>str_replace("_compatible", "", ($node->custom_rss==1?$this->user->obfs:$node->obfs)),
                                        "obfsparam"=>(($node->custom_rss==1&&$this->user->obfs_param!=NULL)?$this->user->obfs_param:$node->obfs_param),
                                        "remarks_base64"=>base64_encode($node->name),
                                        "group"=>"shadowsky",
                                        "udp_over_tcp"=>false,
                                        "protocol"=>str_replace("_compatible", "", ($node->custom_rss==1?$this->user->protocol:$node->protocol)),
                                        "enable"=>true
            ));
        }
        $json["configs"]=$temparray;
        $json = json_encode($json,JSON_PRETTY_PRINT);
        $newResponse = $response->withHeader('Content-type', ' application/octet-stream')->withHeader('Content-Disposition', ' attachment; filename=gui-config.json');
        $newResponse->getBody()->write($json);
        return $newResponse;
    }

    public function profile($request, $response, $args)
    {
        $methods = Node::getAllMethod();
        $obfses = Node::getAllObfs();
        $protocols = Node::getAllProtocol();
        return $this->view()->assign('methods', $methods)->assign('obfses', $obfses)->assign('protocols', $protocols)->display('user/profile.tpl');
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
            $char = Tools::genRandomChar(32);
            $code = new InviteCode();
            $code->code = $char;
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
        $users = User::select("*")->get();
        $u = User::sum("u");
        $d = User::sum("d");
        $usedTransfer = Tools::flowAutoShow($u + $d);
        $allUserCount = count($users);
        $paidUserCount = User::where("plan", "=", "B")->where("enable", 1)->count();
        $activeUserCount = User::where("d", "!=", 0)->count();
        $checkinCount = User::where("last_check_in_time", ">", (time()-24*3600))->count();
        $donateUserCount = User::where("ref_by", "=", 3)->count();
        $ana = array('allUserCount' => $allUserCount, 'paidUserCount' => $paidUserCount, 'donateUserCount' => $donateUserCount, 'usedTransfer' => $usedTransfer, 'activeUserCount' => $activeUserCount, "checkinCount" => $checkinCount);
        return $this->view()->assign('ana', $ana)->assign('users', $users)->display('user/sys.tpl');
    }

    public function purchase()
    {
        $msg = DbConfig::get('user-purchase');
        $now_date = date("Y-m-d H:i:s");
        $B_count = User::where("expire_date", ">", $now_date)->get()->count();
        $nodes_count = Node::all()->count();
        $B_able_to_buy = (($nodes_count*13)>$B_count) ? 1 : 0;
        $user = Auth::getUser();
        $products = Shop::where('status', 1)->get();
        return $this->view()->assign('products', $products)->assign('user', $user)->assign('msg', $msg)->assign('B_able_to_buy', $B_able_to_buy)->display('user/purchase.tpl');
    }

    public function qna()
    {
        return $this->view()->display('user/qna.tpl');
    }

    public function updatePassword($request, $response, $args)
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;
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
        $hashPwd = Hash::passwordHash($pwd);
        $user->pass = $hashPwd;
        $user->save();

        $res['ret'] = 1;
        $res['msg'] = "ok";
        return $this->echoJson($response, $res);
    }

    public function updateEmail($request, $response, $args)
    {
        $verifycode = $request->getParam('verifycode');
        $email = $request->getParam('email');
        $reemail = $request->getParam('reemail');
        $user = $this->user;

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

        $obfs = $request->getParam('obfs');
        $obfs = strtolower($obfs);

        $obfs_param = $request->getParam('obfs_param');
        $obfs_param = strtolower($obfs_param);

        $user->passwd = $request->getParam('sspwd');
        $user->method = $method;
        $user->protocol = $protocol;
        $user->obfs = $obfs;
        $user->obfs_param = $obfs_param;
        $user->save();
        $res['ret'] = 1;
        $res['msg'] = "配置修改成功，请重新导入节点配置，新配置将在片刻后生效。";
        return $this->echoJson($response, $res);
    }
    
    public function ResetPort($request, $response, $args)
    {
        
        $user = Auth::getUser();
        $user->port=Tools::getAvPort();
        $user->save();
        
        
        $res['ret'] = 1;
        $res['msg'] = "设置成功，新端口是".$user->port;
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
        } catch (Exception $e) {
        }
    }

    public function doCheckIn($request, $response, $args)
    {
        if (!$this->user->isAbleToCheckin()) {
            $res['msg'] = "您似乎已经签到过了...";
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        }else{
            if ($this->user->ref_by == 3) {
            	$money = $this->user->money;
                $traffic = 200*log10($money)+rand(100,200);
            }else{
                $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
            }
            $trafficToAdd = Tools::toMB($traffic);
            $this->user->transfer_enable = $this->user->transfer_enable + $trafficToAdd;
            $this->user->last_check_in_time = time();
            $this->user->save();
            // checkin log
            try {
                $log = new CheckInLog();
                $log->user_id = Auth::getUser()->id;
                $log->traffic = $trafficToAdd;
                $log->checkin_at = time();
                $log->save();
            } catch (\Exception $e) {
            }
            $res['msg'] = sprintf("获得了 %u MB流量.", $traffic);
            $res['ret'] = 1;
            return $this->echoJson($response, $res);
        }
    }

    public function vote($request, $response, $args)
    {
        $uid = $this->user->id;
        $nodeid = $request->getParam('nodeid');
        $poll = $request->getParam('poll');
        $f = Vote::where("uid", $uid)->where("nodeid", $nodeid)->first();
        if ($f) {
            $f->poll = $poll;
            $f->save();
        }else {
            $v = new Vote;
            $v->uid = $uid;
            $v->nodeid = $nodeid;
            $v->poll = $poll;
            $v->save();
        }
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
    }

    public function kill($request, $response, $args)
    {
        return $this->view()->display('user/kill.tpl');
    }

    public function handleKill($request, $response, $args)
    {
        $user = Auth::getUser();
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
            "transfer_enable"
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
        $logs = TrafficLog::where('user_id', $uid)->orderBy('id','ASC')->get();
        $labels_hour = array(); // time odd hour
        $labels_node = array(); // node name
        $datas = array();
        for($i=0;$i<=date("H");$i++){
            if ($i%2!=0) {
                $label_name = '';
            }elseif ($i<12) {
                $label_name = $i.' a.m.';
            }else {
                $label_name = $i.' p.m.';
            }
            array_push($labels_hour,$label_name);
        }
        
        foreach ($logs as $log) {
            $datas[0][date('H',$log->log_time)] += round((($log->u+$log->d)/1048576),2);
            $datas[1][Node::where('id', $log->node_id)->get()->first()->name] += round((($log->u+$log->d)/1048576),2);
        }
        $d = array('0'=>array(),'1'=>array());
        for ($i = 0; $i <= date('H',$log->log_time); $i++) {
            if ($i<10) {
                $n = "0".$i;
            }else {
                $n = $i;
            }
            if (!isset($datas[0][$n])) {
                array_push($d[0], 0);
            }else {
                array_push($d[0], $datas[0][$n]);
            }
        }
        foreach ($datas[1] as $k=>$v) {
            array_push($d[1], $v);
            array_push($labels_node,$k);
        }
        $labels = [$labels_hour,$labels_node];
        $datas = $d;
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
        $logs = TrafficLog::where('user_id', $this->user->id)->orderBy('id','ASC')->get();

        $array_for_chart = UserController::getTrafficInfoArrayForChart($this->user->id);
        $array_for_chart = json_encode($array_for_chart);
        
        return $this->view()->assign('logs', $traffic)->assign('array_for_chart', $array_for_chart)->display('user/trafficlog.tpl');
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
}
