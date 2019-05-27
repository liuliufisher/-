<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $seo_admin_title; ?></title>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <meta name="description" content="Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        <!-- Styles -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        <link href="/user_guide/admin/plugins/pace-master/themes/blue/pace-theme-flash.css" rel="stylesheet"/>
        <link href="/user_guide/admin/plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>
        <link href="/user_guide/admin/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/plugins/line-icons/simple-line-icons.css" rel="stylesheet" type="text/css"/> 
        <link href="/user_guide/admin/plugins/offcanvasmenueffects/css/menu_cornerbox.css" rel="stylesheet" type="text/css"/>  
        <link href="/user_guide/admin/plugins/waves/waves.min.css" rel="stylesheet" type="text/css"/>  
        <link href="/user_guide/admin/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/plugins/3d-bold-navigation/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/plugins/slidepushmenus/css/component.css" rel="stylesheet" type="text/css"/> 
        <link href="/user_guide/admin/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet" type="text/css"/>   
        <link href="/user_guide/admin/plugins/metrojs/MetroJs.min.css" rel="stylesheet" type="text/css"/>  
        <link href="/user_guide/admin/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>    
        <!-- Theme Styles -->
        <link href="/user_guide/admin/css/modern.min.css" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/css/themes/green.css" class="theme-color" rel="stylesheet" type="text/css"/>
        <link href="/user_guide/admin/css/custom.css" rel="stylesheet" type="text/css"/>
        
        <script src="/user_guide/admin/plugins/3d-bold-navigation/js/modernizr.js"></script>
        <script src="/user_guide/admin/plugins/offcanvasmenueffects/js/snap.svg-min.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="page-header-fixed">
        <main class="page-content content-wrap">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="logo-box">
                        <a href="/admin" class="logo-text"><span><?php echo WEB_SITE;?></span></a>
                    </div><!-- Logo Box -->
                    <div class="topmenu-outer">
                        <div class="top-menu">
                            <ul class="nav navbar-nav navbar-left">
                                <li>        
                                    <a href="javascript:void(0);" class="waves-effect waves-button waves-classic sidebar-toggle"><i class="fa fa-bars"></i></a>
                                </li>
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle waves-effect waves-button waves-classic" data-toggle="dropdown">
                                        <span class="user-name"><?php echo $login_system_user->name;?><i class="fa fa-angle-down"></i></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-list" role="menu">
                                        <li role="presentation">
                                            <a href="/privilege/account_info"><i class="fa fa-user"></i>个人资料</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="/privilege/edit_password"><i class="fa fa-gear"></i>修改密码</a>
                                        </li>
                                        <li role="presentation">
                                            <a href="/privilege/login_out"><i class="fa fa-sign-out m-r-xs"></i>安全退出</a>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="/privilege/login_out" class="log-out waves-effect waves-button waves-classic">
                                        <span><i class="fa fa-sign-out m-r-xs"></i>安全退出</span>
                                    </a>
                                </li>
                            </ul><!-- Nav -->
                        </div><!-- Top Menu -->
                    </div>
                </div>
            </div><!-- Navbar -->
            <div class="page-sidebar sidebar">
                <div class="page-sidebar-inner slimscroll">
                    <ul class="menu accordion-menu">
                        <li class="droplink <?php if($my_module == ACCOUNT) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>后台账户权限</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == ACCOUNT) echo 'class="active"'; ?>><a href="/privilege/user_list">账户列表</a></li>
                            </ul>
                        </li>
                        <li class="droplink <?php if($my_module == SETTING || $my_module == BANNER) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>系统设置</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == BANNER) echo 'class="active"'; ?>><a href="/admin/banner/banner_list">轮播图列表</a></li>
                            </ul>
                        </li>

                        <li class="droplink <?php if($my_module == USER) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>用户管理</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == USER) echo 'class="active"'; ?>><a href="/admin/user/user_list">用户列表</a></li>
                            </ul>
                        </li>

                        <li class="droplink <?php if($my_module == SHOP) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>店铺管理</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == SHOP) echo 'class="active"'; ?>><a href="/admin/shop/shop_list">店铺列表</a></li>
                            </ul>
                        </li>

                        <li class="droplink <?php if($my_module == ORDER || $my_module == USER_ORDER || $my_module == SHOP_ORDER) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>订单管理</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == USER_ORDER) echo 'class="active"'; ?>><a href="/admin/order/user_order_list">会员订单列表</a></li>
                                <li <?php if($my_module == SHOP_ORDER) echo 'class="active"'; ?>><a href="/admin/order/shop_order_list">商品订单列表</a></li>
                            </ul>
                        </li>

                        <li class="droplink <?php if($my_module == GOODS) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>商品管理</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == GOODS) echo 'class="active"'; ?>><a href="/admin/goods/goods_list">商品列表</a></li>
                            </ul>
                        </li>

                        <li class="droplink <?php if($my_module == WITHDRAW) echo 'open'; ?>">
                            <a href="#" class="waves-effect waves-button">
                                <span class="menu-icon fa fa-user"></span>
                                <p>提现管理</p><span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li <?php if($my_module == WITHDRAW) echo 'class="active"'; ?>><a href="/admin/withdraw/withdraw_list">提现申请列表</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!-- Page Sidebar Inner -->
            </div><!-- Page Sidebar -->
            <div class="page-inner">
                
                <?php include_once $template_content; ?>

                <div class="page-footer">
                    <p class="no-s">2019 &copy; <?php echo WEB_SITE;?>.</p>
                </div>
            </div><!-- Page Inner -->
        </main><!-- Page Content -->
        <div class="cd-overlay"></div>

        <!-- Javascripts -->
        <script src="/user_guide/admin/plugins/jquery/jquery-2.1.4.min.js"></script>
        <script src="/user_guide/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="/user_guide/admin/plugins/pace-master/pace.min.js"></script>
        <script src="/user_guide/admin/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="/user_guide/admin/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="/user_guide/admin/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="/user_guide/admin/plugins/switchery/switchery.min.js"></script>
        <script src="/user_guide/admin/plugins/uniform/jquery.uniform.min.js"></script>
        <script src="/user_guide/admin/plugins/offcanvasmenueffects/js/classie.js"></script>
        <script src="/user_guide/admin/plugins/offcanvasmenueffects/js/main.js"></script>
        <script src="/user_guide/admin/plugins/waves/waves.min.js"></script>
        <script src="/user_guide/admin/plugins/jquery-mockjax-master/jquery.mockjax.js"></script>
        <script src="/user_guide/admin/plugins/moment/moment.js"></script>
        <script src="/user_guide/admin/plugins/datatables/js/jquery.datatables.min.js"></script>
        <script src="/user_guide/admin/plugins/x-editable/bootstrap3-editable/js/bootstrap-editable.js"></script>
        <script src="/user_guide/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="/user_guide/admin/js/pages/table-data.js"></script>
        <script src="/user_guide/admin/plugins/3d-bold-navigation/js/main.js"></script>
        <script src="/user_guide/admin/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="/user_guide/admin/plugins/jquery-counterup/jquery.counterup.min.js"></script>
        <script src="/user_guide/admin/plugins/toastr/toastr.min.js"></script>
        <script src="/user_guide/admin/plugins/flot/jquery.flot.min.js"></script>
        <script src="/user_guide/admin/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="/user_guide/admin/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="/user_guide/admin/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="/user_guide/admin/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="/user_guide/admin/plugins/curvedlines/curvedLines.js"></script>
        <script src="/user_guide/admin/plugins/metrojs/MetroJs.min.js"></script>
        <script src="/user_guide/admin/js/modern.min.js"></script>
        <script src="/user_guide/admin/js/pages/dashboard.js"></script>
        <script src="/user_guide/admin/plugins/datetimepicker/datetimepicker.js"></script>
    </body>
</html>