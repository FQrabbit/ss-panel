{include file='admin/main.tpl'}

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
        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="id" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <input name="email" type="email" placeholder="用户邮箱" class="form-control">
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
                <select name="type" class="form-control">
                    <option value="">套餐</option>
                    <option value="试玩">试玩</option>
                    <option value="基础">基础</option>
                    <option value="包月">包月</option>
                    <option value="包季">包季</option>
                    <option value="包年">包年</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        {$users->render()}
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>邮箱</th>
                                <th>端口</th>
                                <th>plan</th>
                                <!-- <th>状态</th> -->
                                <!-- <th>加密方式</th> -->
                                <th>已用流量/总流量</th>
                                <!-- <th>最后在线时间</th> -->
                                <th>最后签到时间</th>
                                <th>本月签到次数</th>
                                <th>注册时间</th>
                                <th>注册IP</th>
                                <!-- <th>邀请者</th> -->
                                <th>操作</th>
                            </tr>
                            {foreach $users as $user}
                            <tr>
                                <td>#{$user->id}</td>
                                <td>{$user->email}</td>
                                <td>{$user->port}</td>
                                <td>{$user->plan}</td>
                                <!-- <td>{$user->enable}</td> -->
                                <!-- <td>{$user->method}</td> -->
                                <td>{$user->usedTraffic()}/{$user->enableTraffic()}</td>
                                <!-- <td>{$user->lastSsTime()}</td> -->
                                <td>{$user->lastCheckInTime()}</td>
                                <td>{$user->CheckInTimes()}</td>
                                <td>{$user->reg_date}</td>
                                <td>{$user->reg_ip}</td>
                                <!-- <td>{$user->ref_by}</td> -->
                                <td>
                                    <a class="btn btn-info btn-sm" href="/admin/user/{$user->id}/edit">编辑</a>
                                    <a class="btn btn-danger btn-sm" id="delete" value="{$user->id}" href="/admin/user/{$user->id}/delete">删除</a>
                                </td>
                            </tr>
                            {/foreach}
                        </table>
                        {$users->render()}
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>
    $(document).ready(function(){
        function delete(){
            $.ajax({
                type:"DELETE",
                url:"/admin/user/",
                dataType:"json",
                data:{
                    name: $("#name").val()
                },
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
        $("html").keydown(function(event){
            if(event.keyCode==13){
                login();
            }
        });
        $("#delete").click(function(){
            delete();
        });
        $("#ok-close").click(function(){
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function(){
            $("#msg-error").hide(100);
        });
    })
</script>

{include file='admin/footer.tpl'}