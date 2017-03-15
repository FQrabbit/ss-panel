<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\User;
use App\Utils\Hash;
use App\Utils\Tools;

class UserController extends AdminController
{
    public function index($request, $response, $args)
    {
        $q = $request->getQueryParams();
        if (!isset($request->getQueryParams()['page'])) {
            $q['page'] = 1;
        }
        $path = '/admin/user?';
        $users = User::where('id', ">" , 0);
        foreach ($q as $k => $v) {
            if ($v != "" && $k != 'page') {
                $users = $users->where($k, $v);
                $path .= $k.'='.$v.'&';
            }
        }
        $path = substr($path,0,strlen($path)-1);
        $users = $users->paginate(15, ['*'], 'page', $q['page']);
        $users->setPath($path);
        return $this->view()->assign('users', $users)->assign('q', $q)->display('admin/user/index.tpl');
    }

    public function edit($request, $response, $args)
    {
        if (isset($args['port'])) {
            $port = $args['port'];
            $user = User::where('port', $port)->first();
        }else{
            $id = $args['id'];
            $user = User::find($id);
        }
        if ($user == null) {
            return "空";
        }
        return $this->view()->assign('user', $user)->display('admin/user/edit.tpl');
    }

    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     */
    public function update($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);

        $user->email = $request->getParam('email');
        if ($request->getParam('pass') != '') {
            $user->pass = Hash::passwordHash($request->getParam('pass'));
        }
        if ($request->getParam('passwd') != '') {
            $user->passwd = $request->getParam('passwd');
        }
        $user->port = $request->getParam('port');
        $user->transfer_enable = Tools::toGB($request->getParam('transfer_enable'));
        // $user->invite_num = $request->getParam('invite_num');
        $user->method = $request->getParam('method');
        $user->protocol = $request->getParam('protocol');
        $user->protocol_param = $request->getParam('protocol_param');
        $user->obfs = $request->getParam('obfs');
        $user->obfs_param = $request->getParam('obfs_param');
        $user->enable = $request->getParam('enable');
        $user->status = $request->getParam('status');
        $user->is_admin = $request->getParam('is_admin');
        $user->money = $request->getParam('money');
        $user->user_type = $request->getParam('user_type');
        $user->type = $request->getParam('type');
        $user->plan = $request->getParam('plan');
        $user->ref_by = $request->getParam('ref_by');
        $user->expire_date = $request->getParam('expire_date');
        $user->buy_date = $request->getParam('buy_date');
        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "修改失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "修改成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function delete($request, $response, $args)
    {
        $id = $args['id'];
        AdminController::clearUserLogs($id);
        $user = User::find($id);
        if (!$user->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] = "删除失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "删除成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteGet($request, $response, $args)
    {
        $id = $args['id'];
        $user = User::find($id);
        $user->delete();
        $newResponse = $response->withStatus(302)->withHeader('Location', '/admin/user');
        return $newResponse;
    }
}