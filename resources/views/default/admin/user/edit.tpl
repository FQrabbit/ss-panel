{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户编辑 #{$user->id}
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
                <div id="msg-error" class="alert alert-warning alert-dismissable" style="display: none;">
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

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">付费金额</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="user_type" value="{$user->user_type}" type="number">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">套餐</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="type">
                                                <option value=1 {if $user->type==1}selected="selected"{/if}>
                                                    无
                                                </option>
                                                {foreach ["试玩","基础","标准","高级","包月","包季","包年"] as $a}
                                                    <option value={$a} {if $user->type=={$a}}selected="selected"{/if}>
                                                        {$a}
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
                                        <label class="col-sm-3 control-label">邮箱验证状态</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="status">
                                                <option value="1" {if $user->status=="1"}selected="selected"{/if}>
                                                    已验证
                                                </option>
                                                <option value="0" {if $user->status=="0"}selected="selected"{/if}>
                                                    未验证
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">过期时间</label>

                                        <div class="col-sm-9 input-group" style="padding: 0 15px 0 15px !important">
                                            <input class="form-control" id="expire_date" value="{$user->expire_date}">
                                            <div class="input-group-btn">
                                                <button type="button" id="timeReseter" class="btn btn-default btn-flat">此时</button>
                                            </div>
                                            <div class="input-group-btn">
                                                <button type="button" id="tozero" class="btn btn-default btn-flat">归零</button>
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
                                            <input class="form-control" id="method" value="{$user->method}">
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
                                            <input class="form-control" id="invite_num" type="text"
                                                   value="{$user->usedTraffic()}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="col-sm-12">
                                    <legend>邀请</legend>
                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">可用邀请数量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="invite_num" type="number"
                                                   value="{$user->invite_num}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">邀请人ID</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ref_by" type="number"
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
                    invite_num: $("#invite_num").val(),
                    method: $("#method").val(),
                    enable: $("#enable").val(),
                    is_admin: $("#is_admin").val(),
                    money: $("#money").val(),
                    user_type: $("#user_type").val(),
                    type: $("#type").val(),
                    plan: $("#plan").val(),
                    ref_by: $("#ref_by").val(),
                    expire_date: $("#expire_date").val(),
                    status: $("#status").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("location.href='/admin/user/{$user->id}/edit'", 2000);
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

        function resetTime(){
            var time,Y,M,D,H,M,S,nowdate;
            time = new Date();
            Y = time.getFullYear();
            M = time.getMonth() + 1;
            D = time.getDate();
            H = time.getHours();
            i = time.getMinutes();
            S = time.getSeconds();
            nowdate = Y+"-"+M+"-"+D+" "+H+":"+i+":"+S;
            $("#expire_date").val(nowdate);
        }

        function tozero(){
            $("#expire_date").val("00-00-00 00:00:00");
        }

        $("#tozero").click(function(){
            tozero();
        });
        $("#timeReseter").click(function(){
            resetTime();
        });
    })
</script>


{include file='admin/footer.tpl'}
