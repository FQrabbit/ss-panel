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
                                    <img src="/assets/public/images/ali-qr.png" alt="二维码" style="width:200px;margin:0">
                                </div>
                            </div>
                        </dov>
                        <div class="table-responsive">
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
                                {foreach $products as $product}
                                    {if $product->type != 'A'}
                                        <tr>
                                            <td>{$product->description}</td>
                                            <td>{$product->transfer}G</td>
                                            <td>￥{$product->price}</td>
                                            <td>{$product->period_for_show}</td>
                                            <td>免费节点+付费节点</td>
                                            <td>
                                                <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                                                    <input type="hidden" name="uid" value={$user->id}>
                                                    <input type="hidden" name="total" value={$product->price}>
                                                    <input type="hidden" name="product_id" value={$product->id}>
                                                    <input type="submit" value="购买" class="btn btn-default btn-flat">
                                                </form>
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                            </tbody>
                            </table>
                        </div>

                        <hr />

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption class="center"><h4><strong>按时间购买 </strong></h4></caption>
                                <thead>
                                <tr>
                                    <th>套餐名</th>
                                    <th>每月流量</th>
                                    <th>价格</th>
                                    <th>节点</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $products as $product}
                                    {if $product->type == 'A'}
                                        <tr>
                                            <td>{$product->description}</td>
                                            <td>{$product->transfer}G</td>
                                            <td>￥{$product->price}</td>
                                            <td>免费节点+付费节点</td>
                                            <td>
                                                <form name="alipaypay" method="post" accept-charset="utf-8" action="/prepay" target="_blank">
                                                    <input type="hidden" name="uid" value={$user->id}>
                                                    <input type="hidden" name="total" value={$product->price}>
                                                    <input type="hidden" name="product_id" value={$product->id}>
                                                    <input type="submit" value="购买" class="btn btn-default btn-flat">
                                                </form>
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
{include file='user/footer.tpl'}
