{include file='admin/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            购买记录
            <small>
                Purchase Log
            </small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissable" id="msg-success" style="display: none;">
                    <button aria-hidden="true" class="close" id="ok-close" type="button">
                        ×
                    </button>
                    <h4>
                        <i class="icon fa fa-info">
                        </i>
                        成功!
                    </h4>
                    <p id="msg-success-p">
                    </p>
                </div>
                <div class="alert alert-warning alert-dismissable" id="msg-error" style="display: none;">
                    <button aria-hidden="true" class="close" id="error-close" type="button">
                        ×
                    </button>
                    <h4>
                        <i class="icon fa fa-warning">
                        </i>
                        出错了!
                    </h4>
                    <p id="msg-error-p">
                    </p>
                </div>
            </div>
        </div>
        <form action="" class="form-inline margin-bottom" method="GET">
            <div class="form-group">
                <input class="form-control" name="uid" placeholder="用户id" type="number" value="{$q['uid']}"/>
            </div>
            <div class="form-group">
                <input class="form-control" name="port" placeholder="用户端口" type="number" value="{$q['port']}"/>
            </div>
            <div class="form-group">
                <input class="form-control" name="out_trade_no" placeholder="交易号" type="text" value="{$q['out_trade_no']}"/>
            </div>
            <div class="form-group">
                <select class="form-control" name="body">
                    <option value="">
                        套餐
                    </option>
                    {foreach $products as $product}
                    <option value="{$product->name}">
                        {$product->name}
                    </option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <button class="form-control btn btn-default btn-flat" type="submit">
                    查询
                </button>
            </div>
        </form>
        <hr/>
        <fieldset class="form-inline margin-bottom">
            <div class="form-group">
                <input class="form-control" id="uid" placeholder="用户id" type="number"/>
            </div>
            <div class="form-group">
                <input class="form-control" id="port" placeholder="用户端口" type="number"/>
            </div>
            <div class="form-group">
                <select class="form-control" id="body">
                    <option value="">
                        套餐
                    </option>
                    {foreach $products as $product}
                    <option value="{$product->id}">
                        {$product->name}
                    </option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-default form-control" id="insert">
                    插入
                </button>
            </div>
        </fieldset>
        <hr/>
        <p>
            建站以来收入：{$income["all"]} 元
        </p>
        <p>
            本年收入：{$income["yearly"]} 元 （其中支出手续费 {$income["yearlyFee"]} 元）
        </p>
        <p>
            本月收入：{$income["monthly"]} 元 （其中支出手续费 {$income["monthlyFee"]} 元）
        </p>
        <p>
            本日收入：{$income["daily"]} 元 （其中支出手续费 {$income["dailyFee"]} 元）
        </p>
        <!-- chart -->
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="300" id="dailyIncomeChart">
            </canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="300" id="weeklyIncomeChart">
            </canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="300" id="monthlyIncomeChart">
            </canvas>
        </div>
        <!-- chart -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        {$logs->render()}
                        <table class="table table-hover">
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    用户ID
                                </th>
                                <th>
                                    用户名
                                </th>
                                <th>
                                    用户端口
                                </th>
                                <th>
                                    购买次数
                                </th>
                                <th>
                                    套餐
                                </th>
                                <th>
                                    价格
                                </th>
                                <th>
                                    手续费
                                </th>
                                <th>
                                    购买日期
                                </th>
                                <th>
                                    交易号
                                </th>
                                <th>
                                    操作
                                </th>
                            </tr>
                            {foreach $logs as $log}
                            <tr>
                                <td>
                                    <a href="/admin/user/{$log->user()->id}/edit">
                                        #{$log->id}
                                    </a>
                                </td>
                                <td>
                                    <a href="/admin/purchaselog?uid={$log->user()->id}">
                                        {$log->uid}
                                    </a>
                                </td>
                                <td>
                                    {$log->user()->user_name}
                                </td>
                                <td>
                                    {$log->user()->port}
                                </td>
                                <td>
                                    {$log->transactionCount()}
                                </td>
                                <td>
                                    {$log->body}
                                </td>
                                <td>
                                    {$log->price}
                                </td>
                                <td>
                                    {$log->fee}
                                </td>
                                <td>
                                    {$log->buy_date}
                                </td>
                                <td>
                                    {$log->out_trade_no}
                                </td>
                                <td>
                                    <a class="btn btn-default btn-sm" href="javascript:void(0);" id="delete" onclick="confirm_delete({$log->id});">
                                        删除
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                        </table>
                        {$logs->render()}
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
{include file='admin/footer.tpl'}
<script>
    function insert() {
        $.ajax({
            type: "POST",
            url: "/admin/addpurchase",
            dataType: "json",
            data: {
                uid: $("#uid").val(),
                port: $("#port").val(),
                body: $("#body").val()
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
            url: "/admin/purchaselog/" + item,
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
<script>
    var ctx = $("#dailyIncomeChart");
    var dailyIncomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$eachHour_income_for_chart}.labels,
            datasets: [
            {
                label: "日收入（元）",
                fill: false,
                borderColor: "rgba(75,192,192,0.4)",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$eachHour_income_for_chart}.datas,
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                labels: {
                    fontColor: "#bbb"
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    // Use the footer callback to display the sum of the items showing in the tooltip
                    footer: function(tooltipItems, data) {
                        // var sum = 0;
                        // data.datasets[0].data.forEach(function(v) {
                        //     sum += v;
                        // });
                        return 'Sum: ' + {$eachHour_income_for_chart}.total + " 元";
                    },
            },
            footerFontStyle: 'bold'
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            animation: {
                duration: 1500
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Time (Each Hour)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb"
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Income (CNY)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb",
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    var ctx = $("#weeklyIncomeChart");
    var weeklyIncomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$weekly_income_for_chart}.labels,
            datasets: [
            {
                label: "周收入（元）",
                fill: false,
                borderColor: "rgba(75,192,192,0.4)",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$weekly_income_for_chart}.datas,
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                labels: {
                    fontColor: "#bbb"
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    // Use the footer callback to display the sum of the items showing in the tooltip
                    footer: function(tooltipItems, data) {
                        // var sum = 0;
                        // data.datasets[0].data.forEach(function(v) {
                        //     sum += v;
                        // });
                        return 'Sum: ' + {$weekly_income_for_chart}.total + " 元";
                    },
            },
            footerFontStyle: 'bold'
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            animation: {
                duration: 1500
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Date (Each Day)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb"
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Income (CNY)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb",
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    var ctx = $("#monthlyIncomeChart");
    var monthlyIncomeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$monthly_income_for_chart}.labels,
            datasets: [
            {
                label: "月收入（元）",
                fill: false,
                borderColor: "rgba(75,192,192,0.4)",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$monthly_income_for_chart}.datas,
            }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                labels: {
                    fontColor: "#bbb"
                }
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            animation: {
                duration: 1500
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Date (Month)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb"
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Income (CNY)',
                        fontColor: "#bbb"
                    },
                    ticks: {
                        fontColor: "#bbb",
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
