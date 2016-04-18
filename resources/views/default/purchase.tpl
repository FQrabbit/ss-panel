{include file='header.tpl'}
<div class="section no-pad-bot" id="index-banner">
    <div class="container">
        <br><br>
        <div class="row center">
            <h5>购买说明</h5>
            <p>{$msg}</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="section"> 
        <!--   Icon Section   -->
        <div class="row">
            <div class="row marketing">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <caption class="center"><h5><strong>按流量购买</strong></h5></caption>
                        <thead>
                            <tr>
                                <th>套餐名</th>
                                <th>流量</th>
                                <th>价格</th>
                                <th>期限</th>
                                <th>节点</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $menu1 as $menu}
                                <tr>
                                    <td>{$menu["name"]}</td>
                                    <td>{$menu["transfer"]}</td>
                                    <td>￥{$menu["price"]}</td>
                                    <td>{$menu["time"]}</td>
                                    <td>免费节点+付费节点</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
					<br />
                    <table class="table table-striped">
                        <caption class="center"><h5><strong>按时间购买</strong></h5></caption>
                        <thead>
                        <tr>
                            <th>套餐名</th>
                            <th>每月流量</th>
                            <th>价格</th>
                            <th>期限</th>
                            <th>节点</th>
                        </tr>
                        </thead>
                        <tbody>
                            {foreach $menu2 as $menu}
                                <tr>
                                    <td>{$menu["name"]}</td>
                                    <td>无限</td>
                                    <td>￥{$menu["price"]}</td>
                                    <td>{$menu["time"]}</td>
                                    <td>免费节点+付费节点</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>  
</div>
{include file='footer.tpl'}