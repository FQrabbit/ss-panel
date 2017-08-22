<footer class="main-footer">
    <div align="center">
        {$userFooter}
    </div>
    <div class="pull-right hidden-xs">
        Made with Love
    </div>
    Copyright &copy; 2015-{date('Y')} <strong><a href="/">{$config['appName']}</a> </strong>
    All rights reserved. Powered by ss-panel {$config['version']} | <a href="/tos">服务条款 </a>
</footer>
</div><!-- ./wrapper -->

<div aria-hidden="true" class="modal fade" id="xlm" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-full">
        <div class="modal-content" style="overflow:hidden">
            <iframe class="iframe-seamless" id="xlmifram" title="Modal with iFrame"></iframe>
        </div>
    </div>
</div>

<div class="bb-wrapper">
    <ul class="bg-bubbles" style="list-style: none">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
<script>
var xlm_mobile_url = '{$xlm["mobile_url"]}';
function showXlm() {
    var xlmifram = document.getElementById('xlmifram');
    if (xlmifram.src) {
        $("#xlm").modal();
    } else {
        document.getElementById('xlmifram').src = xlm_mobile_url;
        $("#xlm").modal();
    }
    $('html').css('overflow-y', 'hidden');
}
if (screen && screen.width > 767) {
    var xlm_wid='{$xlm["id"]}';
    var xlm_url='https://www.xianliao.me/';
    var xlm_uid='{$user->id}';
    var xlm_name='{$user->user_name}-{$user->id}';
    var xlm_avatar='{$user->gravatar}';
    var xlm_time='{time()}';
    var xlm_hash='{$xlm["hash"]}';
    var s = document.createElement("script");
    s.type = "text/javascript";
    s.charset = "UTF-8";
    s.src = "https://www.xianliao.me/embed.js";
    $("head").append(s);
}
</script>

<!-- jQuery 2.1.3 -->
<script src="/assets/public/js/jquery.min.js"></script>
{if $requireJQueryConfirm}
<!-- jquery-confirm -->
<script src="/assets/public/js/jquery-confirm.js"></script>
{/if}
<!-- Bootstrap 3.3.2 JS -->
<script src="/assets/public/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<!-- <script src="/assets/public/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script> -->
<!-- FastClick -->
<script src='/assets/public/plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="/assets/public/js/app.min.js"></script>
<!-- Clipboard -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.6.1/clipboard.min.js"></script>
<script src="/assets/public/js/main.js"></script>
{if $requireJQueryDatatable}
<!-- DataTables -->
<script charset="utf8" src="/assets/public/js/jquery.dataTables.min.js"></script>
{/if}
{if $requireChartjs}
<!-- chart -->
<script src="/assets/public/js/Chart.min.js"></script>
{/if}
<div style="display:none;">
    {$analyticsCode}
</div>
</body>
</html>
