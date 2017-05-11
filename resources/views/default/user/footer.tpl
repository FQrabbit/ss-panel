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
    <ul class="bg-bubbles">
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

<!-- jQuery 2.1.3 -->
<script src="/assets/public/js/jquery.min.js"></script>
{if $requireJQueryConfirm}
<!-- jquery-confirm -->
<script src="/assets/public/js/jquery-confirm.js"></script>
{/if}
<!-- Bootstrap 3.3.2 JS -->
<script src="/assets/public/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/assets/public/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/assets/public/plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="/assets/public/js/app.min.js" type="text/javascript"></script>
<script src="/assets/public/js/main.js"></script>
{if $requireJQueryDatatable}
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="/assets/public/js/jquery.dataTables.min.js"></script>
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
