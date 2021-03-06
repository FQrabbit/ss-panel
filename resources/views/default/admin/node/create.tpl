{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            添加节点
            <small>Add Node</small>
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
                                <fieldset class="col-sm-6">
                                    <legend>连接信息</legend>
                                    <div class="form-group">
                                        <label for="title" class="col-sm-3 control-label">节点名称</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="name" value="" placeholder="us1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="server" class="col-sm-3 control-label">节点地址</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="server" value="{$config['baseUrl']}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ip" class="col-sm-3 control-label">IP地址</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ip" value="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="ipv6" class="col-sm-3 control-label">IPV6地址</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ipv6" value="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="method" class="col-sm-3 control-label">加密方式</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="method">
                                                {foreach $methods as $method}
                                                <option value="{$method}">{$method}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="protocol" class="col-sm-3 control-label">协议</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="protocol">
                                                {foreach $protocols as $protocol}
                                                {if strpos($protocol, 'compatible') === false}
                                                <option value="{$protocol}">{$protocol}</option>
                                                {/if}
                                                {/foreach}
                                                <option disabled="disabled">==以下兼容原协议==</option>
                                                {foreach $protocols as $protocol}
                                                {if strpos($protocol, 'compatible') !== false}
                                                <option value="{$protocol}">{$protocol}</option>
                                                {/if}
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="obfs" class="col-sm-3 control-label">混淆</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="obfs">
                                                {foreach $obfses as $obfs}
                                                {if strpos($obfs, 'compatible') === false}
                                                <option value="{$obfs}">{$obfs}</option>
                                                {/if}
                                                {/foreach}
                                                <option disabled="disabled">==以下兼容原协议==</option>
                                                {foreach $obfses as $obfs}
                                                {if strpos($obfs, 'compatible') !== false}
                                                <option value="{$obfs}">{$obfs}</option>
                                                {/if}
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="traffic_rate" class="col-sm-3 control-label">流量比例</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="traffic_rate" value="1">
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="method" class="col-sm-3 control-label">自定义加密</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="custom_method">
                                                <option value="0">
                                                    不支持
                                                </option>
                                                <option value="1" selected="selected">
                                                    支持
                                                </option>
                                            </select>

                                            <p class="help-block">
                                                <a href="https://github.com/orvice/ss-panel/wiki/v3-custom-method">如何使用自定义加密?</a>|
                                                <a href="https://github.com/orvice/ss-panel/wiki/v3-traffic-rate">如何设置流量比例?</a>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="rss" class="col-sm-3 control-label">自定义rss</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="custom_rss">
                                                <option value="0">
                                                    不支持
                                                </option>
                                                <option value="1" selected="selected">
                                                    支持
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset class="col-sm-6">
                                    <legend>描述信息</legend>

                                    <div class="form-group">
                                        <label for="id" class="col-sm-3 control-label">id</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="id" type="number" value="" placeholder="1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="type" class="col-sm-3 control-label">等级（0为免费，1为付费）</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="type">
                                                <option value="1" selected="selected">付费</option>
                                                <option value="0">免费</option>
                                                <option value="-1">其他，测试，隐藏</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="status" class="col-sm-3 control-label">节点状态</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="status" value="可用">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="sort" class="col-sm-3 control-label">排序</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="sort" type="number" value="" placeholder="1">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="info" class="col-sm-3 control-label">节点描述</label>

                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="info" rows="2" value="付费节点"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="transfer_reset_day" class="col-sm-3 control-label">流量重置日</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="transfer_reset_day" value="1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="vps" class="col-sm-3 control-label">主机商</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="vps">
                                                {foreach $vpsMerchants as $mer}
                                                <option value="{$mer->id}">{$mer->name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="renewal_day" class="col-sm-3 control-label">续费日</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="renewal_day" value="1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="subid" class="col-sm-3 control-label">subid</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="subid" placeholder="如:3868748">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="api" class="col-sm-3 control-label">api</label>

                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="api" placeholder="如:https://api.vultr.com/v1/server/list?api_key=" row="2"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="node_usage" class="col-sm-3 control-label">使用情况</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="node_usage"type="number" value="0">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="transfer" class="col-sm-3 control-label">总流量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="transfer" type="number" value="500">
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" id="submit" name="action" value="add" class="btn btn-default">添加</button>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script>
    $(document).ready(function () {
        function submit() {
            $.ajax({
                type: "POST",
                url: "/admin/node",
                dataType: "json",
                data: {
                    id: $("#id").val(),
                    name: $("#name").val(),
                    server: $("#server").val(),
                    ip: $("#ip").val(),
                    ipv6: $("#ipv6").val(),
                    method: $("#method").val(),
                    protocol: $("#protocol").val(),
                    obfs: $("#obfs").val(),
                    custom_method: $("#custom_method").val(),
                    custom_rss: $("#custom_rss").val(),
                    traffic_rate: $("#traffic_rate").val(),
                    info: $("#info").val(),
                    type: $("#type").val(),
                    status: $("#status").val(),
                    sort: $("#sort").val(),
                    transfer: $("#transfer").val(),
                    transfer_reset_day: $("#transfer_reset_day").val(),
                    vps: $("#vps").val(),
                    renewal_day: $("#renewal_day").val(),
                    subid: $("#subid").val(),
                    api: $("#api").val(),
                    node_usage: $("#node_usage").val()
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