<?php

namespace App\Controllers;

use App\Models\CheckInLog;
use App\Models\PurchaseLog;
use App\Models\DonateLog;
use App\Models\InviteCode;
use App\Models\TrafficLog;
use App\Models\Ann;
use App\Services\Analytics;
use App\Services\DbConfig;
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
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $traffic = CheckInLog::orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $traffic->setPath('/admin/checkinlog');
        return $this->view()->assign('logs', $traffic)->display('admin/checkinlog.tpl');
    }

    public function purchaseLog($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $purchase = PurchaseLog::orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $purchase->setPath('/admin/purchaselog');
        return $this->view()->assign('logs', $purchase)->display('admin/purchaselog.tpl');
    }

    public function donateLog($request, $response, $args)
    {
        $pageNum = 1;
        if (isset($request->getQueryParams()["page"])) {
            $pageNum = $request->getQueryParams()["page"];
        }
        $donate = DonateLog::orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $donate->setPath('/admin/donatelog');
        return $this->view()->assign('logs', $donate)->display('admin/donatelog.tpl');
    }

    public function trafficLog($request, $response, $args)
    {
        $pageNum = isset($request->getQueryParams()["page"]) ? $request->getQueryParams()["page"] : 1;
        $user_id = isset($request->getQueryParams()["user_id"]) ? $request->getQueryParams()["user_id"] : 0;
        $node_id = isset($request->getQueryParams()["node_id"]) ? $request->getQueryParams()["node_id"] : 0;

        if ($user_id && $node_id) {
            $logs = TrafficLog::where("user_id", $user_id)->where("node_id", $node_id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
            $logs->setPath('/admin/trafficlog?user_id=' . $user_id . "&node_id=" . $node_id);
        }elseif ($user_id) {
            $logs = TrafficLog::where("user_id", $user_id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
            $logs->setPath('/admin/trafficlog?user_id='. $user_id);
        }elseif ($node_id) {
            $logs = TrafficLog::where("node_id", $node_id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
            $logs->setPath('/admin/trafficlog?node_id='. $node_id);
        }else {
            $logs = TrafficLog::orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
            $logs->setPath('/admin/trafficlog');
        }
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

}
