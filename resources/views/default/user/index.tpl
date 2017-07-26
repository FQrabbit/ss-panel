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

                                <h3 class="box-title">公告&amp;FAQ</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="margin-top:-13px">
                                <p>{$msg}</p>
                                <div class="social">
                                    <a href="https://telegram.me/shadowsky" target="_blank" class="w3-btn col-md-4 col-xs-6 w3-border-right">Telegram群组</a>
                                    <a href="https://plus.google.com/communities/102799415585211637190" target="_blank" class="w3-btn col-md-4 col-xs-6 w3-border-right">G+社群</a>
                                    <a href="http://shadowsky-join-slack.herokuapp.com/" target="_blank" class="w3-btn col-md-4 col-xs-12">Slack(不用番茄)</a>
                                </div>
                                <button class="w3-btn w3-block donate-btn" onclick="$('#donateModal').show()">捐助</button>
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
                                        <button id="checkin" class="btn btn-default  btn-flat w3-border">签到</button>
                                    </p>
                                {else}
                                    <p><a class="btn btn-default btn-flat disabled w3-border" href="#">不能签到</a></p>
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
                                    <div class="col-md-6">
                                        <canvas id="myPieChart" class="center" style="max-height: 250px;max-width: 250px"></canvas>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="center w3-padding">
                                        {if $user->unlimitTransfer()}
                                            <p>已用流量: <code>{$user->usedTraffic()}</code></p>
                                            <!-- <p>购买日期: <br><code>{$user->buy_date}</code></p> -->
                                            <p>到期日期: <br><code>{$user->expire_date}</code></p>
                                        {else}
                                            <p>总流量: <code>{$user->enableTraffic()}</code></p>
                                            <p>已用流量: <code>{$user->usedTraffic()}</code></p>
                                            <p>剩余流量: <code>{$user->unusedTraffic()}</code></p>
                                        {/if}
                                        {if $user->product && $user->product->isByTime() && $user->willResetTransfer()}
                                            <p>流量重置日:<br><code>{$user->nextTransferResetDate()}</code></p>
                                        {/if}
                                        {if $user->product && $user->product->isByTime() && !$user->unlimitTransfer()}
                                            <p>平均每日还可使用: <code>{$user->transferAvailableEveryDay()} G</code></p>
                                        {/if}
                                        </div>
                                    </div>
                                </div>
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
                                            {$user->getUserClassName()} {if $user->product}| {$user->product->name}{/if}
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

<div id="donateModal" class="w3-modal" style="z-index:10001;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container">
            <span onclick="$('#donateModal').fadeOut()" class="w3-btn w3-large w3-display-topright close-btn">×</span>
            <h3>捐助Shadowsky</h3>
        </header>
        <hr>
        <br>
        <div class="w3-container">
            <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                <div class="w3-row">
                        <label class="w3-label">请输入捐助金额：</label>
                        <input type="hidden" name="uid" value="{$user->id}">
                        <input type="hidden" name="product_id" value="0">
                        <input class="w3-input" name="total" type="number" value=2 style="width:20%;display:inline">
                        <button class="w3-btn w3-border" type="submit">确认</button>
                </div>
            </form>
            <div class="w3-row w3-margin-top w3-margin-bottom">
                {foreach [2,5,10,20] as $a}
                <div class="w3-quarter">
                    <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                        <input type="hidden" name="uid" value="{$user->id}">
                        <input type="hidden" name="product_id" value="0">
                        <input type="hidden" name="total" value="{$a}">
                        <button class="w3-btn w3-xxlarge w3-center" type="submit">￥{$a}</button>
                    </form>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

<div id="activate-modal" class="w3-modal" style="z-index:10001;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container">
            <span onclick="$('#activate-modal').fadeOut()" class="w3-btn w3-large w3-display-topright close-btn">×</span>
            <h3>激活账号</h3>
        </header>
        <hr>
        <div class="w3-container">
            <br>
            <p>Hello, {$user->user_name}</p>
            <p>由于您已超过一个月没有使用本站的ss了，为了释放服务器资源，您的账号已被冻结，点击下面的按钮可重新激活账号。</p>
            <button id="activate" class="w3-btn w3-border w3-margin">点此激活账号</button>
            <p id="activate-msg"></p>
        </div>
    </div>
</div>
<div id="new-ann-modal" class="w3-modal" style="z-index:10001;display:none;">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container">
            <span onclick="$('#new-ann-modal').fadeOut()" class="w3-btn w3-large w3-display-topright close-btn">×</span>
            <h3>{$new_ann->title}</h3>
        </header>
        <hr>
        <div class="w3-container">
            <br>
            <p>{$new_ann->content}</p>
        </div>
        <hr>
        <footer class="w3-container w3-padding">
            <button id="read" class="pull-right w3-btn w3-border" onclick="read({$new_ann->id})">知道了</button>
        </footer>
    </div>
</div>

{include file='user/footer.tpl'}

<script>
    $(document).ready(function () {
        var ctx = $('#myPieChart');
    {if $user->unlimitTransfer()}
        var labels = ['已使用天数', '剩余天数'];
        var used = {$user->daysFromBuyDate()};
        var remain = {$user->daysUntilExpireDate()};
    {else}
        var labels = ['已使用流量(GB)', '剩余流量(GB)'];
        var used = {$user->usedTrafficInGB()};
        var remain = {$user->unusedTrafficInGB()};
    {/if}
        var data = {
            labels: labels,
            datasets: [
                {
                    data: [used, remain],
                    backgroundColor: [
                        'rgba(12, 12, 12, 0.2)',
                        'rgba(3, 144, 81, 1)',
                    ],
                    borderWidth: 3,
                    borderColor: 'rgba(0,0,0,0.1)',
                }]
        };
        var myPieChart = new Chart(ctx,{
            type: 'pie',
            data: data,
            options: {
                animation:{
                    animateScale:true
                },
                responsive: true,
                legend: {
                    labels: {
                        fontColor: "#ddd"
                    }
                },
            }
        });

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
                        $("#checkin-btn").fadeOut();
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