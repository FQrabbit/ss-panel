{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            问题反馈
            <small>Q&A</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">

		<div id="disqus_thread"></div>

		<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		var disqus_shortname = 'shadowsky'; // required: replace example with your forum shortname

		var disqus_config = function () {
		this.page.url = "https://www.shdowsky.info/user/qna";  // Replace PAGE_URL with your page's canonical URL variable
		this.page.identifier = "/user/qna"; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
		};
		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function() {
		var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
		(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();

		</script>
		<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

{include file='user/footer.tpl'}