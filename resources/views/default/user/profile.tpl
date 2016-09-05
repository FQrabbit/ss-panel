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
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
            </div>
        </div>

        <!-- first row -->
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
                        </dl>

                    </div>
                    <!-- /.box -->
                </div>
            </div>

            <div class="col-md-6">
            <!-- right column -->
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
                                        <input type="text" id="sspwd" placeholder="输入新连接密码" class="form-control" required="required">
                                        <div class="input-group-btn">
                                            <button type="submit" id="ss-pwd-update" class="btn btn-default btn-flat">修改</button>
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
                                            <button type="submit" id="portreset" class="btn btn-default btn-flat">重置端口</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">加密方式</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <select id="method" class="form-control">
                                            <option value="aes-256-ctr">AES-256-CTR</option>
                                            <option value="camellia-128-cfb">CAMELLIA-128-CFB</option>
                                            <option value="camellia-192-cfb">CAMELLIA-192-CFB</option>
                                            <option value="camellia-256-cfb">CAMELLIA-256-CFB</option>
                                            <option value="bf-cfb">BF-CFB</option>
                                            <option value="cast5-cfb">CAST5-CFB</option>
                                            <option value="des-cfb">DES-CFB</option>
                                            <option value="des-cfb">DES-EDE3-CFB</option>
                                            <option value="idea-cfb">IDEA-CFB</option>
                                            <option value="rc2-cfb">RC2-CFB</option>
                                            <option value="seed-cfb">SEED-CFB</option>
                                            <option value="salsa20">SALSA20</option>
                                            <option value="chacha20">CHACHA20</option>
                                            <option value="chacha20-ietf">CHACHA20-IETF</option>
                                        </select>
                                        <div class="input-group-btn">
                                            <button type="submit" id="updateMethod" class="btn btn-default btn-flat">修改</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">协议</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <select class="form-control" id="protocol">
                                            <option value="auth_sha1_compatible">auth_sha1_compatible</option>
                                            <option value="auth_sha1_v3_compatible">auth_sha1_v3_compatible</option>
                                        </select>
                                        <div class="input-group-btn">
                                            <button type="submit" id="updateProtocol" class="btn btn-default btn-flat">修改</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">混淆</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <select class="form-control" id="obfs">
                                            <option value="http_simple_compatible">http_simple_compatible</option>
                                            <option value="tls1.2_ticket_auth_compatible">tls1.2_ticket_auth_compatible</option>
                                        </select>
                                        <div class="input-group-btn">
                                            <button type="submit" id="updateObfs" class="btn btn-default btn-flat">修改</button>
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

        <!-- second row -->
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

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="当前密码(必填)" required="required" id="oldpwd">
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
                <!-- /.box -->
            </div>
            <!-- /.col (left) -->

            <!-- right column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录邮箱修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新邮箱</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" placeholder="新邮箱(必填)" required="required" id="email">
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
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html(data.msg);
                    } else {
                        $("#msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#msg-error-p").html(data.msg);
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
                        $("#msg-success").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#msg-success-p").html(data.msg);
                    } else {
                        $("#msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#msg-error-p").html(data.msg);
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
                $("#msg-error").show(500, function(){
                    $(this).delay(3000).hide(500);
                });
                $("#msg-error-p").html("请先填写邮箱!");
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
                        $("#msg-success").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#msg-success-p").html(data.msg);
                        timer = setInterval(function () {
                            --countdown;
                            if (countdown) {
                                $btn.text('重新发送 (' + countdown + '秒)');
                            } else {
                                clearTimer();
                            }
                        }, 1000);
                    } else {
                        $("#msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
                        $("#msg-error-p").html(data.msg);
                        clearTimer();
                    }
                },
                error: function (jqXHR) {
                    $("#msg-error").show(500, function(){
                        $(this).delay(3000).hide(500);
                    });
                    $("#msg-error-p").html("发生错误：" + jqXHR.status);
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
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html(data.msg);
                    } else {
                        $("#msg-error").show(500, function(){
                            $(this).delay(3000).hide(500);
                        });
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
        $("#portreset").click(function () {
            $.ajax({
                type: "POST",
                url: "resetport",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html(data.msg);
                    } else {
                        $("#msg-error").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#updateProtocol").click(function () {
            $.ajax({
                type: "POST",
                url: "protocol",
                dataType: "json",
                data: {
                    protocol: $("#protocol").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html("有的节点不支持自定义协议，请进入节点详情页查看。");
                    } else {
                        $("#msg-error").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#updateObfs").click(function () {
            $.ajax({
                type: "POST",
                url: "obfs",
                dataType: "json",
                data: {
                    obfs: $("#obfs").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html("有的节点不支持自定义混淆插件，请进入节点详情页查看。");
                    } else {
                        $("#msg-error").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#updateMethod").click(function () {
            $.ajax({
                type: "POST",
                url: "method",
                dataType: "json",
                data: {
                    method: $("#method").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-success").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
                        $("#msg-success-p").html("有的节点不支持自定义加密，请进入节点详情页查看。");
                    } else {
                        $("#msg-error").show(500, function(){
                            window.setTimeout("location.reload()",5000);
                        });
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
{include file='user/footer.tpl'}