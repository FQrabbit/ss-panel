{include file='user/head-info.tpl'}
{if $user->plan == "A" and $node->type == 1}
    <script>
        window.location.href = "/user/node";
    </script>
{/if}
<style>
html{
    margin-right: 0;
}
</style>
<div class="content-wrapper" style="margin:0">
    <section class="content-header">
        <h1>
            节点信息
            <small>Node Info</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- START PROGRESS BARS -->
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <h4>注意!</h4>

                    <p>配置文件以及二维码请勿泄露！</p>
                    <p>手机端也可直接点击二维码。</p>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-code"></i>

                        <h3 class="box-title">配置Json</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <textarea class="form-control" rows="9">{$json_show}</textarea>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-code"></i>

                        <h3 class="box-title">配置地址</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <input id="ss-qr-text" class="form-control" value="{$ssqr}">
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->
            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-qrcode"></i>

                        <h3 class="box-title">原版配置二维码</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="text-center">
                            <a href="{$ssqr}"><div id="ss-qr-y" class="qr-background"></div></a>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->

            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-qrcode"></i>

                        <h3 class="box-title">SSR 旧版(3.8.3之前)配置二维码</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="text-center">
                            <a href="{$ssqr_s}"><div id="ss-qr" class="qr-background"></div></a>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->

            <div class="col-md-4">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-qrcode"></i>

                        <h3 class="box-title">SSR 新版(3.8.3之后)配置二维码</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="text-center">
                            <a href="{$ssqr_s_new}"><div id="ss-qr-n" class="qr-background"></div></a>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <i class="fa fa-qrcode"></i>

                        <h3 class="box-title">Surge配置</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h4>Surge使用步骤</h4>
                                <p>基础配置只需要做一次：
                                <ol>
                                    <li>打开 Surge ，点击右上角“Edit”，点击“Download Configuration from URL”</li>
                                    <li>输入基础配置的地址（或扫描二维码得到地址，复制后粘贴进来），点击“OK”</li>
                                    <li><b>注意：</b>基础配置不要改名，不可以直接启用。</li>
                                </ol>
                                </p>
                                <p>代理配置需要根据不同的节点进行添加：
                                <ol>
                                    <li>点击“New Empty Configuration”</li>
                                    <li>在“NAME”里面输入一个配置文件的名称</li>
                                    <li>点击下方“Edit in Text Mode”</li>
                                    <li>输入代理配置的全部文字（或扫描二维码得到配置，复制后粘贴进来），点击“OK”</li>
                                    <li>直接启用代理配置即可科学上网。</li>
                                </ol>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h4>基础配置</h4>

                                <div class="text-center">
                                    <div id="surge-base-qr" class="qr-background"></div>
                                </div><br>
                                <textarea id="surge-base-text" class="form-control">{$surge_base}</textarea>
                            </div>
                            <div class="col-md-4">
                                <h4>代理配置</h4>

                                <div class="text-center">
                                    <div id="surge-proxy-qr" class="qr-background"></div>
                                </div><br>
                                <textarea id="surge-proxy-text" class="form-control" rows="6">{$surge_proxy}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
        <!-- END PROGRESS BARS -->
    <!-- /.content -->
</div><!-- /.content-wrapper -->
<script src=" /assets/public/js/jquery.qrcode.min.js "></script>
<script>
    var text_qrcode1 = '{$ssqr}';
    jQuery('#ss-qr-y').qrcode({
        "text": text_qrcode1
    });

    var text_qrcode = '{$ssqr_s}';
    jQuery('#ss-qr').qrcode({
        "text": text_qrcode
    });
    
    var text_qrcode2 = '{$ssqr_s_new}';
    jQuery('#ss-qr-n').qrcode({
        "text": text_qrcode2
    });

    var text_surge_base = jQuery('#surge-base-text').val();
    jQuery('#surge-base-qr').qrcode({
        "text": text_surge_base
    });
    var text_surge_proxy = jQuery('#surge-proxy-text').text();
    jQuery('#surge-proxy-qr').qrcode({
        "text": text_surge_proxy
    });
</script>

<script>
    $(document).ready(function(){
        $(".content-wrapper,.main-footer").css("margin-left", 0);

        function hidemodal () {
            $("#nodeinfo").modal("hide");
        }
    })
</script>
