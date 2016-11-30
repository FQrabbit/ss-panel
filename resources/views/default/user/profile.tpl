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

        <!-- first row -->
        <div class="row">

            <!-- left column -->
            <div class="col-md-6">
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
                                <dd><span class="badge w3-teal">{$user->expire_date}</span></dd>
                            {/if}
                            <br>
                            <dt>端口</dt>
                            <dd>{$user->port}</dd>
                            <dt>密码</dt>
                            <dd>{$user->passwd}</dd>
                            <dt>自定义加密</dt>
                            <dd>{$user->method}</dd>
                            <dt>自定义协议</dt>
                            <dd>{$user->protocol}</dd>
                            <dt>自定义混淆</dt>
                            <dd>{$user->obfs}</dd>
                            <dt>自定义混淆参数</dt>
                            <dd>{$user->obfs_param}</dd>
                        </dl>

                    </div>
                    <!-- /.box -->
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-link"></i>

                        <h3 class="box-title">Shadowsocks连接信息修改</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="config-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="config-msg-error-p"></p>
                                </div>
                                <div id="config-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="config-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前端口</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="ssport" placeholder="{$user->port}" class="form-control" disabled>
                                        <div class="input-group-btn">
                                            <button type="submit" id="portreset" class="btn btn-default btn-flat">重置端口</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">连接密码</label>
                                <div class="col-sm-9">
                                    <input type="text" id="sspwd" value="{$user->passwd}" class="form-control" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">加密方式</label>
                                <div class="col-sm-9">
                                    <select id="method" class="form-control">
                                        <option value="{$user->method}" style="background-color:#009688;" selected="selected">{$user->method} (当前)</option>
                                        <option value="{$user->method}" disabled="disabled">======</option>
                                        <option value="aes-256-cfb">aes-256-cfb</option>
                                        <option value="aes-256-ctr">aes-256-ctr</option>
                                        <option value="camellia-256-cfb">camellia-256-cfb</option>
                                        <option value="salsa20">salsa20</option>
                                        <option value="chacha20">chacha20</option>
                                        <option value="chacha20-ietf">chacha20-ietf</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">协议</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="protocol">
                                        <option value="{$user->protocol}" style="background-color:#009688;" selected="selected">{$user->protocol} (当前)</option>
                                        <option value="{$user->protocol}" disabled="disabled">======</option>
                                        <option value="verify_deflate">verify_deflate</option>
                                        <option value="verify_sha1">verify_sha1</option>
                                        <option value="auth_sha1_v2">auth_sha1_v2</option>
                                        <option value="auth_sha1_v4">auth_sha1_v4</option>
                                        <option value="auth_aes128_md5">auth_aes128_md5</option>
                                        <option value="auth_aes128_sha1">auth_aes128_sha1</option>
                                        <option value="{$user->protocol}" disabled="disabled">==以下兼容原协议==</option>
                                        <option value="verify_sha1_compatible">verify_sha1_compatible</option>
                                        <option value="auth_sha1_v2_compatible">auth_sha1_v2_compatible</option>
                                        <option value="auth_sha1_v4_compatible">auth_sha1_v4_compatible</option>
                                        <option value="auth_aes128_md5_compatible">auth_aes128_md5_compatible (推荐)</option>
                                        <option value="auth_aes128_sha1_compatible">auth_aes128_sha1_compatible (推荐)</option>
                                        <!-- <option value="verify_deflate">verify_deflate</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">混淆</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="obfs">
                                        <option value="{$user->obfs}" style="background-color:#009688;" selected="selected">{$user->obfs} (当前)</option>

                                        <option value="{$user->obfs}" disabled="disabled">======</option>
                                        <option value="http_simple">http_simple</option>
                                        <option value="http_post">http_post</option>
                                        <option value="tls1.2_ticket_auth">tls1.2_ticket_auth</option>
                                        <option value="tls1.2_ticket_auth_compatible" disabled="disabled">==以下兼容原协议==</option>
                                        <option value="http_simple_compatible">http_simple_compatible</option>
                                        <option value="http_post_compatible">http_post_compatible</option>
                                        <option value="tls1.2_ticket_auth_compatible">tls1.2_ticket_auth_compatible</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">混淆参数</label>
                                <div class="col-sm-9">
                                    <input id="obfs_param" class="form-control" type="text" value="{$user->obfs_param}" placeholder="输入混淆参数，如'cloudflare.com'，请勿乱填。">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="config-update" class="btn btn-default btn-flat">修改</button>
                    </div>
                </div>
            </div>
            <!-- /.col (left) -->

            <div class="col-md-6">
            <!-- right column -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录密码修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="psw-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="psw-msg-error-p"></p>
                                </div>
                                <div id="psw-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="psw-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="当前密码" required="required" id="oldpwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="新密码" required="required" id="pwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">确认密码</label>

                                <div class="col-sm-9">
                                    <input type="password" placeholder="确认密码" class="form-control" required="required" id="repwd">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="pwd-update" class="btn btn-default btn-flat">修改</button>
                    </div>

                </div>
                
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录邮箱修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="email-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="email-msg-error-p"></p>
                                </div>
                                <div id="email-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="email-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新邮箱</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" placeholder="新邮箱" required="required" id="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">邮箱验证码</label>

                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="verifycode" class="form-control" placeholder="邮箱验证码"/>
                                        <span class="input-group-btn">
                                            <button type="button" id="sendcode" class="btn btn-default btn-flat">发送验证码</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="email-update" class="btn btn-default btn-flat">修改</button>
                    </div>

                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->

        </div>
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
    $("#msg-success").hide();
    $("#msg-error").hide();
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
                        $("#psw-msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#psw-msg-success-p").html(data.msg);
                    } else {
                        $("#psw-msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#psw-msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#email-update").click(function () {
            $.ajax({
                type: "POST",
                url: "email",
                dataType: "json",
                data: {
                    email: $("#email").val(),
                    verifycode: $("#verifycode").val(),
                    reemail: $("#reemail").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#email-msg-success").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#email-msg-success-p").html(data.msg);
                    } else {
                        $("#email-msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#email-msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#sendcode").on("click", function () {
            var count = sessionStorage.getItem('email-code-count') || 0;
            var timer, countdown = 60, $btn = $(this);
            if (count > 3 || timer) return false;

            if (!email) {
                $("#email-msg-error").show(500, function(){
                    $(this).delay(3000).hide(500);
                });
                $("#email-msg-error-p").html("请先填写邮箱!");
                return $("#email").focus();
            }

            $.ajax({
                type: "POST",
                url: "sendcode",
                dataType: "json",
                data: {
                    email: $("#email").val(),
                },
                success: function (data) {
                    if (data.ret == 1) {
                        $("#email-msg-success").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#email-msg-success-p").html(data.msg);
                        timer = setInterval(function () {
                            --countdown;
                            if (countdown) {
                                $btn.text('重新发送 (' + countdown + '秒)');
                            } else {
                                clearTimer();
                            }
                        }, 1000);
                    } else {
                        $("#email-msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#email-msg-error-p").html(data.msg);
                        clearTimer();
                    }
                },
                error: function (jqXHR) {
                    $("#email-msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#email-msg-error-p").html("发生错误：" + jqXHR.status);
                    clearTimer();
                }
            });
            $btn.addClass("disabled").prop("disabled", true).text('发送中...');
            $("#verifycode").select();
            function clearTimer() {
                $btn.text('重新发送').removeClass("disabled").prop("disabled", false);
                clearInterval(timer);
                timer = null;
            }
        });
    })
</script>

<script>
    $(document).ready(function () {
        $("#config-update").click(function () {
            $.ajax({
                type: "POST",
                url: "ssconfig",
                dataType: "json",
                data: {
                    sspwd: $("#sspwd").val(),
                    method: $("#method").val(),
                    protocol: $("#protocol").val(),
                    obfs: $("#obfs").val(),
                    obfs_param: $("#obfs_param").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#config-msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#config-msg-success-p").html(data.msg);
                    } else {
                        $("#config-msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#config-msg-error-p").html(data.msg);
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
                        $("#config-msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#config-msg-success-p").html(data.msg);
                    } else {
                        $("#config-msg-error").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#config-msg-error-p").html(data.msg);
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