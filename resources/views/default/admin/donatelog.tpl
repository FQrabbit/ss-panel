{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            捐助记录
            <small>Donate Log</small>
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

        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="uid" type="number" placeholder="输入用户id" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>

        <hr>
        
        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <input id="uid" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <input id="port" type="number" placeholder="用户端口" class="form-control">
            </div>
            <div class="form-group">
                <input id="money" type="number" placeholder="捐助金额" class="form-control">
            </div>
            <div class="form-group">
                <button id="insert" class="btn btn-default form-control">插入</button>
            </div>
        </fieldset>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        {$logs->render()}
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>用户ID</th>
                                <th>用户名</th>
                                <th>用户端口</th>
                                <th>金额</th>
                                <th>捐助日期</th>
                                <th>交易号</th>
                                <th>操作</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td><a href="/admin/user/{$log->user()->id}/edit">#{$log->id}</a></td>
                                    <td><a href="/admin/donatelog?uid={$log->user()->id}">{$log->uid}</a></td>
                                    <td>{$log->user()->user_name}</td>
                                    <td>{$log->user()->port}</td>
                                    <td>{$log->money}</td>
                                    <td>{$log->datetime}</td>
                                    <td>{$log->trade_no}</td>
                                    <td>
                                        <a class="btn btn-default btn-sm" id="delete" href="javascript:void(0);" onclick="confirm_delete({$log->id});">删除</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                        {$logs->render()}
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script>
    function insert() {
        $.ajax({
            type: "POST",
            url: "/admin/adddonate",
            dataType: "json",
            data: {
                uid: $("#uid").val(),
                port: $("#port").val(),
                money: $("#money").val()
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

    function deleterecord(item) {
        $.ajax({
            type: "DELETE",
            url: "/admin/donatelog/" + item,
            dataType:"json",
            success: function (data) {
                if (data.ret) {
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    // window.setTimeout("location.reload()", 2000);
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
    };

    function confirm_delete(item) {
        $.confirm({
            title: '确认操作',
            content: '你确定要删除这条记录?',
            confirm: function(){
                deleterecord(item);
            },
            confirmButton: '是',
            cancelButton: '否',
            theme: 'black'
        });
    }


    $("#insert").click(function () {
        $(this).attr("disabled", "disabled");
        insert();
    });
    $("#ok-close").click(function () {
        $("#msg-success").hide(100);
    });
    $("#error-close").click(function () {
        $("#msg-error").hide(100);
    });
</script>