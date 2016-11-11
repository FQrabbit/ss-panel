<?php

namespace App\Controllers;

use App\Models\CheckInLog;
use App\Models\PurchaseLog;
use App\Models\DonateLog;
use App\Models\InviteCode;
use App\Models\TrafficLog;
use App\Models\Ann;
use App\Models\User;
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
        $n = $request->getParam('num');
        $prefix = $request->getParam('prefix');
        $uid = $request->getParam('uid');
        if ($n < 1) {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }
        for ($i = 0; $i < $n; $i++) {
            $char = Tools::genRandomChar(32);
            $code = new InviteCode();
            $code->code = $prefix . $char;
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
        $logs = CheckInLog::where('id', ">" , 0);
        $path = '/admin/checkinlog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k.'='.$v.'&';
            }
        }
        $path = substr($path,0,strlen($path)-1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/checkinlog.tpl');
    }

    public function purchaseLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = PurchaseLog::where('id', ">" , 0);
        $path = '/admin/purchaselog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k.'='.$v.'&';
            }
        }
        $path = substr($path,0,strlen($path)-1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/purchaselog.tpl');
    }

    public function addPurchase($request, $response, $args)
    {
        $q = $request->getParsedBody();
        $record = PurchaseLog::create($q);
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($q));
    }

    public function buy($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        return $this->view()->display('admin/buy.tpl');
    }

    public function donateLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = DonateLog::where('id', ">" , 0);
        $path = '/admin/donatelog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k.'='.$v.'&';
            }
        }
        $path = substr($path,0,strlen($path)-1);
        $logs = $logs->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/donatelog.tpl');
    }

    public function trafficLog($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $logs = TrafficLog::where('id', ">" , 0);
        $path = '/admin/trafficlog?';
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $logs = $logs->where($k, $v);
                $path .= $k.'='.$v.'&';
            }
        }
        $path = substr($path,0,strlen($path)-1);
        $logs = $logs->paginate(15, ['*'], 'page', $q['page']);
        $logs->setPath($path);
        return $this->view()->assign('logs', $logs)->display('admin/trafficlog.tpl');
    }

    public function config($request, $response, $args)
    {
        $conf = [
            "app-name" => DbConfig::get('app-name'),
            "home-code" => DbConfig::get('home-code'),
            "home-purchase" => DbConfig::get('home-purchase'),
            "analytics-code" => DbConfig::get('analytics-code'),
            "user-index" => DbConfig::get('user-index'),
            "user-node" => DbConfig::get('user-node'),
            "user-purchase" => DbConfig::get('user-purchase'),
        ];
        $ann = Ann::orderBy("id", "desc")->get()->first();
        return $this->view()->assign('conf', $conf)->assign('ann', $ann)->display('admin/config.tpl');
    }

    public function updateConfig($request, $response, $args)
    {
        $config = [
            "analytics-code" => $request->getParam('analyticsCode'),
            "home-code" => $request->getParam('homeCode'),
            "home-purchase" => $request->getParam('homePurchase'),
            "app-name" => $request->getParam('appName'),
            "user-index" => $request->getParam('userIndex'),
            "user-node" => $request->getParam('userNode'),
            "user-purchase" => $request->getParam('userPurchase'),
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
        $ann_title = $request->getParam('ann_title');
        $ann_content = $request->getParam('ann_content');
        $ann = Ann::orderBy("id", "desc")->get()->first();
        $ann->title = $ann_title;
        $ann->content = $ann_content;
        $ann->save();
        $res['ret'] = 1;
        $res['msg'] = "更新邮箱公告成功";
        return $response->getBody()->write(json_encode($res));
    }

    public function sendMailPost($request, $response, $args)
    {
        $q = $request->getParsedBody();
        $users = User::where('id', '>', 0);
        foreach ($q as $k => $v) {
            if ($v != "") {
                $users = $users->where($k, $v);
            }
        }
        $users = $users->get();

        $ann = Ann::orderBy("id", "desc")->get()->first();
        $arr = [
            "title" => $ann->title,
            "content" => $ann->content,
            "user_name" => ""
        ];

        $res['names'] = [];
        $i = 0;
        if ($users && $ann->title && $ann->content) {
            foreach ($users as $user) {
                $arr["user_name"] = $user->user_name;
                $res['names'][$i++] = $user->user_name;
                try {
                    Mail::send($user->email, $ann->title, 'news/announcement.tpl', $arr, []);
                    $res['ret'] = 1;
                    $res['msg'] = "发送邮箱公告成功";
                } catch (\Exception $e) {
                    $res['ret'] = 0;
                    $res['msg'] = $e->getMessage();
                }
            }
        }else{
            $res['ret'] = 0;
            $res['msg'] = "无符合条件的用户或无公告";
        }
        $res['total'] = count($users);
        return $response->getBody()->write(json_encode($res));
    }
}
