<?php

namespace App\Controllers;

use App\Models\CheckInLog;
use App\Models\InviteCode;
use App\Models\User;
use App\Models\DelUser;
use App\Models\Node;
use App\Models\TrafficLog;
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
        return parent::view()->assign('userFooter', $userFooter);
    }

    public function index($request, $response, $args)
    {
        $msg = DbConfig::get('user-index');
        if ($msg == null) {
            $msg = "在后台修改用户中心公告...";
        }
        return $this->view()->assign('msg', $msg)->display('user/index.tpl');
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
            $ary['method'] = $node->method;

            $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
            $ssqr = "ss://" . base64_encode($ssurl); //原版
            $android_add .= $ssqr."|";
            $ssqrs[$node->name] = $ssqr;

            $ssurl = ($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs).":".($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol).":".$ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port']."/".base64_encode($node->obfs_param);
            $ssqr_s = "ss://" . base64_encode($ssurl);  //SSR (3.8.3之前)

            $ssurl = $ary['server']. ":" . $ary['server_port'].":".($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol).":".$ary['method'].":".($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs).":".Tools::base64_url_encode($ary['password'])."/?obfsparam=".Tools::base64_url_encode($node->obfs_param)."&remarks=".Tools::base64_url_encode($node->name)."&group=".Tools::base64_url_encode("shadowsky");
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
        $ary['method'] = $node->method;
        $ary['obfs'] = ($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs);
        $ary['obfs_param'] = $node->obfs_param;
        $ary['protocol'] = ($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol);
        if ($node->custom_method) {
            $ary['method'] = $this->user->method;
        }
        $json = json_encode($ary);
        $json_show = json_encode($ary, JSON_PRETTY_PRINT);

        $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
        $ssqr = "ss://" . base64_encode($ssurl); //原版
        $ssurl = ($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs).":".($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol).":".$ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port']."/".base64_encode($node->obfs_param);
        $ssqr_s = "ss://" . base64_encode($ssurl);  //SSR (3.8.3之前)
        $ssurl = $ary['server']. ":" . $ary['server_port'].":".($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol).":".$ary['method'].":".($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs).":".Tools::base64_url_encode($ary['password'])."/?obfsparam=".Tools::base64_url_encode($node->obfs_param)."&remarks=".Tools::base64_url_encode($node->name)."&group=".Tools::base64_url_encode("shadowsky");
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
    "index" : 0,
    "random" : false,
    "global" : false,
    "enabled" : false,
    "shareOverLan" : false,
    "isDefault" : false,
    "bypassWhiteList" : false,
    "localPort" : 1080,
    "reconnectTimes" : 3,
    "randomAlgorithm" : 0,
    "TTL" : 0,
    "proxyEnable" : false,
    "pacDirectGoProxy" : false,
    "proxyType" : 0,
    "proxyHost" : null,
    "proxyPort" : 0,
    "proxyAuthUser" : null,
    "proxyAuthPass" : null,
    "proxyUserAgent" : null,
    "authUser" : null,
    "authPass" : null,
    "autoBan" : false,
    "sameHostForSameTarget" : false,
    "keepVisitTime" : 180,
    "dns_server" : null,
    "token" : {

    },
    "portMap" : {

    }
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
                                        "obfs"=>($node->custom_rss==1?str_replace("_compatible", "", $node->obfs):$node->obfs),
                                        "obfsparam"=>"cloudflare.com",
                                        "remarks_base64"=>base64_encode($node->name),
                                        "group"=>"shadowsky",
                                        "udp_over_tcp"=>false,
                                        "protocol"=>($node->custom_rss==1?str_replace("_compatible", "", $node->protocol):$node->protocol),
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
        return $this->view()->display('user/profile.tpl');
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
        return $this->view()->assign('ana', $ana)->assign('url', $_SERVER['REQUEST_URI'])->assign('users', $users)->display('user/sys.tpl');
    }

    public function purchase()
    {
        $msg = DbConfig::get('user-purchase');
        $user = Auth::getUser();
        $menu1 = array(
            ["name"=>"1元1G试玩套餐","transfer"=>"1G","price"=>1,"body"=>"试玩","time"=>"3天"],
            ["name"=>"5元10G基础套餐","transfer"=>"10G","price"=>5,"body"=>"基础","time"=>"永久"],
            ["name"=>"10元25G标准套餐","transfer"=>"25G","price"=>10,"body"=>"标准","time"=>"永久"],
            ["name"=>"20元55G高级套餐","transfer"=>"55G","price"=>20,"body"=>"高级","time"=>"永久"]
        );
        $menu2 = array(
            ["name"=>"10元包月无限流量套餐","price"=>10,"body"=>"包月","time"=>"一月"],
            ["name"=>"25元包季无限流量套餐","price"=>25,"body"=>"包季","time"=>"一季"],
            ["name"=>"80元包年无限流量套餐","price"=>80,"body"=>"包年","time"=>"一年"]
        );
        return $this->view()->assign('menu1', $menu1)->assign('menu2', $menu2)->assign('user', $user)->assign('msg', $msg)->display('user/purchase.tpl');
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

    public function updateSsPwd($request, $response, $args)
    {
        $user = Auth::getUser();
        $pwd = $request->getParam('sspwd');
        $user->updateSsPwd($pwd);
        $res['ret'] = 1;
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

    public function updateMethod($request, $response, $args)
    {
        $user = Auth::getUser();
        $method = $request->getParam('method');
        $method = strtolower($method);
        $user->updateMethod($method);
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
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

    public function trafficLog($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $traffic = TrafficLog::where('user_id', $this->user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $traffic->setPath('/user/trafficlog');
        return $this->view()->assign('logs', $traffic)->display('user/trafficlog.tpl');
    }
}
