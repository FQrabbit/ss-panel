{include file='user/main.tpl'}

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
                    <p>仅保存当日的流量记录，部分节点不支持流量记录.</p>
                </div>
            </div>
        </div>

        <!-- chart -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
        <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
            <canvas id="chart" height="400"></canvas>
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
                                <th>使用节点</th>
                                <th>倍率</th>
                                <th>实际使用流量</th>
                                <th>结算流量</th>
                                <th>记录时间</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td>#{$log->id}</td>
                                    <td>{$log->node()->name}</td>
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

{include file='user/footer.tpl'}

<script>
var ctx = $("#chart");
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {$labels}[0],
        datasets: [
            {
                label: "#(MB) Traffic",
                backgroundColor: "rgba(75,192,192,0.4)",
                data: {$datas}[0],
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
                    return 'Sum: ' + Math.round(sum);
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
                    fontColor: "#bbb"
                }
            }]
        }
    }
});
</script>