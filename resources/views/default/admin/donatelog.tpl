{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            捐助记录
            <small>Donate Log</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="uid" type="number" placeholder="输入用户id" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
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
                                <th>用户ID</th>
                                <th>用户名</th>
                                <th>用户端口</th>
                                <th>金额</th>
                                <th>捐助日期</th>
                                <th>交易号</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td><a href="/admin/user/{$log->user()->id}/edit">#{$log->id}</a></td>
                                    <td>{$log->uid}</td>
                                    <td>{$log->user()->user_name}</td>
                                    <td>{$log->user()->port}</td>
                                    <td>{$log->money}</td>
                                    <td>{$log->datetime}</td>
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

{include file='user/footer.tpl'}