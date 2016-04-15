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
      	                        <div>1. 流量不会重置，可以通过签到获取流量。</div>
      	                        <div>2. 免费用户和付费用户每次签到可获得30-100M流量，捐助用户每次签到可获得300-400M流量。</div>
      	                        <div>3. 自动清理注册超过一周不验证邮箱和三周不签到的免费用户(不包含付费用户与捐助用户)</div>
      	                        <div class="w3-btn-group" style="margin-top:10px">
        		                        <a href="https://telegram.me/joinchat/BdFlBwGgEH9rdgp5JSaxGA" target="_blank" class="w3-btn w3-green w3-border-white w3-border-right" style="width:33.3%;border-color:white !important">Telegram</a>
        		                        <a href="https://plus.google.com/communities/102799415585211637190" target="_blank" class="w3-btn w3-green w3-border-white w3-border-right w3-border-left" style="width:33.3%;border-color:white !important">G+社群</a>
        		                        <a class="w3-btn w3-green w3-border-white w3-border-left" target="_blank" href="http://shang.qq.com/wpa/qunwpa?idkey=c49710b2362e96840cd04aee8185cd10ad4132f5746f8041e2eb9b76dbc3e2d3" style="width:33.3%;border-color:white !important">QQ群</a>
      	                        </div>
      							<button class="w3-btn w3-teal w3-btn-block" onclick=$("#donateModal").show() style="margin-top:5px">捐助</button>
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

                                <p>上次签到时间：<span class="badge bg-teal">{$user->lastCheckInTime()}</span></p>
                                {if $user->isAbleToCheckin() }
                                    <p id="checkin-btn">
                                        <button id="checkin" class="btn btn-success  btn-flat">签到</button>
                                    </p>
                                {else}
                                    <p><a class="btn btn-success btn-flat disabled" href="#">不能签到</a></p>
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

                                <h3 class="box-title">本月流量使用情况</h3>
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
                                    <dt>(上月末剩余流量)总流量</dt>
                                    <dd>{$user->enableTraffic()}</dd>
                                    <dt>已用流量</dt>
                                    <dd>{$user->usedTraffic()}</dd>
                                    <dt>剩余流量</dt>
                                    <dd>{$user->unusedTraffic()}</dd>
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

                                <h3 class="box-title">连接信息</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <dl class="dl-horizontal">
                                    <dt>端口</dt>
                                    <dd>{$user->port}</dd>
                                    <dt>密码</dt>
                                    <dd>{$user->passwd}</dd>
                                    <!-- <dt>自定义加密方式</dt>
                                    <dd>{$user->method}</dd> -->
                                    <dt>上次使用</dt>
                                    <dd>{$user->lastSsTime()}</dd>
                                    <dt>用户类型</dt>
                                    <dd>
                                        {if $user->plan == "A"}
                                            <span class="badge bg-green">免费用户</span>
                                        {elseif $user->plan == "C"}
                                            <span class="badge bg-green">特殊用户</span>
                                        {else}
                                            <span class="badge bg-green">付费用户</span>
                                        {/if}

                                        {if $user->ref_by == 3}
                                            <span class="badge bg-green">捐助用户</span>
                                        {/if}
                                    </dd>
                                    {if $user->type != 1}
                                    <dt>当前套餐</dt>
                                    <dd>
                                        <span class="badge bg-green">{$user->type}套餐</span>
                                    </dd>
                                    {/if}
                                    {if $user->expire_date != 0}
                                        <dt>到期时间</dt>
                                        <dd><span class="badge bg-teal">{$user->expire_date}</span></dd>
                                    {/if}
                                </dl>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col (right) -->
                </div>
            </div>
        </div>
        <!-- /.row --><!-- END PROGRESS BARS -->
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        $("#checkin").click(function () {
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
        {if $user->status == 0}
            var notifyModal = '<div id="notifyModal" class="w3-modal"><div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%"><header class="w3-container w3-teal"> <span onclick=$("#notifyModal").hide() class="w3-closebtn">×</span><h2>请验证邮箱</h2></header><div class="w3-container"><p style="padding-top:15px">请前往<a href="user/profile" class="w3-btn w3-teal w3-round w3-small w3-ripple">我的信息</a>页面验证邮箱以激活账号，否则节点将不可用。如果从注册时起超过两天不验证邮箱，账号将会被自动删除。</p></div></div></div>';
            $("body").append(notifyModal);
            $("#notifyModal").show();
            $("#notifyModal").css("zIndex", 999);
        {/if}
    })
</script>

{include file='user/footer.tpl'}

<div id="donateModal" class="w3-modal" style="z-index:999">
    <div class="w3-modal-content w3-animate-zoom w3-card-8" style="width:50%">
        <header class="w3-container w3-teal">
            <span onclick=$("#donateModal").hide() class="w3-closebtn">×</span>
            <h3>捐助Shadowsky</h3>
        </header>
        <div class="w3-container">
            <form action=/pay/donatealipayapi.php method=post target="_blank">
                <div class="w3-row">
                        <lable class="w3-label">请输入捐助金额：</label>
                        <input name="WIDreceive_name" type="hidden" value="uid:{$user->id}" >
                        <input class="w3-input" name="WIDprice" type="number" value=2 style="width:20%;display:inline">
                        <button class="w3-btn w3-teal" type="submit">确认</button>
                </div>
            </form>
            <div class="w3-row w3-margin-top">
                {foreach [2,5,10,20] as $a}
                <div class="w3-quarter">
                    <form action=/pay/donatealipayapi.php method=post target="_blank">
                        <input name="WIDreceive_name" type="hidden" value="uid:{$user->id}" >
                        <input type="submit" name="WIDprice" class="w3-btn w3-xxlarge w3-center w3-teal" value={$a}>
                    </form>
                </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>
