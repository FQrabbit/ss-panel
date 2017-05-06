{include file='user/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            公告
            <small>
                Announcement
            </small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- chart -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body table-responsive">
                        {$anns->render()}
                        <table class="table">
                            <tr>
                                <th>
                                    ID
                                </th>
                                <th>
                                    内容
                                </th>
                                <th>
                                    日期
                                </th>
                            </tr>
                            {foreach $anns as $ann}
                            <tr>
                                <td>
                                    #{$ann->id}
                                </td>
                                <td>
                                    {$ann->content}
                                </td>
                                <td>
                                    {$ann->time}
                                </td>
                            </tr>
                            {/foreach}
                        </table>
                        {$anns->render()}
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
{include file='user/footer.tpl'}
