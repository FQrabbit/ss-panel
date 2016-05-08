<?php

namespace App\Controllers;

//use Psr\Http\Message\ServerRequestInterface as Request;
//use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Request;
use Slim\Http\Response;

use App\Models\InviteCode;
use App\Services\Auth;
use App\Services\Config;
use App\Services\DbConfig;
use App\Services\Logger;
use App\Utils\Check;
use App\Utils\Http;

/**
 *  HomeController
 */
class HomeController extends BaseController
{

    public function index()
    {
        $homeIndexMsg = DbConfig::get('home-index');
        return $this->view()->assign('homeIndexMsg', $homeIndexMsg)->display('index.tpl');
    }

    public function code()
    {
        $msg = DbConfig::get('home-code');
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $this->view()->assign('codes', $codes)->assign('msg', $msg)->display('code.tpl');
    }

    public function purchase()
    {
        $msg = DbConfig::get('home-purchase');
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
        return $this->view()->assign('msg', $msg)->assign('menu1', $menu1)->assign('menu2', $menu2)->display('purchase.tpl');
    }

    public function debug($request, $response, $args)
    {
        $server = [
            "headers" => $request->getHeaders(),
            "content_type" => $request->getContentType()
        ];
        $res = [
            "server_info" => $server,
            "ip" => Http::getClientIP(),
            "version" => Config::get('version'),
            "reg_count" => Check::getIpRegCount(Http::getClientIP()),
        ];
        Logger::debug(json_encode($res));
        return $this->echoJson($response, $res);
    }

    public function tos()
    {
        return $this->view()->display('tos.tpl');
    }

    public function clients()
    {
        return $this->view()->display('clients.tpl');
    }

    public function postDebug(Request $request,Response $response, $args)
    {
        $res = [
            "body" => $request->getBody(),
            "pa" => $request->getParsedBody(),
            "params" => $request->getParams(),
            "name" => $request->getParam('name'),
        ];
        return $this->echoJson($response, $res);
    }
}