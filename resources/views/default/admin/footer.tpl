<footer class="main-footer">
    <div class="pull-right hidden-xs">
        Made with Love
    </div>
    <strong>Copyright &copy; {date('Y')} <a href="/">{$config['appName']}</a> </strong>
    All rights reserved. Powered by ss-panel {$config['version']} | <a href="/tos">服务条款 </a>
</footer>
</div><!-- ./wrapper -->


<!-- jQuery 2.1.3 -->
<script src="/assets/public/js/jquery.min.js"></script>
<!-- jquery-confirm -->
<script src="/assets/public/js/jquery-confirm.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="/assets/public/js/bootstrap.min.js" type="text/javascript"></script>
<!-- datetimepicker -->
<!-- <script src="/assets/public/js/bootstrap-datetimepicker.min.js"></script> -->
<!-- SlimScroll -->
<script src="/assets/public/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src='/assets/public/plugins/fastclick/fastclick.min.js'></script>
<!-- AdminLTE App -->
<script src="/assets/public/js/app.min.js" type="text/javascript"></script>
{if $requireChartjs}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
{/if}
{if $requireWYSI}
<script type="text/javascript" src="/assets/public/js/bootstrap3-wysihtml5.all.js"></script>
{/if}
<div style="display:none;">
    {$analyticsCode}
</div>
</body>
</html>
