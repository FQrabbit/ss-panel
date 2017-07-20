{include file='user/main.tpl'}
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            购买说明
            <small>
                Buy
            </small>
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
                                <p>如果3分钟内没有自动开通请<a href="mailto:shadowskyinfo@gmail.com"><b>联系﻿站长</b></a></p>
                                <p>如发现滥用行为账号将被禁用。</p>
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="center" style="width:200px;background-color:white">
                                    <img alt="二维码" src="/assets/public/images/ali-qr.png" style="width:200px;margin:0">
                                    </img>
                                </div>
                            </div> -->
                        </dov>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption class="center">
                                    <h4>
                                        <strong>
                                            按流量购买
                                        </strong>
                                    </h4>
                                </caption>
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
                                    {if $product->isByMete() || $product->type == 'AB'}
                                    <tr>
                                        <td>{$product->description}</td>
                                        <td>{$product->transfer}G</td>
                                        <td>￥{$product->price}</td>
                                        <td>{$product->period_for_show}</td>
                                        <td>免费节点+付费节点</td>
                                        <td>
                                            <form accept-charset="utf-8" action="/prepay" method="post" name="alipaypay" target="_blank">
                                                <input name="uid" type="hidden" value="{$user->id}">
                                                <input name="total" type="hidden" value="{$product->price}">
                                                <input name="product_id" type="hidden" value="{$product->id}">
                                                <button class="btn btn-default btn-flat" type="submit">购买</button>
                                            </form>
                                        </td>
                                    </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <hr/>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption class="center">
                                    <h4>
                                        <strong>按时间购买</strong>
                                    </h4>
                                </caption>
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
                                        <td>{if $product->unlimitTransfer()}不限流量{else}{$product->transfer}G{/if}</td>
                                        <td>￥{$product->price}</td>
                                        <td>免费节点+付费节点</td>
                                        <td>
                                            <form accept-charset="utf-8" action="/prepay" method="post" name="alipaypay" target="_blank">
                                                <input name="uid" type="hidden" value="{$user->id}">
                                                <input name="total" type="hidden" value="{$product->price}">
                                                <input name="product_id" type="hidden" value="{$product->id}">
                                                <button class="btn btn-default btn-flat" type="submit">购买</button>
                                            </form>
                                        </td>
                                    </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <hr/>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <caption class="center">
                                    <h4>
                                        <strong>流量加油包(原套餐为按时间购买)</strong>
                                    </h4>
                                </caption>
                                <thead>
                                    <tr>
                                        <th>套餐名</th>
                                        <th>流量</th>
                                        <th>期限</th>
                                        <th>价格</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                {foreach $products as $product}
                                    {if $product->type == 'C'}
                                    <tr>
                                        <td>{$product->description}</td>
                                        <td>{$product->transfer}G</td>
                                        <td>{$product->period_for_show}</td>
                                        <td>￥{$product->price}</td>
                                        <td>
                                            <form accept-charset="utf-8" action="/prepay" method="post" name="alipaypay" target="_blank">
                                                <input name="uid" type="hidden" value="{$user->id}">
                                                <input name="total" type="hidden" value="{$product->price}">
                                                <input name="product_id" type="hidden" value="{$product->id}">
                                                <button class="btn btn-default btn-flat" type="submit">购买</button>
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
