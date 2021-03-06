<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$config["appName"]}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="theme-color" content="#333">
    <!-- Bootstrap 3.3.2 -->
    <link href="/assets/public/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Theme style -->
    <link href="/assets/public/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="/assets/public/css/w3.css" rel="stylesheet" type="text/css"/> -->
    {if $requireJQueryConfirm}
    <link href="/assets/public/css/jquery-confirm.css" rel="stylesheet" type="text/css"/>
    {/if}
    {if $requireWYSI}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    {/if}
    <link href="/assets/public/css/main.css" rel="stylesheet" type="text/css"/>
    <style>
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 5px;
        white-space: nowrap;
    }
    .jconfirm .jconfirm-box div.content-pane .content{
        min-height: auto;
    }
    </style>

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
                {if $user->isOnline()}
                    <i class="fa fa-circle" style="color:#00a65a !important;"></i> <small>Online</small>
                {else}
                    <i class="fa fa-circle" style="color:#444 !important;"></i> <small>Offline</small>
                {/if}
                </div>
            </div>

            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li>
                    <a href="/admin">
                        <i class="fa fa-dashboard"></i> <span>信息概览</span>
                    </a>
                </li>

                <li>
                    <a href="/admin/config">
                        <i class="fa fa-cubes"></i> <span>站点配置</span>
                    </a>
                </li>
                <li class="treeview">
                    <a href="javascript:void(0)"><i class="fa fa-dashboard"></i> 管理中心 <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
                    <ul class="nav treeview-menu">
                        <li>
                            <a href="/admin/node">
                                <i class="fa fa-sitemap"></i> <span>节点管理</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/user">
                                <i class="fa fa-users"></i> <span>用户管理</span>
                            </a>
                        </li>

                        <!-- <li>
                            <a href="/admin/invite">
                                <i class="fa fa-users"></i> <span>邀请管理</span>
                            </a>
                        </li> -->
                        
                        <li>
                            <a href="/admin/music">
                                <i class="fa fa-music"></i> <span>曲库管理</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="treeview active">
                    <a href="javascript:void(0)"><i class="fa fa-file-text" aria-hidden="true"></i> 记录日志 <i class="fa fa-angle-left pull-right" aria-hidden="true"></i></a>
                    <ul class="nav treeview-menu menu-open">
                        <li>
                            <a href="/admin/trafficlog">
                                <i class="fa fa-history"></i> <span>流量日志</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/checkinlog">
                                <i class="fa  fa-check-square"></i> <span>签到日志</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/purchaselog">
                                <i class="fa fa-shopping-cart"></i> <span>购买日志</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/donatelog">
                                <i class="fa fa-users"></i> <span>捐助日志</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/expenditurelog">
                                <i class="fa fa-archive"></i> <span>支出日志</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="/admin/email">
                        <i class="fa fa-envelope"></i> <span>邮件中心</span>
                    </a>
                </li>

                <li>
                    <a href="/user">
                        <i class="fa fa-reply-all"></i> <span>用户中心</span>
                    </a>
                </li>


            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>
