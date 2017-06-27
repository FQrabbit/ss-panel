{include file='admin/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            节点列表
            <small>Node List</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div id="msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display:none">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="margin-bottom" style="background-color:rgba(0, 0, 0, 0.6);padding:10px;">
                    <canvas height="300" id="Vote">
                    </canvas>
                </div>
                <p> <a class="btn btn-success btn-sm" href="/admin/node/create">添加</a> </p>
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>节点</th>
                                    <th>加密</th>
                                    <th>IP地址</th>
                                    <th>描述</th>
                                    <th>vps</th>
                                    <th>排序</th>
                                    <th>操作</th>
                                </tr>
                                {foreach $nodes as $node}
                                <tr>
                                    <td>#{$node->id}</td>
                                    <td> {$node->name}</td>
                                    <td>{$node->method}</td>
                                    <td>{$node->ip}</td>
                                    <td>{$node->info}</td>
                                    <td><a href="{$node->vpsMerchant->website}" target="_blank">{$node->vpsMerchant->name}</a></td>
                                    <td>{$node->sort}</td>
                                    <td>
                                        <a class="btn btn-success btn-sm" href="/admin/node/{$node->id}/edit">编辑</a>
                                        <a class="btn btn-danger btn-sm" id="delete" value="{$node->id}" href="javascript:void(0);" onclick="confirm_delete({$node->id});">删除</a>
                                    </td>
                                </tr>
                                {/foreach}
                            </table>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='admin/footer.tpl'}

<script>
    function deleteNode(id){
        $.ajax({
            type:"DELETE",
            url:"/admin/node/" + id,
            dataType:"json",
            success:function(data){
                if(data.ret){
                    $("#msg-error").hide(100);
                    $("#msg-success").show(100);
                    $("#msg-success-p").html(data.msg);
                    window.setTimeout("location.href='/admin/node'", 2000);
                }else{
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html(data.msg);
                }
            },
            error:function(jqXHR){
                $("#msg-error").hide(10);
                $("#msg-error").show(100);
                $("#msg-error-p").html("发生错误："+jqXHR.status);
            }
        });
    }
    function confirm_delete(id) {
        $.confirm({
            title: '确认操作',
            content: '你确定要删除这个节点?',
            confirm: function(){
                deleteNode(id);
            },
            confirmButton: '是',
            cancelButton: '否',
            theme: 'black'
        });
    }
    $("#ok-close").click(function(){
        $("#msg-success").hide(100);
    });
    $("#error-close").click(function(){
        $("#msg-error").hide(100);
    });


    var ctx = $("#Vote");
    var Vote = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {$nodes_polls}.labels,
            datasets: [
            {
                label: "like",
                fill: false,
                borderColor: "rgb(32, 90, 87)",
                backgroundColor: "rgb(32, 90, 87)",
                data: {$nodes_polls}.datas[1],
            },
            {
                label: "dislike",
                fill: false,
                borderColor: "rgb(169, 68, 66)",
                backgroundColor: "rgb(169, 68, 66)",
                data: {$nodes_polls}.datas[0],
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
                        labelString: '节点',
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
                        labelString: 'The number of votes',
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