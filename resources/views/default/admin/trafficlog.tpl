{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            流量使用记录
            <small>Traffic Log</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <h4>注意!</h4>
                    <p>部分节点不支持流量记录.</p>
                </div>
            </div>
        </div>
        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="user_id" type="number" placeholder="输入用户id" class="form-control" value="{$q['user_id']}">
            </div>
            <div class="form-group">
                <input name="node_id" type="number" placeholder="输入节点id" class="form-control" value="{$q['node_id']}">
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>

        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas id="chart3" height="400"></canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas id="chart2" height="400"></canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas id="chart1" height="400"></canvas>
        </div>
        <!-- chart -->

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive no-padding">
                        {$logs->render()}
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>用户</th>
                                <th>使用节点</th>
                                <th>倍率</th>
                                <th>实际使用流量</th>
                                <th>结算流量</th>
                                <th>记录时间</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td><a href="https://www.shadowsky.website/admin/user/{$log->user_id}/edit">#{$log->id}</a></td>
                                    <td><a href="/admin/trafficlog?user_id={$log->user_id}">{$log->user_id}</a></td>
                                    <td><a href="/admin/trafficlog?node_id={$log->node_id}">{$log->node()->name}</a></td>
                                    <td>{$log->rate}</td>
                                    <td>{$log->totalUsed()}</td>
                                    <td>{$log->traffic}</td>
                                    <td>{$log->logTime()}</td>
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
var ctx = $("#chart1");
var chart1 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$array_for_chart}[0][0],
        datasets: [
            {
                label: "#(MB) Traffic by hour",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$array_for_chart}[1][0],
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
                    var sum = 0;
                    data.datasets[0].data.forEach(function(v) {
                        sum += v;
                    });
                    if (sum>1024) {
                        return 'Sum: ' + Math.round(sum/1024*100)/100 + " GB";
                    }else{
                        return 'Sum: ' + Math.round(sum) + " MB";
                    }
                },
            },
            footerFontStyle: 'bold'
        },
        hover: {
            mode: 'index',
            intersect: false
        },
        animation: {
            duration: 2000
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Time (Hour)',
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
                    labelString: 'Traffic (MB)',
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
var ctx = $("#chart2");
var chart2 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$array_for_chart}[0][1],
        datasets: [
            {
                label: "#(MB) Traffic by node",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$array_for_chart}[1][1],
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
                    var sum = 0;
                    data.datasets[0].data.forEach(function(v) {
                        sum += v;
                    });
                    if (sum>1024) {
                        return 'Sum: ' + Math.round(sum/1024*100)/100 + " GB";
                    }else{
                        return 'Sum: ' + Math.round(sum) + " MB";
                    }
                },
            },
            footerFontStyle: 'bold'
        },
        hover: {
            mode: 'index',
            intersect: false
        },
        animation: {
            duration: 2000
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Node name',
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
                    labelString: 'Traffic (MB)',
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
var ctx = $("#chart3");
var chart3 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$users_transfer_array_for_chart}.labels,
        datasets: [
            {
                label: "#(GB) Traffic by user",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$users_transfer_array_for_chart}.datas,
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
                    var sum = 0;
                    data.datasets[0].data.forEach(function(v) {
                        sum += v;
                    });
                    return 'Sum: ' + Math.round(sum*100)/100 + "GB";
                },
            },
            footerFontStyle: 'bold'
        },
        hover: {
            mode: 'index',
            intersect: false
        },
        animation: {
            duration: 2000
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'User ID',
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
                    labelString: 'Traffic (GB)',
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