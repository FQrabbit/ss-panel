<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$pageTitle} - {$config["appName"]}</title>
    <meta name="theme-color" content="#333">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Font Awesome Icons -->
    <link href="/assets/public/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap -->
    <link href="/assets/public/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/public/css/w3.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="/assets/public/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/public/css/main.css" rel="stylesheet" type="text/css"/>
    {if $requireJQueryDatatable}
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/public/css/jquery.dataTables.min.css">
    {/if}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body class="skin-blue">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <a href="/user" class="logo">{$config["appName"]}</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li id="xlm_mobile_li" onclick="showXlm()">
                        <a href="javascript:void(0)">
                            <i class="fa fa-comments-o" aria-hidden="true"></i> <span>闲聊么</span>
                        </a>
                    </li>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{$user->gravatar}" class="user-image" alt="User Image"/>
                            <span class="hidden-xs">{$user->user_name}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{$user->gravatar}" class="img-circle" alt="User Image"/>

                                <p>
                                    {$user->email}
                                    <small>加入时间：{$user->regDate()}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="/user/profile" class="btn btn-default btn-flat">个人信息</a>
                                </div>
                                <div class="pull-right">
                                    <a href="/user/logout" class="btn btn-default btn-flat">退出</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{$user->gravatar}" class="img-circle" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p>{$user->user_name}</p>

                    <small href="#"><i class="fa fa-circle text-success"></i> Online</small>
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
            {foreach $menuList as $item}
                <li{if $uri == $item['uri']} class='active'{/if}>
                    <a href="{$item['uri']}">
                        <i class="fa fa-{$item['icon']}"></i> <span>{$item['name']}</span>
                    </a>
                </li>
            {/foreach}
            {if $user->isAdmin()}
                <li>
                    <a href="/admin">
                        <i class="fa fa-cog"></i> <span>管理面板</span>
                    </a>
                </li>
            {/if}
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
