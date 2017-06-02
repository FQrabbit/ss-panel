{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            支出记录
            <small>Expenditure Log</small>
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

        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <select class="form-control" name="vps_merchant_id">
                    <option value="">vps供应商</option>
                    {foreach $vpsMerchants as $merchant}
                    <option value="{$merchant->id}">{$merchant->name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="node">
                    <option value="">节点</option>
                    {foreach $nodes as $node}
                    <option value="{$node->id}">{$node->name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>

        <hr>
        
        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <input id="m_name" type="text" placeholder="name" class="form-control">
            </div>
            <div class="form-group">
                <input id="m_api" type="text" placeholder="api" class="form-control">
            </div>
            <div class="form-group">
                <input id="m_website" type="text" placeholder="website" class="form-control">
            </div>
            <div class="form-group">
                <button id="add_vps_merchant" class="btn btn-default form-control">添加供应商</button>
            </div>
        </fieldset>

        <hr/>
        
        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <select class="form-control" id="vps_merchant_id">
                    {foreach $vpsMerchants as $merchant}
                    <option value="{$merchant->id}">{$merchant->name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" id="node_id">
                    {foreach $nodes as $node}
                    <option value="{$node->id}">{$node->name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <input id="price" type="number" placeholder="价格（CNY）" class="form-control">
            </div>
            <div class="form-group">
                <button id="insert" class="btn btn-default form-control">插入</button>
            </div>
        </fieldset>

        <hr/>

        <p>
            本年vps支出：{$expenditure["yearly"]} 元
        </p>
        <p>
            本月vps支出：{$expenditure["monthly"]} 元
        </p>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            {$logs->render()}
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>vps</th>
                                    <th>节点</th>
                                    <th>价格</th>
                                    <th>日期</th>
                                    <th>操作</th>
                                </tr>
                                {foreach $logs as $log}
                                    <tr>
                                        <td>{$log->id}</td>
                                        <td>{$log->vpsMerchant->name}</td>
                                        <td>{$log->node->name}</td>
                                        <td>{$log->price}</td>
                                        <td>{$log->date}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" id="delete" href="javascript:void(0);" onclick="confirm_delete({$log->id});">删除</a>
                                        </td>
                                    </tr>
                                {/foreach}
                            </table>
                            {$logs->render()}
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script>
    function addVpsMerchant() {
        $.ajax({
            type: "POST",
            url: "/admin/addvpsmerchant",
            dataType: "json",
            data: {
                name: $("#m_name").val(),
                api: $("#m_api").val(),
                website: $("#m_website").val()
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
                    $("#insert").removeAttr("disabled");
                }
            },
            error: function (jqXHR) {
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + jqXHR.status);
                $("#insert").removeAttr("disabled");
            }
        });
    }

    function insert() {
        $.ajax({
            type: "POST",
            url: "/admin/addexpenditure",
            dataType: "json",
            data: {
                vps_merchant_id: $("#vps_merchant_id").val(),
                node_id: $("#node_id").val(),
                price: $("#price").val()
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
                    $("#insert").removeAttr("disabled");
                }
            },
            error: function (jqXHR) {
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误：" + jqXHR.status);
                $("#insert").removeAttr("disabled");
            }
        });
    }

    function deleterecord(item) {
        $.ajax({
            type: "DELETE",
            url: "/admin/expenditurelog/" + item,
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


    $("#add_vps_merchant").click(function () {
        $(this).attr("disabled", "disabled");
        addVpsMerchant();
    });
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