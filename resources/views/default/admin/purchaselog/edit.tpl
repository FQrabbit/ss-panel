{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            购买记录编辑 #{$log->id} {$log->user->user_name}
            <small>Edit Purchase Log</small>
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

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">用户ID</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="uid" type="number" value="{$log->uid}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">套餐</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="product_id">
                                                {foreach $products as $product}
                                                <option value="{$product->id}"{if $log->product_id == $product->id} selected{/if}>
                                                    {$product->name}
                                                </option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">订单号</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="out_trade_no" type="text" value="{$log->out_trade_no}">
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">支付方式</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="payment_method">
                                                <option value="">
                                                    无
                                                </option>
                                                {foreach $payment_methods as $method}
                                                <option value="{$method}"{if $log->payment_method == $method} selected{/if}>
                                                    {$method}
                                                </option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="col-sm-3 control-label">费用</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="fee" type="number" value="{$log->fee}">
                                        </div>
                                    </div>

                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" id="submit" name="action" class="btn btn-default">修改</button>
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
                url: "/admin/purchaselog/{$log->id}",
                dataType: "json",
                data: {
                    uid: $("#uid").val(),
                    product_id: $("#product_id").val(),
                    out_trade_no: $("#out_trade_no").val(),
                    payment_method: $("#payment_method").val(),
                    fee: $("#fee").val(),
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
    })
</script>