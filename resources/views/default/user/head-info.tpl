<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$config["appName"]}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link href="/assets/public/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css"/>
    <!-- Bootstrap -->
    <link href="/assets/public/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="/assets/public/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- Ionicons -->
    <link href="/assets/public/css/ionicons.min.css" rel="stylesheet">
    <!-- Theme style -->
    <link href="/assets/public/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>

    <link href="/assets/public/css/main.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/public/css/w3.css" rel="stylesheet" type="text/css"/>
    <!-- jQuery 2.1.3 -->
    <script src="/assets/public/js/jquery.min.js"></script>
    {if isset($url) }
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/public/css/jquery.dataTables.min.css">
    <!-- DataTables -->
    <script type="text/javascript" charset="utf8" src="/assets/public/js/jquery.dataTables.min.js"></script>
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
