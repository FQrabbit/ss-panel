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
                        <dov class="row">
                            <div class="col-sm-6">
                                <p>{$msg}</p>
                            </div>
                            <div class="col-sm-6">
                                <div class="center" style="width:200px;background-color:white">
                                    <img src="/assets/public/images/ali-qr.png" alt="二维码" style="width:200px">
                                </div>
                            </div>
                        </dov>
                        <table class="table table-striped table-hover">
                            <caption class="center"><h4><strong>按流量购买</strong></h4></caption>
                            <thead>
                                <tr>
                                    <th>套餐名</th>
                                    <th>流量</th>
                                    <th>价格</th>
                                    <th>期限</th>
                                    <th>节点</th>
                                    <th></th>
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
                                            <form name="alipaypay" method="post" accept-charset="utf-8" action="http://senlinpay.com/api.php" target="_blank">
                                                <input type="hidden" name="uid" value="100001627">
                                                <input type="hidden" name="payno" value="zhwalker20@gmail.com">
                                                <input type="hidden" name="price" value="{$menu['price']}">
                                                <input type="hidden" name="title" value="{$menu['title']}">
                                                <input type="submit" value="购买" class="btn btn-default btn-flat" {if !$able}disabled{/if}>
                                            </form>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>

                        <hr />

                        <table class="table table-striped table-hover">
                            <caption class="center"><h4><strong>按时间购买 </strong></h4></caption>
                            <thead>
                            <tr>
                                <th>套餐名</th>
                                <th>每月流量</th>
                                <th>价格</th>
                                <th>期限</th>
                                <th>节点</th>
                                <th></th>
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
                                            <form name="alipaypay" method="post" accept-charset="utf-8" action="http://senlinpay.com/api.php" target="_blank">
                                                <input type="hidden" name="uid" value="100001627">
                                                <input type="hidden" name="payno" value="zhwalker20@gmail.com">
                                                <input type="hidden" name="price" value="{$menu['price']}">
                                                <input type="hidden" name="title" value="{$menu['title']}">
                                                <input type="submit" value="购买" class="btn btn-default btn-flat" {if !$able}disabled{/if}>
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
