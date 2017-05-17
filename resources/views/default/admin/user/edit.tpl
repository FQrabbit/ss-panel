{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户编辑 #{$user->id} {$user->user_name}
            <small>Edit User</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="ok-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
                <div id="msg-error" class="alert alert-danger alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="error-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="form-horizontal">
                            <div class="row">
                                <fieldset class="col-sm-12">
                                    <legend>帐号信息</legend>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">邮箱</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="email" type="email" value="{$user->email}">
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">密码</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="pass" value="" placeholder="不修改时留空">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">是否管理员</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="is_admin">
                                                <option value="0" {if $user->is_admin==0}selected="selected"{/if}>
                                                    否
                                                </option>
                                                <option value="1" {if $user->is_admin==1}selected="selected"{/if}>
                                                    是
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">用户状态</label>

                                        <div class="col-sm-9"><select class="form-control" id="enable">
                                                <option value="1" {if $user->enable==1}selected="selected"{/if}>
                                                    正常
                                                </option>
                                                <option value="0" {if $user->enable==0}selected="selected"{/if}>
                                                    禁用
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">滥用</label>

                                        <div class="col-sm-9"><select class="form-control" id="status">
                                                <option value="1" {if $user->status==1}selected="selected"{/if}>
                                                    否
                                                </option>
                                                <option value="0" {if $user->status==0}selected="selected"{/if}>
                                                    是
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12"><hr></div>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">是否捐助</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="ref_by">
                                                <option value="0" {if $user->ref_by!=3}selected="selected"{/if}>
                                                    否
                                                </option>
                                                <option value="3" {if $user->ref_by==3}selected="selected"{/if}>
                                                    是
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">捐助金额</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="money" value="{$user->money}" type="number">
                                        </div>
                                    </div>
                                    <div class="col-sm-12"><hr></div>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">付费金额</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="user_type" value="{$user->user_type}" type="number">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">套餐</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="product">
                                                <option value='0' {if $user->product_id==0}selected="selected"{/if}>
                                                    无
                                                </option>
                                            {foreach $products as $product}
                                                <option value='{$product->id}' {if $user->product_id && $user->product->name=={$product->name}}selected="selected"{/if}>
                                                    {$product->name}
                                                </option>
                                            {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">plan</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="plan">
                                                <option value="A" {if $user->plan=="A"}selected="selected"{/if}>
                                                    A
                                                </option>
                                                <option value="B" {if $user->plan=="B"}selected="selected"{/if}>
                                                    B
                                                </option>
                                                <option value="C" {if $user->plan=="C"}selected="selected"{/if}>
                                                    C
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">过期时间</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input class="form-control" id="expire_date" value="{$user->getFormatedDateTime($user->expire_date)}" type="text" onfocus="(this.type='datetime-local')">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-flat timeReseter">此时</button>
                                                    <button type="button" class="btn btn-default btn-flat tozero">归零</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">购买时间</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input class="form-control date" id="buy_date" value="{$user->getFormatedDateTime($user->buy_date)}" type="text" onfocus="(this.type='datetime-local')">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-flat timeReseter">此时</button>
                                                    <button type="button" class="btn btn-default btn-flat tozero">归零</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="col-sm-12">
                                    <legend>ShadowSocks连接信息</legend>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">连接端口</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="port" type="number" value="{$user->port}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">连接密码</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="passwd" value="{$user->passwd}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">自定义加密</label>

                                        <div class="col-sm-9">
                                            <select id="method" class="form-control">
                                                <option value="{$user->method}" style="background-color:#009688;" selected="selected">{$user->method} (当前)</option>
                                                <option value="{$user->method}" disabled="disabled">======</option>
                                                <option value="aes-256-cfb">aes-256-cfb</option>
                                                <option value="aes-256-cfb">aes-256-cfb</option>
                                                <option value="camellia-256-cfb">camellia-256-cfb</option>
                                                <option value="salsa20">salsa20</option>
                                                <option value="chacha20">chacha20</option>
                                                <option value="chacha20-ietf">chacha20-ietf</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
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
                                                <option value="auth_sha1_v4_compatible">auth_sha1_v4_compatible</option>
                                                <!-- <option value="verify_deflate">verify_deflate</option> -->
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">协议参数</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="protocol_param" class="form-control" value="{$user->protocol_param}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
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

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">混淆参数</label>

                                        <div class="col-sm-9">
                                            <input type="text" id="obfs_param" class="form-control" value="{$user->obfs_param}">
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="row">
                                <fieldset class="col-sm-12">
                                    <legend>流量</legend>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">总流量</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input class="form-control" id="transfer_enable" type="number"
                                                       value="{$user->enableTrafficInGB()}">

                                                <div class="input-group-addon">GiB</div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">已用流量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="traffic_usage" type="text"
                                                   value="{$user->usedTraffic()}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="col-sm-12">
                                    <legend>邀请</legend>
                                    <!-- <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">可用邀请数量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="invite_num" type="number"
                                                   value="{$user->invite_num}">
                                        </div>
                                    </div> -->

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">邀请人ID</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ref_by1" type="number"
                                                   value="{$user->ref_by}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" id="submit" name="action" value="add" class="btn btn-primary">修改</button>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script>
    $(document).ready(function () {
        function submit() {
            $.ajax({
                type: "PUT",
                url: "/admin/user/{$user->id}",
                dataType: "json",
                data: {
                    email: $("#email").val(),
                    pass: $("#pass").val(),
                    port: $("#port").val(),
                    passwd: $("#passwd").val(),
                    transfer_enable: $("#transfer_enable").val(),
                    // invite_num: $("#invite_num").val(),
                    method: $("#method").val(),
                    obfs: $("#obfs").val(),
                    obfs_param: $("#obfs_param").val(),
                    protocol: $("#protocol").val(),
                    protocol_param: $("#protocol_param").val(),
                    enable: $("#enable").val(),
                    status: $("#status").val(),
                    is_admin: $("#is_admin").val(),
                    money: $("#money").val(),
                    user_type: $("#user_type").val(),
                    product_id: $("#product").val(),
                    plan: $("#plan").val(),
                    ref_by: $("#ref_by").val(),
                    expire_date: $("#expire_date").val(),
                    buy_date: $("#buy_date").val(),
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("location.reload()", 2000);
                    } else {
                        $("#msg-error").hide(10);
                        $("#msg-error").show(100);
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html("发生错误：" + jqXHR.status);
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                submit();
            }
        });
        $("#submit").click(function () {
            submit();
        });
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function () {
            $("#msg-error").hide(100);
        });

        $(".tozero").click(function(){
            $(this).parent().parent().children("input").val("00-00-00T00:00:00");
        });
        $(".timeReseter").click(function(){
            var time,Y,M,D,H,M,S,nowdate;
            time = new Date();
            Y = time.getFullYear();
            M = time.getMonth() + 1;
            D = time.getDate();
            H = time.getHours();
            i = time.getMinutes();
            S = time.getSeconds();
            nowdate = Y+"-"+M+"-"+D+"T"+H+":"+i+":"+S;
            $(this).parent().parent().children("input").val(nowdate);
        });
    })
    // $('input.date').datetimepicker({
    //     format: "yyyy-mm-dd hh:ii:ss",
    //     todayBtn: true,
    //     clearBtn: true,
    //     todayHighlight: true
    // });
</script>