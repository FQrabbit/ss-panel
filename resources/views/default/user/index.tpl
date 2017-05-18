{include file='user/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户中心
            <small>User Center</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- START PROGRESS BARS -->
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-bullhorn"></i>

                                <h3 class="box-title">公告&FAQ</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="margin-top:-13px">
                                <p>{$msg}</p>
                                <div class="w3-bar" style="margin-top:10px">
                                        <a href="https://telegram.me/shadowsky" target="_blank" class="w3-btn w3-bar-item w3-green w3-border-right" style="width:50%;border-color:rgba(0,0,0,0.2) !important">Telegram群组</a>
                                        <a href="https://plus.google.com/communities/102799415585211637190" target="_blank" class="w3-btn w3-bar-item w3-green w3-border-right w3-border-left" style="width:50%;border-color:rgba(0,0,0,0.2) !important">G+社群</a>
                                </div>
                                <button class="w3-btn w3-teal w3-block" style="margin-top:10px" onclick=$("#donateModal").show()>捐助</button>
                            </div><!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col (right) -->
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-pencil"></i>

                                <h3 class="box-title">签到获取流量</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <p> 每{$config['checkinTime']}小时可以签到一次。</p>

                                <p>上次签到时间：<span class="badge bg-green">{$user->lastCheckInTime()}</span></p>
                                {if $user->isAbleToCheckin() }
                                    <p id="checkin-btn">
                                        <button id="checkin" class="btn btn-default  btn-flat">签到</button>
                                    </p>
                                {else}
                                    <p><a class="btn btn-default btn-flat disabled" href="#">不能签到</a></p>
                                {/if}
                                <p id="checkin-msg"></p>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <!-- /.col (right) -->
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa fa-exchange"></i>

                                <h3 class="box-title">流量使用情况</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="progress progress-striped">
                                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40"
                                                 aria-valuemin="0" aria-valuemax="100"
                                                 style="width: {$user->trafficUsagePercent()}%">
                                                <span class="sr-only">Transfer</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <dl class="dl-horizontal">
                                    <dt>总流量</dt>
                                    <dd>{$user->enableTraffic()}</dd>
                                    <dt>已用流量</dt>
                                    <dd>{$user->usedTraffic()}</dd>
                                    <dt>剩余流量</dt>
                                    <dd>{$user->unusedTraffic()}</dd>
                                    {if $user->willResetTransfer()}
                                    <dt>流量重置日</dt>
                                    <dd>{$user->nextTransferResetDate()}</dd>
                                    {/if}
                                </dl>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col (left) -->

                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa  fa-paper-plane"></i>

                                <h3 class="box-title">账号信息</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <dl class="dl-horizontal">
                                    <dt>端口</dt>
                                    <dd>{$user->port}</dd>
                                    <dt>密码</dt>
                                    <dd>{$user->passwd}</dd>
                                    <dt>上次使用</dt>
                                    <dd>{$user->lastSsTime()}</dd>
                                    <dt>用户类型</dt>
                                    <dd>
                                        <span class="badge bg-green">
                                            {$user->getUserClassName()} {if $user->product_id}| {$user->product->name}{/if}
                                        </span>
                                        {if $user->isDonator()}
                                        <span class="badge bg-green">
                                            捐助用户 | ￥{$user->money}
                                        </span>
                                        {/if}
                                    </dd>

                                {if $user->expire_date != 0}
                                    <dt>到期时间</dt>
                                    <dd><span class="badge w3-teal">{$user->expire_date}</span></dd>
                                {/if}

                                {if !$user->enable}
                                    <dt>账号状态</dt>
                                    <dd><span class="badge bg-red">禁用{if $user->status == 0} | 滥用{/if}</span></dd>
                                {/if}
                                </dl>

                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

<!--                     <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <i class="fa  fa-music"></i>

                                <h3 class="box-title">不妨听首歌</h3>
                            </div>
                            <div class="box-body">
                                <iframe frameborder="no" border="0" marginwidth="0" marginheight="0" width=100% height=86 src="//music.163.com/outchain/player?type=2&id={$mid}&auto=0&height=66"></iframe>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- /.row --><!-- END PROGRESS BARS -->
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<div id="donateModal" class="w3-modal" style="z-index:999999;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container w3-teal">
            <span onclick=$("#donateModal").hide() class="w3-btn w3-large w3-display-topright">×</span>
            <h3>捐助Shadowsky</h3>
        </header>
        <div class="w3-container">
            <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                <div class="w3-row">
                        <lable class="w3-label">请输入捐助金额：</label>
                        <input type="hidden" name="uid" value="{$user->id}">
                        <input type="hidden" name="product_id" value="00">
                        <input class="w3-input" name="total" type="number" value=2 style="width:20%;display:inline">
                        <button class="w3-btn w3-teal" type="submit">确认</button>
                </div>
            </form>
            <div class="w3-row w3-margin-top">
                {foreach [2,5,10,20] as $a}
                <div class="w3-quarter">
                    <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                        <input type="hidden" name="uid" value="{$user->id}">
                        <input type="hidden" name="product_id" value="00">
                        <input type="submit" name="total" class="w3-btn w3-xxlarge w3-center w3-teal" value={$a}>
                    </form>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

<div id="activate-modal" class="w3-modal" style="z-index:999999;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container w3-teal">
            <span onclick=$("#activate-modal").hide() class="w3-btn w3-large w3-display-topright">×</span>
            <h3>激活账号</h3>
        </header>
        <div class="w3-container">
            <br>
            <p>Hello, {$user->user_name}。由于您已超过一个月没有使用本站的ss了，为了释放服务器资源，您的账号已被冻结，点击下面的按钮可重新激活账号。</p>
            <button id="activate" class="btn btn-default btn-flat btn-sm w3-margin">点此激活账号</button>
            <br>
            <p id="activate-msg"></p>
        </div>
    </div>
</div>
<div id="new-ann-modal" class="w3-modal" style="z-index:999999;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container w3-teal">
            <span onclick=$("#new-ann-modal").hide() class="w3-btn w3-large w3-display-topright">×</span>
            <h3>{$new_ann->title}</h3>
        </header>
        <div class="w3-container">
            <br>
            <p>{$new_ann->content}</p>
        </div>
        <footer class="w3-container w3-teal w3-padding">
            <button id="read" class="pull-right w3-btn w3-teal w3-border" onclick="read({$new_ann->id})">知道了</button>
        </footer>
    </div>
</div>

{include file='user/footer.tpl'}

<script>
    $(document).ready(function () {
        $("#checkin").click(function () {
                $(this).hide(0, function(){
                    $("#checkin-msg").html("loading...");
                })
                $.ajax({
                    type: "POST",
                    url: "/user/checkin",
                    dataType: "json",
                    success: function (data) {
                        $("#checkin-msg").html(data.msg);
                        $("#checkin-btn").hide();
                    },
                    error: function (jqXHR) {
                        alert("发生错误：" + jqXHR.status);
                    }
                })
        })

        $("#activate").click(function () {
                $.ajax({
                    type: "POST",
                    url: "/user/activate",
                    dataType: "json",
                    success: function (data) {
                        $("#activate-msg").html(data.msg);
                        window.setTimeout("location.reload()", 3000);
                    },
                    error: function (jqXHR) {
                        alert("发生错误：" + jqXHR.status);
                    }
                })
        })

        {if !$user->enable && $user->status == 1}
            $("#activate-modal").show();
        {/if}

        {if $new_ann && !$user->getReadStatusOfAnn($new_ann->id)}
            $("#new-ann-modal").show();
        {/if}
    })

    function read (id) {
        $("#new-ann-modal").fadeOut("slow");
        $.ajax({
            type: "POST",
            url: "/user/readann/" + id,
            dataType: "json",
            success: function (data) {
                console.log("已阅读公告");
            },
            error: function (jqXHR) {
                console.log("发生错误：" + jqXHR.status);
            }
        })
    }
</script>