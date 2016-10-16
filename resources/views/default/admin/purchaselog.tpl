{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            购买记录
            <small>Purchase Log</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

        <form action="" method="GET" class="form-inline margin-bottom">
            <div class="form-group">
                <input name="uid" type="number" placeholder="用户id" class="form-control">
            </div>
            <div class="form-group">
                <select name="body" class="form-control">
                    <option value="">套餐</option>
                    <option value="试玩">试玩</option>
                    <option value="基础">基础</option>
                    <option value="包月">包月</option>
                    <option value="包季">包季</option>
                    <option value="包年">包年</option>
                </select>
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
                                <th>用户</th>
                                <th>套餐</th>
                                <th>价格</th>
                                <th>购买日期</th>
                                <th>交易号</th>
                            </tr>
                            {foreach $logs as $log}
                                <tr>
                                    <td>#{$log->id}</td>
                                    <td>{$log->uid}</td>
                                    <td>{$log->body}</td>
                                    <td>{$log->price}</td>
                                    <td>{$log->buy_date}</td>
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