{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            我的信息
            <small>User Profile</small>
        </h1>
    </section>
    <!-- Main content --><!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div id="msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
                <div id="ss-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                    <p id="ss-msg-success-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录密码修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="form-horizontal">

                            <div id="msg-success" class="alert alert-info alert-dismissable" style="display:none">
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-info"></i> Ok!</h4>

                                <p id="msg-success-p"></p>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="当前密码(必填)" id="oldpwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="新密码" id="pwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">确认密码</label>

                                <div class="col-sm-9">
                                    <input type="password" placeholder="确认密码" class="form-control" id="repwd">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="pwd-update" class="btn btn-primary">修改</button>
                    </div>

                </div>
                <!-- /.box -->
            </div>

            <div class="col-md-6">

                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-link"></i>

                        <h3 class="box-title">Shadowsocks连接信息修改</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">连接密码</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="sspwd" placeholder="输入新连接密码" class="form-control">
                                        <div class="input-group-btn">
                                            <button type="submit" id="ss-pwd-update" class="btn btn-primary">修改</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前端口</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="ssport" placeholder="{$user->port}" class="form-control" disabled>
                                        <div class="input-group-btn">
                                            <button type="submit" id="portreset" class="btn btn-primary">重置端口</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
            <!-- /.col (right) -->

        </div>

        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-user"></i>

                        <h3 class="box-title">我的帐号</h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>id</dt>
                            <dd>{$user->id}</dd>
                            <dt>用户名</dt>
                            <dd>{$user->user_name}</dd>
                            <dt>邮箱</dt>
                            <dd>{$user->email}</dd>
                            <!-- <dt>邮箱验证状态</dt>
                            <dd>
                                {if $user->status == 1}
                                    <code>已验证</code>
                                {else}
                                    <code>未验证</code><a id='validate' class='btn btn-success btn-sm' href='#' style='margin-left:10px'>点击发送验证邮件</a>
                                {/if}
                            </dd> -->
                            <dt>用户类型</dt>
                            <dd>
                                {if $user->plan == "A"}
                                    <span class="badge bg-green">免费用户</span>
                                {elseif $user->plan == "C"}
                                    <span class="badge bg-green">特殊用户</span>
                                {else}
                                    <span class="badge bg-green">付费用户</span>
                                {/if}

                                {if $user->ref_by == 3}
                                    <span class="badge bg-green">捐助用户</span>
                                {/if}
                            </dd>
                            {if $user->type != 1}
                            <dt>当前套餐</dt>
                            <dd>
                                <span class="badge bg-green">{$user->type}套餐</span>
                            </dd>
                            {/if}
                            {if $user->expire_date != 0}
                                <dt>到期时间</dt>
                                <dd><span class="badge bg-teal">{$user->expire_date}</span></dd>
                            {/if}
                        </dl>

                    </div>
                    <!-- /.box -->
                </div>
            </div>

        </div>
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    // $(document).ready(function(){
    //      function validate(){
    //             $.ajax({
    //                 type:"GET",
    //                 url:"/_validate.php",
    //                 dataType:"json",
    //                 data:{
    //                     email: "{$user->email}",
    //                     id: "{$user->id}"
    //                 },
    //                 success:function(data){
    //                     if(data.ok){
    //                         $("#msg-error").hide(10);
    //                         $("#msg-success").show(100);
    //                         $("#msg-success-p").html(data.msg);
    //                     }else{
    //                         $("#msg-error").hide(10);
    //                         $("#msg-error").show(100);
    //                         $("#msg-error-p").html(data.msg);
    //                     }
    //                 },
    //                 error:function(jqXHR){
    //                     $("#msg-error").hide(10);
    //                     $("#msg-error").show(100);
    //                     $("#msg-error-p").html("发生错误："+jqXHR.status);
    //                 }
    //             }); 
    //     }
    //     $("#validate").click(function(){
    //         validate();
    //         $(this).hide(500);
    //     });
    //     $("#ok-close").click(function(){
    //         $("#msg-success").hide(100);
    //     });
    //     $("#error-close").click(function(){
    //         $("#msg-error").hide(100);
    //     });
    // })
</script>

<script>
    $("#msg-success").hide();
    $("#msg-error").hide();
    $("#ss-msg-success").hide();
</script>

<script>
    $(document).ready(function () {
        $("#pwd-update").click(function () {
            $.ajax({
                type: "POST",
                url: "password",
                dataType: "json",
                data: {
                    oldpwd: $("#oldpwd").val(),
                    pwd: $("#pwd").val(),
                    repwd: $("#repwd").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide();
                        $("#msg-success").show();
                        $("#msg-success-p").html(data.msg);
                    } else {
                        $("#msg-error").show();
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#ss-pwd-update").click(function () {
            $.ajax({
                type: "POST",
                url: "sspwd",
                dataType: "json",
                data: {
                    sspwd: $("#sspwd").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#ss-msg-success").show();
                        $("#ss-msg-success-p").html(data.msg);
                    } else {
                        $("#ss-msg-error").show();
                        $("#ss-msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#portreset").click(function () {
            $.ajax({
                type: "POST",
                url: "resetport",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#ss-msg-success").show();
                        $("#ss-msg-success-p").html(data.msg);
                    } else {
                        $("#ss-msg-error").show();
                        $("#ss-msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })
    })
</script>
{include file='user/footer.tpl'}