{include file='user/main.tpl'}
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
            <div class="col-xs-12">
                <div class="table-responsive no-padding">
                    {$logs->render()}
                    <table class="table table-hover">
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>
                                套餐
                            </th>
                            <th>
                                金额
                            </th>
                            <th>
                                购买日期
                            </th>
                            <th>
                                交易号
                            </th>
                        </tr>
                        {foreach $logs as $log}
                        <tr>
                            <td>
                                #{$log->id}
                            </td>
                            <td>
                                {$log->body}
                            </td>
                            <td>
                                {$log->price}
                            </td>
                            <td>
                                {$log->buy_date}
                            </td>
                            <td>
                                {$log->out_trade_no}
                            </td>
                        </tr>
                        {/foreach}
                    </table>
                    {$logs->render()}
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
{include file='user/footer.tpl'}
