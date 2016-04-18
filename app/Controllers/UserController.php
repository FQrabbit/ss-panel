<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Services\Auth, App\Models\User;
use App\Models\Node, App\Models\TrafficLog, App\Models\CheckInLog;
use App\Services\Config,App\Services\DbConfig;
use App\Utils\Hash, App\Utils\Tools;


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
        return parent::view()->assign('userFooter',$userFooter); 
    }

    public function index($request, $response, $args)
    {
        $msg = DbConfig::get('user-index');
        if($msg == null ){
            $msg = "在后台修改用户中心公告...";
        }
        return $this->view()->assign('msg',$msg)->display('user/index.tpl');
    }

    public function node($request, $response, $args)
    {
        $msg = DbConfig::get('user-node');
        $user = Auth::getUser();
        $nodes = Node::where('type', ">=", 0)->orderBy('sort')->get();
        return $this->view()->assign('nodes', $nodes)->assign('user', $user)->assign('msg',$msg)->display('user/node.tpl');
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
        if ($node->custom_method) {
            $ary['method'] = $this->user->method;
        }
        $json = json_encode($ary);
        $json_show = json_encode($ary, JSON_PRETTY_PRINT);
        $ssurl = $ary['method'] . ":" . $ary['password'] . "@" . $ary['server'] . ":" . $ary['server_port'];
        $ssqr = "ss://" . base64_encode($ssurl);

        $surge_base = Config::get('baseUrl') . "/downloads/ProxyBase.conf";
        $surge_proxy = "#!PROXY-OVERRIDE:ProxyBase.conf\n";
        $surge_proxy .= "[Proxy]\n";
        $surge_proxy .= "Proxy = custom," . $ary['server'] . "," . $ary['server_port'] . "," . $ary['method'] . "," . $ary['password'] . "," . Config::get('baseUrl') . "/downloads/SSEncrypt.module";
        return $this->view()->assign('node', $node)->assign('json', $json)->assign('json_show', $json_show)->assign('ssqr', $ssqr)->assign('surge_base', $surge_base)->assign('surge_proxy', $surge_proxy)->display('user/nodeinfo.tpl');
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
        $paidUserCount = User::where("plan", "=", "B")->count();
        $activeUserCount = User::where("d", "!=", 0)->count();
        $checkinCount = User::where("last_check_in_time", ">", (time()-24*3600))->count();
        $donateUserCount = User::where("ref_by", "=", 3)->count();
        $ana = array('allUserCount' => $allUserCount, 'paidUserCount' => $paidUserCount, 'donateUserCount' => $donateUserCount, 'usedTransfer' => $usedTransfer, 'activeUserCount' => $activeUserCount, "checkinCount" => $checkinCount);
        return $this->view()->assign('ana', $ana)->assign('url', $_SERVER['REQUEST_URI'])->assign('users', $users)->display('user/sys.tpl');
    }

    public function purchase()
    {
        $user = Auth::getUser();
        $menu1 = array(
            ["name"=>"1元2G试玩套餐","transfer"=>"2G","price"=>1,"body"=>"试玩","time"=>"3天"],
            ["name"=>"5元15G基础套餐","transfer"=>"15G","price"=>5,"body"=>"基础","time"=>"永久"],
            ["name"=>"10元35G标准套餐","transfer"=>"35G","price"=>10,"body"=>"标准","time"=>"永久"],
            ["name"=>"20元75G高级套餐","transfer"=>"75G","price"=>20,"body"=>"高级","time"=>"永久"]
        );
        $menu2 = array(
            ["name"=>"10元包月无限流量套餐","price"=>10,"body"=>"包月","time"=>"一月"],
            ["name"=>"25元包季无限流量套餐","price"=>25,"body"=>"包季","time"=>"一季"],
            ["name"=>"80元包年无限流量套餐","price"=>80,"body"=>"包年","time"=>"一年"]
        );
        return $this->view()->assign('menu1', $menu1)->assign('menu2', $menu2)->assign('user', $user)->display('user/purchase.tpl');
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

    public function updateSsPwd($request, $response, $args)
    {
        $user = Auth::getUser();
        $pwd = $request->getParam('sspwd');
        $user->updateSsPwd($pwd);
        $res['ret'] = 1;
        return $this->echoJson($response, $res);
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

    public function doCheckIn($request, $response, $args)
    {
        if (!$this->user->isAbleToCheckin()) {
            $res['msg'] = "您似乎已经签到过了...";
            $res['ret'] = 1;
            return $response->getBody()->write(json_encode($res));
        }else{
            if ($this->user->ref_by == 3) {
                $traffic = rand(300, 400);
            }else{
                $traffic = rand(Config::get('checkinMin'), Config::get('checkinMax'));
            }
            $this->user->transfer_enable = $this->user->transfer_enable + Tools::toMB($traffic);
            $this->user->last_check_in_time = time();
            $this->user->save();
            $res['msg'] = sprintf("获得了 %u MB流量.", $traffic);
            $res['ret'] = 1;
        }
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
