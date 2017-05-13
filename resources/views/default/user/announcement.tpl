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
                <div class="box box-primary">
                    <div class="box-body">
                    {$anns->render()}
                        {foreach $anns as $ann}
                        <div class="panel panel-primary">
                            <div class="panel-heading">{$ann->time}</div>
                            <div class="panel-body">{$ann->content}</div>
                        </div>
                        {/foreach}
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
