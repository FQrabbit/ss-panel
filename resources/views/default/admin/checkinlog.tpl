{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            签到记录
            <small>Checkin Log</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="user_id" type="number" placeholder="输入用户id" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-default btn-flat">查询</button>
            </div>
        </form>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            {$logs->render()}
                            <table class="table table-hover">
                                <tr>
                                    <th>ID</th>
                                    <th>用户</th>
                                    <th>获得流量</th>
                                    <th>签到时间</th>
                                </tr>
                                {foreach $logs as $log}
                                    <tr>
                                        <td>#{$log->id}</td>
                                        <td data-toggle="tooltip" data-placement="top" data-original-title="{$log->user->user_name}">{$log->user_id}</td>
                                        <td>{$log->traffic()}</td>
                                        <td>{$log->CheckInTime()}</td>
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