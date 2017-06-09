{include file='admin/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            邮件中心
            <small>Mail</small>
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
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">单封邮件</h3>
                    </div>

                    <div class="box-body">
                        <form role="form">
                            <div class="form-group">
                                <label>标题</label>
                                <input type="text" class="form-control" placeholder="Enter ..." id="title"
                                value="Shadowsky">
                            </div>

                            <div class="form-group">
                                <label>内容</label>
                                <textarea class="form-control" id="content" rows="8"
                                placeholder="Enter ..."></textarea>
                            </div>

                            <div class="form-group">
                                <input id="id" type="number" placeholder="用户id" class="form-control">
                            </div>

                            <div class="form-group">
                                <input id="email" type="email" placeholder="用户邮箱" class="form-control">
                            </div>

                            <div class="form-group">
                                <input id="port" type="number" placeholder="用户端口" class="form-control">
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="send_email" type="submit" class="btn btn-default flat">发送</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">批量邮件</h3>
                    </div>

                    <div class="box-body">
                        <form role="form">
                            <div class="form-group">
                                <label>标题</label>
                                <input type="text" class="form-control" placeholder="Enter ..." id="emails_title"
                                value="Shadowsky">
                            </div>

                            <div class="form-group">
                                <label>内容</label>
                                <textarea class="form-control" id="emails_content" rows="8"
                                placeholder="Enter ..."></textarea>
                            </div>

                            <div class="form-group">
                                <select id="plan" class="form-control">
                                    <option value="">用户类型</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <select id="enable" class="form-control">
                                    <option value="">用户状态</option>
                                    <option value="1">可用</option>
                                    <option value="0">禁用</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <select id="status" class="form-control">
                                    <option value="">用户行为</option>
                                    <option value="1">正常</option>
                                    <option value="0">滥用</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <select id="type" class="form-control">
                                    <option value="">套餐</option>
                                    <option value="试玩">试玩</option>
                                    <option value="基础">基础</option>
                                    <option value="包月">包月</option>
                                    <option value="包季">包季</option>
                                    <option value="包年">包年</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="send_emails" type="submit" class="btn btn-default flat">发送</button>
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}
<script type="text/javascript">
    $('textarea').summernote();
</script>
<script type="text/javascript">
    $("#send_email").click(function () {
        $.ajax({
            type: "POST",
            url: "/admin/sendemail",
            dataType: "json",
            data: {
                id: $("#id").val(),
                email: $("#email").val(),
                port: $("#port").val(),
                title: $("#title").val(),
                content: $("#content").val(),
            },
            success: function (data) {
                if (data.ret) {
                    $("#msg-success,#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                } else {
                    $("#msg-success,#msg-error").hide(100);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + jqXHR.status);
            }
        })
    })

    $("#send_emails").click(function () {
        $.ajax({
            type: "POST",
            url: "/admin/sendemails",
            dataType: "json",
            data: {
                plan: $("#plan").val(),
                enable: $("#enable").val(),
                status: $("#status").val(),
                type: $("#type").val(),
                title: $("#emails_title").val(),
                content: $("#emails_content").val(),
            },
            success: function (data) {
                if (data.ret) {
                    $("#msg-success,#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                } else {
                    $("#msg-success,#msg-error").hide(100);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error: function (jqXHR) {
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + jqXHR.status);
            }
        })
    })

    $(document).ready(function(){
        $("#ok-close").click(function(){
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function(){
            $("#msg-error").hide(100);
        });
    })
</script>