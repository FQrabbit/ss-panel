{include file='admin/main.tpl'}
<style>
.table-responsive>.table>tbody>tr>td, .table-responsive>.table>tbody>tr>th, .table-responsive>.table>tfoot>tr>td, .table-responsive>.table>tfoot>tr>th, .table-responsive>.table>thead>tr>td, .table-responsive>.table>thead>tr>th {
    white-space: nowrap;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户列表
            <small>User List</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div id="msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
            </div>
        </div>
        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="id" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <input name="user_name" type="text" placeholder="用户名" class="form-control">
            </div>
            <div class="form-group">
                <input name="email" type="text" placeholder="用户邮箱" class="form-control">
            </div>
            <div class="form-group">
                <input name="port" type="number" placeholder="用户端口" class="form-control">
            </div>
            <div class="form-group">
                <select name="plan" class="form-control">
                    <option value="">用户类型</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
            </div>
            <div class="form-group">
                <select name="product_id" class="form-control">
                    <option value="">套餐</option>
                {foreach $products as $product}
                    <option value="{$product->id}">{$product->name}</option>
                {/foreach}
                </select>
            </div>
            <div class="form-group">
                <select name="enable" class="form-control">
                    <option value="">用户状态</option>
                    <option value="1">可用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            {$users->render()}
                            <table class="table table-hover">
                                <tr>
                                    <th>操作</th>
                                    <th>ID</th>
                                    <th>用户名</th>
                                    <th>邮箱</th>
                                    <th>端口</th>
                                    <th>是否捐助</th>
                                    <th>plan</th>
                                    <th>套餐</th>
                                    <th>状态</th>
                                    <!-- <th>加密方式</th> -->
                                    <th>已用流量/总流量</th>
                                    <th>购买时间</th>
                                    <th>到期时间</th>
                                    <th>最后在线时间</th>
                                    <th>最后签到时间</th>
                                    <th>本月签到次数</th>
                                    <th>注册时间</th>
                                    <th>注册IP</th>
                                </tr>
                                {foreach $users as $user}
                                <tr>
                                    <td>
                                        <a class="btn bg-green btn-sm" href="/admin/user/{$user->id}/edit">编辑</a>
                                        <a class="btn btn-danger btn-sm" id="delete" value="{$user->id}" href="javascript:void(0);" onclick="confirm_delete({$user->id});">删除</a>
                                    </td>
                                    <td>#{$user->id}</td>
                                    <td>{$user->user_name}</td>
                                    <td>{$user->email}</td>
                                    <td>{$user->port}</td>
                                    <td>{if $user->isDonator()}&check;{else}X{/if}</td>
                                    <td>{$user->plan}</td>
                                    <td>{if $user->product_id}{$user->product->name}{else}无{/if}</td>
                                    <td>{if $user->enable==1}&check;{else}X{/if}</td>
                                    <!-- <td>{$user->method}</td> -->
                                    <td>{$user->usedTraffic()}/{$user->enableTraffic()}</td>
                                    <td>{$user->buy_date}</td>
                                    <td>{$user->expire_date}</td>
                                    <td>{$user->lastSsTime()}</td>
                                    <td>{$user->lastCheckInTime()}</td>
                                    <td>{$user->CheckInTimes()}</td>
                                    <td>{$user->reg_date}</td>
                                    <td>{$user->reg_ip}</td>
                                </tr>
                                {/foreach}
                            </table>
                            {$users->render()}
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script type="text/javascript"> 
    function deleteuser(uid){
        $.ajax({
            type:"DELETE",
            url:"/admin/user/" + uid,
            dataType:"json",
            success:function(data){
                if(data.ret){
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    window.setTimeout("location.href='/admin/user'", 2000);
                }else{
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error:function(jqXHR){
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误："+jqXHR.status);
            }
        });
    }
    function confirm_delete(id) {
        $.confirm({
            title: '确认操作',
            content: '你确定要删除这个用户?',
            confirm: function(){
                deleteuser(id);
            },
            confirmButton: '是',
            cancelButton: '否',
            theme: 'black'
        });
    }
    $(document).ready(function(){
        $("html").keydown(function(event){
            if(event.keyCode==13){
                $(form).summit();
            }
        });
        $("#ok-close").click(function(){
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function(){
            $("#msg-error").hide(100);
        });
    })
</script>