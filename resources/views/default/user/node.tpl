{include file='user/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
<!--     <section class="content-header">
        <h1>
            节点列表
            <small>Node List</small>
        </h1>
    </section> -->

    <!-- Main content -->
    <section class="content">
        <!-- START PROGRESS BARS -->
        <div class="row">
            <div class="col-md-12">
                <div class="callout callout-warning">
                    <h4>注意!</h4>
		            <p>{$msg}</p>
                </div>
            </div>
        </div>



		  <div class="row">
		  	<div class="col-md-12">
		  		<div class="panel">
		  			<div class="panel-heading"><a id="android_add" class="btn w3-teal w3-small pull-right">android导入所有节点</a>
		  				<h3>节点列表</h3> 
		  			</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-hover table-striped node-list">
										<thead>
											<tr>
												<th>节点名</th>
												<th>状态</th>
												<th>地址</th>
												<th>15分钟在线人数</th>
												<th>加密</th>
												<th>二维码</th>
												<th>说明</th>
											</tr>
										</thead>
										<tbody>
		{foreach $nodes as $node}
			{if $user->plan == "A" and $node->type == 1}
												<tr>
													<td><b>{$node->name}</b></td>
													<td><span class="label label-success">{$node->status}</span></td>
													<td>付费用户可见</td>
													<td><span class="badge bg-dark-teal">{$node->getOnlineUserCount(15)}</span></td>
													<td>{$node->method}</td>
													<td>
														<span class="qr-toggle"><i class="fa fa-qrcode" aria-hidden="true"></i></span>
													</td>
													<td>{$node->info}</td>
												</tr>
			{else}
												<tr>
													<td onclick="window.document.location='./node/{$node->id}'" style="cursor: pointer;"><b>{$node->name}</b></td>
													<td><span class="label label-success">{$node->status}</span></td>
													<td>{$node->server}</td>
													<td><span class="badge bg-dark-teal">{$node->getOnlineUserCount(15)}</span></td>
													<td>{$node->method}</td>
													<td>
														<span class="qr-toggle"><i class="fa fa-qrcode" aria-hidden="true"></i></span>
														<div class="qrcode" id="{$node->name}"></div>
													</td>
													<td>{$node->info}</td>
												</tr>
			{/if}
		{/foreach}
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
		  		</div>
		  	</div>
		  </div>
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

<script src=" /assets/public/js/jquery.qrcode.min.js "></script>
<script>
	$("#android_add").click(function(){
		var links = new Array({$android_add});
		for(var i=0; i<links.length; i++){
			window.open(links[i]);
		}
	});
	$(".qr-toggle").click(function(){
		$(this).next("div").toggle();
		$(this).children().toggleClass("text-teal larger-text");
	})
	$(".qrcode").click(function(){
		$(this).hide();
		$(this).prev().children().toggleClass("text-teal larger-text");
	})
</script>

<script>
	$(document).ready(function(){
		{foreach $node_to_add as $node}
			jQuery("#{$node->name}").qrcode({
		        "text":  '{$ssqrs[$node->name]}'
		    })
		{/foreach}
	})
</script>

{include file='user/footer.tpl'}
