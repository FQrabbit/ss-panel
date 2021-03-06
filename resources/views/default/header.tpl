<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>{$config["appName"]}</title>
    <!-- CSS fonts.googleapis.com -->
    <link href="//fonts.lug.ustc.edu.cn/icon?family=Material+Icons" rel="stylesheet">
    <link href="/assets/materialize/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/assets/materialize/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link rel="stylesheet" href="/assets/public/css/indexpage.css">
</head>
<body>
<nav class="lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="/" class="brand-logo">{$config["appName"]}</a>
        <ul class="right hide-on-med-and-down">
            <li><a href="/">首页</a></li>
            <li class="toggle">
                <a href=#>客户端下载</a>

                <ul class="content" style="display:none">
                    <li><a href="http://pan.baidu.com/s/1dEu2XhB" target="_blank">Windows客户端</a></li>
                    <li><a href="http://pan.baidu.com/s/1mg4baXE" target="_blank">Android客户端</a></li>
                    <li><a href="http://pan.baidu.com/s/1bnVb4D5">Mac客户端</a>
                    <li class="toggle">
                        <a href="#" class="toggle">iOS客户端</a>
                        <ul class="content" style="display:none" id="iosul">
                            <li><a href="http://apt.thebigboss.org/onepackage.php?bundleid=com.linusyang.shadowsocks">已越狱</a></li>
                            <li><a href="https://itunes.apple.com/tc/app/shadowsocks/id665729974?mt=8">未越狱</a></li>
                        </ul>
                    </li>
                    </li>
                </ul>
            </li>
            <li><a href="/user/purchase">购买</a></li>
            <!-- <li><a href="/code">邀请码</a></li> -->
            {if $user->isLogin}
                <li><a href="/user">用户中心</a></li>
                <li><a href="/user/logout">退出</a></li>
            {else}
                <li><a href="/auth/login">登录</a></li>
                <li><a href="/auth/register">注册</a></li>
            {/if}

        </ul>

        <ul id="nav-mobile" class="side-nav">
            <li><a href="/">首页</a></li>
            <li><a href="/user/clients">客户端下载</a></li>
            <li><a href="/purchase">购买</a></li>
            <!-- <li><a href="/code">邀请码</a></li> -->
            {if $user->isLogin}
                <li><a href="/user">用户中心</a></li>
                <li><a href="/user/logout">退出</a></li>
            {else}
                <li><a href="/auth/login">登录</a></li>
                <li><a href="/auth/register">注册</a></li>
            {/if}
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
    </div>
</nav>
