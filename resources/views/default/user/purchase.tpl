{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            购买说明
            <small>Buy</small>
        </h1>
    </section>
    <!-- Main content -->
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-body">
                        <!-- <p>支付完成后请<strong>不要立即关闭页面</strong>，请等待跳转至提示开通成功页面，如果没有跳转或遇到其他问题请加群咨询或email反馈。</p> -->
                        <table class="table table-striped">
                            <caption class="center"><h4><strong>按流量购买</strong></h4></caption>
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
                                        <td>
                                            <form name=alipayment action=/pay/alipayapi.php method=post target="_blank">
                                                <input name="WIDsubject" type="hidden" value="{$menu["name"]}" />
                                                <input name="WIDbody" type="hidden" value="{$menu["body"]}" />
                                                <input name="WIDprice" type="hidden" value="{$menu["price"]}.00" />
                                                <input name="WIDreceive_name" type="hidden" value="uid:{$user["id"]}" />
                                                <input type="submit" value="购买" class="btn">
                                            </form>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>

                        <hr />

                        <table class="table table-striped">
                            <caption class="center"><h4><strong>按时间购买</strong></h4></caption>
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
                                        <td>
                                            <form name=alipayment action=/pay/alipayapi.php method=post target="_blank">
                                                <input name="WIDsubject" type="hidden" value="{$menu["name"]}" />
                                                <input name="WIDbody" type="hidden" value="{$menu["body"]}" />
                                                <input name="WIDprice" type="hidden" value="{$menu["price"]}.00" />
                                                <input name="WIDreceive_name" type="hidden" value="uid:{$user["id"]}" />
                                                <input type="submit" value="购买" class="btn">
                                            </form>
                                        </td>
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
