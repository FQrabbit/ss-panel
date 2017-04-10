{include file='user/main.tpl'}
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- START PROGRESS BARS -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary left-border">
                    <div class="w3-padding">
                        <h4>
                            注意!
                        </h4>
                        {$msg}
                        <div class="w3-bar">
                            <a class="w3-btn w3-teal w3-small w3-round w3-margin" href="{$android_add}" id="btn1">
                                手机原版客户端导入所有节点
                            </a>
                            <a class="w3-btn w3-teal w3-small w3-round w3-margin" href="{$android_add_new}" id="btn1">
                                手机SSR客户端导入所有节点
                            </a>
                            <a class="w3-btn w3-teal w3-small w3-round w3-margin" href="./getconf" id="btn3">
                                下载pc配置文件
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h3>
                            节点列表
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped node-list">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Vote
                                                </th>
                                                <th id="t-name">
                                                    节点名
                                                </th>
                                                <th id="t-status">
                                                    状态
                                                </th>
                                                <th id="t-online">
                                                    在线
                                                </th>
                                                <th id="t-percent">
                                                    流量使用情况
                                                </th>
                                                <th id="t-traffic">
                                                    本日产生流量
                                                </th>
                                                <th id="t-traffic-reset-day">
                                                    流量重置日
                                                </th>
                                                <th id="t-info">
                                                    说明
                                                </th>
                                                <th id="t-uptime">
                                                    负载
                                                </th>
                                                <th id="t-ip">
                                                    ip地址
                                                </th>
                                                <th id="t-ip">
                                                    ipv6地址
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
		                    {foreach $nodes as $node}
								{if $user->plan == "A" and $node->type == 1}
                                            <tr>
                                                <td>
                                                    <button class="fa fa-thumbs-up vote-btn like-btn">
                                                        <span>
                                                            {$node->getPollCount(1)}
                                                        </span>
                                                        <span class="sr-only">
                                                            {$node->id}
                                                        </span>
                                                    </button>
                                                    <br>
                                                        <button class="fa fa-thumbs-down vote-btn dislike-btn">
                                                            <span>
                                                                {$node->getPollCount(-1)}
                                                            </span>
                                                            <span class="sr-only">
                                                                {$node->id}
                                                            </span>
                                                        </button>
                                                    </br>
                                                </td>
                                                <td>
                                                    {$node->name}
                                                </td>
	                                {if $node->getOnlineUserCount()!='暂无数据'}
                                                <td>
                                                    <span class="label" style="background-color:#00a65a">
                                                        {$node->status}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-dark-teal">
                                                        {$node->getOnlineUserCount()}
                                                    </span>
                                                </td>
	                                {else}
                                                <td>
                                                    <span class="label" style="background-color:#444">
                                                        维护中
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color:#444">
                                                        {$node->getOnlineUserCount()}
                                                    </span>
				                                </td>
		                            {/if}
                                                <td>
                                                    <div class="progress">
                                                        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="{$node->node_usage}" class="progress-bar progress-bar-{if $node->node_usage < 40}success{elseif $node->node_usage < 60}warning{else}danger{/if} progress-bar-striped" role="progressbar" style="width:{$node->node_usage}%">
                                                            {$node->node_usage}%
                                                            <span class="sr-only">
                                                                {$node->node_usage}% Complete
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {$node->getTrafficFromLogs()}
                                                </td>
                                                <td>
                                                    {$node->transfer_reset_day}
                                                </td>
                                                <td class="info">
                                                    {$node->info} - 总流量: {if $node->transfer == 0}Unlimited{else}{$node->transfer}G{/if}
                                                </td>
                                                <td>
                                                    {$node->getNodeUptime()}
                                                </td>
                                                <td>
                                                    Meow
                                                </td>
                                                <td>
                                                    Meow
                                                </td>
                                            </tr>
	                            {else}
                                            <tr>
                                                <td>
                                                    <button class="fa fa-thumbs-up vote-btn like-btn {if $user->getPollOfNode($node->id) == 1}vote-btn-clicked{/if}">
                                                        <span>
                                                            {$node->getPollCount(1)}
                                                        </span>
                                                        <span class="sr-only">
                                                            {$node->id}
                                                        </span>
                                                    </button>
                                                    <br>
                                                        <button class="fa fa-thumbs-down vote-btn dislike-btn {if $user->getPollOfNode($node->id) == -1}vote-btn-clicked{/if}">
                                                            <span>
                                                                {$node->getPollCount(-1)}
                                                            </span>
                                                            <span class="sr-only">
                                                                {$node->id}
                                                            </span>
                                                        </button>
                                                    </br>
                                                </td>
                                                <td class="node-name" onclick="urlChange('{$node->id}')">
                                                    {$node->name}
                                                </td>
		                            {if $node->getOnlineUserCount()!='暂无数据'}
                                                <td>
                                                    <span class="label" style="background-color:#00a65a">
                                                        {$node->status}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-dark-teal">
                                                        {$node->getOnlineUserCount()}
                                                    </span>
                                                </td>
		                            {else}
                                                <td>
                                                    <span class="label" style="background-color:#444">
                                                        维护中
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color:#444">
                                                        {$node->getOnlineUserCount()}
                                                    </span>
                                                </td>
		                            {/if}
                                                <td>
                                                    <div class="progress">
                                                        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="{$node->node_usage}" class="progress-bar progress-bar-{if $node->node_usage < 40}success{elseif $node->node_usage < 60}warning{else}danger{/if} progress-bar-striped" role="progressbar" style="width:{$node->node_usage}%">
                                                            {$node->node_usage}%
                                                            <span class="sr-only">
                                                                {$node->node_usage}% Complete
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {$node->getTrafficFromLogs()}
                                                </td>
                                                <td>
                                                    {$node->transfer_reset_day}
                                                </td>
                                                <td class="info">
                                                    {$node->info} - 总流量: {if $node->transfer == 0}Unlimited{else}{$node->transfer}G{/if}
                                                </td>
                                                <td>
                                                    {$node->getNodeUptime()}
                                                </td>
                                                <td>
                                                    {$node->ip}
                                                </td>
                                                <td>
                                                    {$node->ipv6}
                                                </td>
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
</div>
<!-- /.content-wrapper -->
<div aria-hidden="true" class="modal fade" id="nodeinfo" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-full">
        <div class="modal-content" style="overflow:hidden">
            <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                <span aria-hidden="true">
                    ×
                </span>
            </button>
            <iframe class="iframe-seamless" id="infoifram" title="Modal with iFrame">
            </iframe>
        </div>
    </div>
</div>
{include file='user/footer.tpl'}
