<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\TrafficLog;
use App\Models\Vote;
use App\Models\VpsMerchant;

class NodeController extends AdminController
{
    public function index($request, $response, $args)
    {
        $nodes = Node::orderBy("sort")->get();
        foreach ($nodes as $node) {
            $nodes_polls['labels'][]   = $node->name;
            $nodes_polls['datas'][0][] = $node->getPollCount(-1);
            $nodes_polls['datas'][1][] = $node->getPollCount(1);
        }
        $nodes_polls = json_encode($nodes_polls);
        return $this->view()->assign('nodes', $nodes)->assign('nodes_polls', $nodes_polls)->display('admin/node/index.tpl');
    }

    public function create($request, $response, $args)
    {
        $methods   = Node::getAllMethod();
        $obfses    = Node::getAllObfs();
        $protocols = Node::getAllProtocol();
        $vpsMerchants = VpsMerchant::all();
        return $this->view()->
            assign('methods', $methods)->
            assign('obfses', $obfses)->
            assign('protocols', $protocols)->
            assign('vpsMerchants', $vpsMerchants)->
            display('admin/node/create.tpl');
    }

    public function add($request, $response, $args)
    {
        $node = new Node();
        $q    = $request->getParsedBody();
        foreach ($q as $k => $v) {
            $node->$k = $v;
        }
        if (!$node->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = "添加失败";
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = "节点添加成功";
        return $response->getBody()->write(json_encode($rs));
    }

    public function edit($request, $response, $args)
    {
        $id   = $args['id'];
        $node = Node::find($id);
        if ($node == null) {
            return;
        }
        $methods      = Node::getAllMethod();
        $obfses       = Node::getAllObfs();
        $protocols    = Node::getAllProtocol();
        $vpsMerchants = VpsMerchant::all();
        return $this->view()->
            assign('node', $node)->
            assign('vpsMerchants', $vpsMerchants)->
            assign('methods', $methods)->
            assign('obfses', $obfses)->
            assign('protocols', $protocols)->
            display('admin/node/edit.tpl');
    }

    public function update($request, $response, $args)
    {
        $id   = $args['id'];
        $node = Node::find($id);
        $q    = $request->getParsedBody();
        foreach ($q as $k => $v) {
            $node->$k = $v;
        }
        if (!$node->save()) {
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
        $id   = $args['id'];
        $node = Node::find($id);

        /**
         * 返回信息
         */
        $rs['msg'] = '';

        /**
         * clean logs related to this node
         */
        if (Vote::where('nodeid', $id)->delete()) {
            $rs['msg'] .= "已清空投票。";
        } else {
            $rs['msg'] .= "可能无投票。";
        }
        if (TrafficLog::where('node_id', $id)->delete()) {
            $rs['msg'] .= "已清空流量记录。";
        } else {
            $rs['msg'] .= "可能无流量记录。";
        }

        if (!$node->delete()) {
            $rs['ret'] = 0;
            $rs['msg'] .= "节点删除失败。";
            return $response->getBody()->write(json_encode($rs));
        }

        $rs['ret'] = 1;
        $rs['msg'] .= "节点删除成功。";
        return $response->getBody()->write(json_encode($rs));
    }

    public function deleteGet($request, $response, $args)
    {
        $id   = $args['id'];
        $node = Node::find($id);
        $node->delete();
        return $this->redirect($response, '/admin/node');
    }
}
