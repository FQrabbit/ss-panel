<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\Node;
use App\Models\Vote;

class NodeController extends AdminController
{
    public function index($request, $response, $args)
    {
        $nodes = Node::orderBy("sort")->get();
        return $this->view()->assign('nodes', $nodes)->display('admin/node/index.tpl');
    }

    public function create($request, $response, $args)
    {
        $methods   = Node::getAllMethod();
        $obfses    = Node::getAllObfs();
        $protocols = Node::getAllProtocol();
        return $this->view()->assign('methods', $methods)->assign('obfses', $obfses)->assign('protocols', $protocols)->display('admin/node/create.tpl');
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

        }
        $methods   = Node::getAllMethod();
        $obfses    = Node::getAllObfs();
        $protocols = Node::getAllProtocol();
        return $this->view()->assign('node', $node)->assign('methods', $methods)->assign('obfses', $obfses)->assign('protocols', $protocols)->display('admin/node/edit.tpl');
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
        $id        = $args['id'];
        $node      = Node::find($id);
        $polls     = Vote::where('nodeid', $id)->get();
        $rs['msg'] = '';
        if (!$polls->isEmpty()) {
            $polls = Vote::where('nodeid', $id)->delete();
            $rs['msg'] .= "已清空投票。";
        } else {
            $rs['msg'] .= "无投票。";
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
