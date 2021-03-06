{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            站点配置
            <small>App Config</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="ok-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> 成功!</h4>

                    <p id="msg-success-p"></p>
                </div>

            </div>
        </div>
        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">修改配置</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <form role="form">
                            <div class="form-group">
                                <label>网站名</label>
                                <input type="text" class="form-control" placeholder="Enter ..." id="app-name"
                                       value="{$conf['app-name']}">
                            </div>

                            <div class="form-group">
                                <label>统计代码</label>
                                <textarea class="form-control" id="analytics-code" rows="3"
                                          placeholder="Enter ...">{$conf['analytics-code']}</textarea>
                            </div>

                            <div class="form-group">
                                <label>购买页公告</label>
                                <textarea class="form-control" id="home-purchase" rows="3"
                                          placeholder="Enter ...">{$conf['home-purchase']}</textarea>
                            </div>

                            <div class="form-group">
                                <label>邀请页公告</label>
                                <textarea class="form-control" id="home-code" rows="8"
                                          placeholder="Enter ...">{$conf['home-code']}</textarea>
                            </div>

                            <div class="form-group">
                                <label>用户中心公告</label>
                                <textarea class="form-control" id="user-index" rows="8"
                                          placeholder="Enter ...">{$conf['user-index']}</textarea>
                            </div>

                            <div class="form-group">
                                <label>用户节点公告</label>
                                <textarea class="form-control" id="user-node" rows="10"
                                          placeholder="Enter ...">{$conf['user-node']}</textarea>
                            </div>

                            <div class="form-group">
                                <label>用户购买页面公告</label>
                                <textarea class="form-control" id="user-purchase" rows="8"
                                          placeholder="Enter ...">{$conf['user-purchase']}</textarea>
                            </div>

                        </form>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button id="update" type="submit" name="update" value="update" class="btn btn-default flat">更新配置
                        </button>
                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">用户中心Modal公告</h3>
                    </div>

                    <div class="box-body">
                        <form role="form">
                            <div class="form-group">
                                <label>标题</label>
                                <input type="text" class="form-control" placeholder="Enter ..." id="ann_title"
                                       value="{$ann->title}">
                            </div>

                            <div class="form-group">
                                <label>内容</label>
                                <textarea class="form-control" id="ann_content" rows="8"
                                          placeholder="Enter ...">{$ann->content}</textarea>
                            </div>
                        </form>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button id="update_ann" type="submit" class="btn btn-default flat">更新公告</button>
                        <button id="add_ann" type="submit" class="btn btn-default flat">添加公告</button>
                        <button id="send_ann" type="submit" class="btn btn-default flat">发送</button>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script type="text/javascript">
    $('textarea').summernote();
</script>

<script>
    $(document).ready(function () {
        $("#update").click(function () {
            $.ajax({
                type: "PUT",
                url: "/admin/config",
                dataType: "json",
                data: {
                    analyticsCode: $("#analytics-code").val(),
                    homeCode: $("#home-code").val(),
                    homePurchase: $("#home-purchase").val(),
                    appName: $("#app-name").val(),
                    userIndex: $("#user-index").val(),
                    userNode: $("#user-node").val(),
                    userPurchase: $("#user-purchase").val(),
                    ann_title: $("#ann_title").val(),
                    ann_content: $("#ann_content").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("location.reload()", 2000);
                    }
                },
                error: function (jqXHR) {
                    alert("发生错误：" + jqXHR.status);
                }
            })
        })

        $("#update_ann").click(function () {
            $.ajax({
                type: "PUT",
                url: "/admin/announcement",
                dataType: "json",
                data: {
                    ann_title: $("#ann_title").val(),
                    ann_content: $("#ann_content").val()
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

        $("#send_ann").click(function () {
            $.ajax({
                type: "POST",
                url: "/admin/sendannounemail",
                dataType: "json",
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
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
            })
        })
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });

        $("#add_ann").click(function () {
            $.ajax({
                type: "POST",
                url: "announcement/create",
                dataType: "json",
                data: {
                    ann_title: $("#ann_title").val(),
                    ann_content: $("#ann_content").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
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
            })
        })
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });
    })
</script>