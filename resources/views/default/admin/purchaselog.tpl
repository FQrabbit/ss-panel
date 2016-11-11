{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            购买记录
            <small>Purchase Log</small>
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
                <input name="uid" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <select name="body" class="form-control">
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

        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <input id="uid" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <select id="body" class="form-control">
                    <option value="">套餐</option>
                    <option value="试玩">试玩</option>
                    <option value="基础">基础</option>
                    <option value="包月">包月</option>
                    <option value="包季">包季</option>
                    <option value="包年">包年</option>
                </select>
            </div>
            <div class="form-group">
                <input id="price" type="number" placeholder="价格" class="form-control">
            </div>
            <div class="form-group">
                <input id="buy_date" value="2016-11-11 11:11:11" placeholder="购买时间" class="form-control">
            </div>
            <div class="form-group">
                <input id="trade_no" type="number" placeholder="交易号" class="form-control">
            </div>
            <div class="form-group">
                <button id="insert" class="form-control btn btn-default btn-flat">插入</button>
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
                                <th>用户</th>
                                <th>套餐</th>
                                <th>价格</th>
                                <th>购买日期</th>
                                <th>交易号</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td>#{$log->id}</td>
                                    <td>{$log->uid}</td>
                                    <td>{$log->body}</td>
                                    <td>{$log->price}</td>
                                    <td>{$log->buy_date}</td>
                                    <td>{$log->trade_no}</td>
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

<script>
    $(document).ready(function () {
        function insert() {
            $.ajax({
                type: "POST",
                url: "/admin/addpurchase",
                dataType: "json",
                data: {
                    uid: $("#uid").val(),
                    price: $("#price").val(),
                    buy_date: $("#buy_date").val(),
                    trade_no: $("#trade_no").val(),
                    body: $("#body").val(),
                    out_trade_no: "1234"
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
                insert();
            }
        });
        $("#insert").click(function () {
            insert();
        });
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function () {
            $("#msg-error").hide(100);
        });
    })
</script>
{include file='user/footer.tpl'}