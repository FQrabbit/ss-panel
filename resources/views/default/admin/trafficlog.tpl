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
        <form action="" method="GET" class="form-horizontal">
            <div class="input-group">
                <input name="user_id" type="number" placeholder="输入用户id" class="form-control col-md-3">
                <input name="node_id" type="number" placeholder="输入节点id" class="form-control col-md-3">
                <button type="submit" class="btn btn-default btn-flat col-md-3">查询</button>
            </div>
        </form>
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
                                    <td>#{$log->id}</td>
                                    <td>{$log->user_id}</td>
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