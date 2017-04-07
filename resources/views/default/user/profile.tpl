{include file='user/main.tpl'}

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            我的信息
            <small>User Profile</small>
        </h1>
    </section>
    <!-- Main content --><!-- Main content -->
    <section class="content">

        <!-- first row -->
        <div class="row">

            <!-- left column -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-user"></i>

                        <h3 class="box-title">我的帐号</h3>
                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>id</dt>
                            <dd>{$user->id}</dd>
                            <dt>用户名</dt>
                            <dd>{$user->user_name}</dd>
                            <dt>邮箱</dt>
                            <dd>{$user->email}</dd>
                            <dt>用户类型</dt>
                            <dd>
                                <span class="badge bg-green">
                                    {$user->getUserClassName()} {if $user->type==1}{else}| {$user->type}{/if}
                                </span>
                                {if $user->isDonator()}
                                <span class="badge bg-green">
                                    捐助用户 | ￥{$user->money}
                                </span>
                                {/if}
                            </dd>
                            {if $user->expire_date != 0}
                                <dt>到期时间</dt>
                                <dd><span class="badge w3-teal">{$user->expire_date}</span></dd>
                            {/if}
                            <br>
                            <dt>端口</dt>
                            <dd>{$user->port}</dd>
                            <dt>密码</dt>
                            <dd>{$user->passwd}</dd>
                            <dt>自定义加密</dt>
                            <dd>{$user->method}</dd>
                            <dt>自定义协议</dt>
                            <dd>{$user->protocol}</dd>
                            <dt>自定义混淆</dt>
                            <dd>{$user->obfs}</dd>
                            <dt>自定义混淆参数</dt>
                            <dd>{$user->obfs_param}</dd>
                        </dl>

                    </div>
                    <!-- /.box -->
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-link"></i>

                        <h3 class="box-title">Shadowsocks连接信息修改</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="config-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="config-msg-error-p"></p>
                                </div>
                                <div id="config-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="config-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前端口</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="ssport" placeholder="{$user->port}" class="form-control" disabled>
                                        <div class="input-group-btn">
                                            <button type="submit" id="portreset" class="btn btn-default btn-flat">重置端口</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">连接密码</label>
                                <div class="col-sm-9">
                                    <input type="text" id="sspwd" value="{$user->passwd}" class="form-control" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">加密方式</label>
                                <div class="col-sm-9">
                                    <select id="method" class="form-control">
                                        <option value="{$user->method}" style="background-color:#009688;" selected="selected">{$user->method} (当前)</option>
                                        <option value="{$user->method}" disabled="disabled">======</option>
                                        {foreach $methods as $method}
                                        <option value="{$method}">{$method}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">协议</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="protocol">
                                        <option value="{$user->protocol}" style="background-color:#009688;" selected="selected">{$user->protocol} (当前)</option>
                                        <option value="{$user->protocol}" disabled="disabled">======</option>
                                        {foreach $protocols as $protocol}
                                        {if strpos($protocol, 'compatible') === false}
                                        <option value="{$protocol}">{$protocol}</option>
                                        {/if}
                                        {/foreach}
                                        <option value="{$user->protocol}" disabled="disabled">==以下兼容原协议（如要使用原版客户端或不支持设置协议的客户端请选择以下中的一个）==</option>
                                        {foreach $protocols as $protocol}
                                        {if strpos($protocol, 'compatible') !== false}
                                        <option value="{$protocol}">{$protocol}</option>
                                        {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">混淆</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="obfs">
                                        <option value="{$user->obfs}" style="background-color:#009688;" selected="selected">{$user->obfs} (当前)</option>
                                        <option value="{$user->obfs}" disabled="disabled">======</option>
                                        {foreach $obfses as $obfs}
                                        {if strpos($obfs, 'compatible') === false}
                                        <option value="{$obfs}">{$obfs}</option>
                                        {/if}
                                        {/foreach}
                                        <option value="{$user->obfs}" disabled="disabled">==以下兼容原协议（如要使用原版客户端或不支持设置混淆的客户端请选择以下中的一个）==</option>
                                        {foreach $obfses as $obfs}
                                        {if strpos($obfs, 'compatible') !== false}
                                        <option value="{$obfs}">{$obfs}</option>
                                        {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">混淆参数</label>
                                <div class="col-sm-9">
                                    <input id="obfs_param" class="form-control" type="text" value="{$user->obfs_param}" placeholder="输入混淆参数，如'cloudflare.com'，请勿乱填。">
                                </div>
                            </div>
                            <p class="center"><a href="https://github.com/breakwa11/shadowsocks-rss/blob/master/ssr.md">==>>>协议插件文档<<<===</a></p>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="config-update" class="btn btn-default btn-flat">修改</button>
                    </div>
                </div>
            </div>
            <!-- /.col (left) -->

            <div class="col-md-6">
            <!-- right column -->
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录密码修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="psw-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="psw-msg-error-p"></p>
                                </div>
                                <div id="psw-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="psw-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">当前密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="当前密码" required="required" id="oldpwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新密码</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="新密码" required="required" id="pwd">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">确认密码</label>

                                <div class="col-sm-9">
                                    <input type="password" placeholder="确认密码" class="form-control" required="required" id="repwd">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="pwd-update" class="btn btn-default btn-flat">修改</button>
                    </div>

                </div>
                
                <div class="box box-primary">
                    <div class="box-header">
                        <i class="fa fa-key"></i>

                        <h3 class="box-title">网站登录邮箱修改</h3>
                    </div>
                    <!-- /.box-header --><!-- form start -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="email-msg-error" class="alert alert-danger alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                                    <p id="email-msg-error-p"></p>
                                </div>
                                <div id="email-msg-success" class="alert alert-success alert-dismissable" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-info"></i> 修改成功!</h4>

                                    <p id="email-msg-success-p"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">新邮箱</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" placeholder="新邮箱" required="required" id="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">邮箱验证码</label>

                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" id="verifycode" class="form-control" placeholder="邮箱验证码"/>
                                        <span class="input-group-btn">
                                            <button type="button" id="sendcode" class="btn btn-default btn-flat">发送验证码</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" id="email-update" class="btn btn-default btn-flat">修改</button>
                    </div>

                </div>
                <!-- /.box -->
            </div>
            <!-- /.col (right) -->

        </div>
    </section>
    <!-- /.content -->
</div><!-- /.content-wrapper -->

{include file='user/footer.tpl'}