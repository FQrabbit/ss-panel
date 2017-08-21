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
    var xlm_wid='{$xlm["id"]}';
    var xlm_url='https://www.xianliao.me/';
    var xlm_uid='{$user->id}'; //登录用户的ID，游客使用0
    var xlm_name='{$user->user_name}'; //登录用户的用户名，游客使用空字符
    var xlm_avatar='{$user->gravatar}';//登录用户的头像URL，游客使用空字符
    var xlm_time='{time()}'; //现在服务器的Linux timestamp, 如：1481673726
    var xlm_hash='{$xlm["hash"]}'; //为保障用户的登录安全，xlm_hash须在后台生成，见下附的xlm_hash的生成方法
</script>
<script type='text/javascript' charset='UTF-8' src='https://www.xianliao.me/embed.js'></script>

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
