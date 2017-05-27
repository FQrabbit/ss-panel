{include file='user/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            流量使用记录
            <small>
                Traffic Log
            </small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary left-border">
                    <div class="w3-padding">
                        <h4>
                            注意!
                        </h4>
                        <p>
                            仅保存当日的流量记录，部分节点不支持流量记录.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="400" id="chart1">
            </canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="400" id="chart2">
            </canvas>
        </div>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas height="400" id="userWeeklyTraffic">
            </canvas>
        </div>
        <!-- chart -->
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            {$logs->render()}
                            <table class="table table-striped">
                                <tr>
                                    <th>ID</th>
                                    <th>使用节点</th>
                                    <th>倍率</th>
                                    <th>实际使用流量</th>
                                    <th>结算流量</th>
                                    <th>记录时间</th>
                                </tr>
                                {foreach $logs as $log}
                                <tr>
                                    <td>#{$log->id}</td>
                                    <td>{$log->node->name}</td>
                                    <td>{$log->rate}</td>
                                    <td>{$log->totalUsed()}</td>
                                    <td>{$log->traffic}</td>
                                    <td>{$log->logTime()}</td>
                                </tr>
                                {/foreach}
                            </table>
                            {$logs->render()}
                        </div>
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
{include file='user/footer.tpl'}
<script>
var ctx = $("#chart1");
var chart1 = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$array_for_chart}[0][0],
        datasets: [
            {
                label: "#(MB) Traffic in an hour",
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
                    labelString: 'Time Period(Hour)',
                    fontColor: "#bbb"
                },
                ticks: {
                    fontColor: "#bbb",
                    autoSkip: true,
                    autoSkipPadding: 2
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
                label: "#(MB) Traffic by Node",
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
                    labelString: 'Node Name',
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
var ctx = $("#userWeeklyTraffic");
var userWeeklyTraffic = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$users_weekly_traffic_for_chart}.labels,
        datasets: [
            {
                label: "#(GB) Traffic in a week",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$users_weekly_traffic_for_chart}.datas,
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
                        return 'Sum: ' + Math.round(sum) + " GB";
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
                    labelString: 'Node Name',
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