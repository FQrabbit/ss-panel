<footer class="page-footer black">
	<div class="container">
		<div class="row">
            <div class="col l3 m6 s12">
                <h5 class="text-lighten-4">关于</h5>
                <p class="text-lighten-4">本站提供某种帐号用于科学上网.</p>
            </div>
			<div class="col l3 m6 s12">
				<h5 class="text-lighten-4">用户</h5>
				<ul>
				{if $user->isLogin}
					<li><a class="text-lighten-4" href="/user">用户中心</a></li>
					<li><a class="text-lighten-4" href="/user/logout">退出</a></li>
				{else}
					<li><a class="text-lighten-4" href="/auth/login">登录</a></li>
					<li><a class="text-lighten-4" href="/auth/register">注册</a></li>
				{/if}
				</ul>
			</div>
			<div class="col l3 m6 s12">
				<h5 class="text-lighten-4">页面</h5>
				<ul>
					<li><a class="text-lighten-4" href="/code">邀请码</a></li>
					<li><a class="text-lighten-4" href="/tos">TOS</a></li>
				</ul>
			</div>
            <div class="col l3 m6 s12">
                <h5 class="text-lighten-4">联系</h5>
                <ul>
                	<li>邮箱: <a href="mailto:shadowskyinfo@gmail.com">shadowskyinfo@gmail.com</a></li>
			<li>Telegram: <a href="https://telegram.me/joinchat/BdFlBwGgEH9rdgp5JSaxGA">shadowsky</a></li>
                </ul>
            </div>
		</div>
	</div>
	<div class="footer-copyright">
		<div class="container">
			&copy; {$config["appName"]}  Powered by ss-panel {$config["version"]}
		 Theme by <a class="text-lighten-3" href="http://materializecss.com">Materialize</a>
		</div>
		<div style="display:none;">
			{$analyticsCode}
		</div>
	</div>
</footer>


<!--  Scripts-->
<!-- <script src="/assets/public/js/jquery.min.js"></script> -->
<script src="/assets/public/js/jquery.min.js"></script>
<script src="/assets/materialize/js/materialize.min.js"></script>
<script src="/assets/materialize/js/init.js"></script>
<script>
	$(document).ready(function() {
			$('.toggle').mouseover(function(){
					$(this).children('ul').show();
			})
			$('.toggle').mouseout(function(){
					$(this).children('ul').hide();
			})
	});
</script>
</body>
</html>
