{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            购买说明
            <small>Buy</small>
        </h1>
    </section>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-body">
                        <p>{$msg}</p>
                        <table class="table table-striped table-hover">
                            <caption class="center"><h4><strong>按流量购买</strong></h4></caption>
                            <thead>
                                <tr>
                                    <th>套餐名</th>
                                    <th>流量</th>
                                    <th>价格</th>
                                    <th>期限</th>
                                    <th>节点</th>
                                    <!-- <th>数量</th> -->
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
                                        <!-- <td>
                                            <form name="alipayment" action="/pay/alipayapi.php" method="post" target="_blank">
                                                <input name="WIDsubject" type="hidden" value="{$menu["name"]}" />
                                                <input name="WIDbody" type="hidden" value="{$menu["body"]}" />
                                                <input name="WIDreceive_name" type="hidden" value="uid:{$user["id"]}" />
                                                <input name="WIDquantity" type="number" value="1"  style="width:50px"/>
                                                <input type="submit" value="购买" class="btn btn-default btn-flat">
                                            </form>
                                        </td> -->
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>

                        <hr />

                        <table class="table table-striped table-hover">
                            <caption class="center"><h4><strong>按时间购买 </strong>{if $B_able_to_buy==0 }<small>(out of stock)</small>{/if}</h4></caption>
                            <thead>
                            <tr>
                                <th>套餐名</th>
                                <th>每月流量</th>
                                <th>价格</th>
                                <th>期限</th>
                                <th>节点</th>
                                <!-- <th></th> -->
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
                                        <!-- <td>
                                            <form name="alipayment" action="/pay/alipayapi.php" method="post" target="_blank">
                                                <input name="WIDsubject" type="hidden" value="{$menu["name"]}" />
                                                <input name="WIDbody" type="hidden" value="{$menu["body"]}" />
                                                <input name="WIDreceive_name" type="hidden" value="uid:{$user["id"]}" />
                                                <input type="submit" value="购买" class="btn btn-default btn-flat" 
                                                        {if $B_able_to_buy==0 }disabled="disabled"{/if}>
                                            </form>
                                        </td> -->
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{include file='user/footer.tpl'}
