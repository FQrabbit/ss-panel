{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            统计信息
            <small>System Info</small>
        </h1>
    </section>
    <!-- Main content -->
        <section class="content">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="callout callout-warning">
                                <h4>注意！</h4>
                                <p>流量统计仅供参考，在线人数有一小会儿的延迟。</p>
                            </div>
                            <p>当前时间：{date("Y-m-d H:i",time())}</p>
                            <p>{$config["appName"]}本月已经产生流量<code>{$ana["usedTransfer"]}</code>。</p>
                            <p>注册用户：<code>{$ana["allUserCount"]}</code></p>
                            <p>已经有<code>{$ana["activeUserCount"]}</code>个用户使用了{$config["appName"]}服务。</p>
                            <p>24小时签到用户：<code>{$ana["checkinCount"]}</code></p>
                            <p>付费用户人数：<code>{$ana["paidUserCount"]}</code>。</p>
                            <p>捐助用户人数：<code>{$ana["donateUserCount"]}</code>。</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 style="text-align:center"><strong>付费用户列表</strong></h4>
                                    <table id="paid_user_table" class="display hover">
                                        <thead>
                                            <tr>
                                                <th>uid</th>
                                                <th>用户名</th>
                                                <th>套餐名</th>
                                            </tr>
                                        </thead>
                                    <tbody>
                                    {foreach $users as $user}
                                        {if $user->plan == "B"}
                                            <tr>
                                                <td>{$user->id}</td>
                                                <td>{mb_substr($user->user_name, 0, 4, 'utf-8')}***</td>
                                                <td>{$user->type}</td>
                                            </tr>
                                        {/if}
                                    {/foreach}
                                    </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h4 style="text-align:center"><strong>捐助榜</strong></h4>
                                    <table id="donate_table" class="display hover">
                                        <thead>
                                            <tr>
                                                <th>uid</th>
                                                <th>用户名</th>
                                                <th>捐助金额(元)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        {foreach $users as $user}
                                            {if $user->ref_by == 3}
                                                <tr>
                                                    <td>{$user->id}</td>
                                                    <td>{mb_substr($user->user_name, 0, 4, 'utf-8')}***</td>
                                                    <td>{$user->money}</td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div><!-- /.box -->
                    </div>
                </div>
        </section><!-- /.content -->
</div><!-- /.content-wrapper -->
{include file='user/footer.tpl'}

 <script>
    $(document).ready( function () {
        $('#donate_table').DataTable({
        "scrollY": "390px",
        "scrollX": false,
        "order": [2, "desc"]
        });
        $('#paid_user_table').DataTable({
        "scrollY": "390px",
        "scrollX": false,
        "order": [[ 2, "asc" ],[0,"asc"]]
        });
    } );
</script>