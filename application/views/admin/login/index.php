<!DOCTYPE html>
<html>
    <head>
        <title><?php echo WEB_SITE;?> | 登录</title>
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
    <body class="page-login">
        <main class="page-content">
            <div class="page-inner">
                <div id="main-wrapper">
                    <div class="row">
                        <div class="col-md-3 center">
                            <div class="login-box">
                                <a href="/admin" class="logo-name text-lg text-center"><?php echo WEB_SITE;?></a><br>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="user_name" placeholder="账号" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password" placeholder="密码" required>
                                </div>
                                <button class="btn btn-success btn-block">登录</button>
                                <p class="text-center m-t-xs text-sm">2019 &copy; <?php echo WEB_SITE;?>.</p>
                            </div>
                        </div>
                    </div><!-- Row -->
                </div><!-- Main Wrapper -->
            </div><!-- Page Inner -->
        </main><!-- Page Content -->
    
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
        <script src="/user_guide/admin/plugins/waves/waves.min.js"></script>
        <script src="/user_guide/admin/js/modern.min.js"></script>
        <script src="/user_guide/admin/plugins/layer/layer.js" type="text/javascript"></script>
        <script>
            //登录
            $(".btn").click(function(){
                var user_name = $("#user_name").val();
                var password = $("#password").val();
                if(user_name == ""){
                    $("#user_name").focus(); 
                }else if(password == ""){
                    $("#password").focus();return; 
                }
                $.ajax({
                    type:'post',
                    dataType:'json',
                    data:'user_name='+user_name+'&password='+password,
                    url: "/admin/login/ajax_login",
                    success:function(json){
                        if(json.error_code == 0){
                            layer.msg(json.error_msg,{icon: 1});
                            location.href = "/admin";
                            return true;
                        }else{
                            layer.msg(json.error_msg,{shift: 6});
                            return false;
                        }
                    }
                })
            });

            $("body").keydown(function() {
                if (event.keyCode == "13") {//keyCode=13是回车键
                    $('.btn').click();
                }
            });
        </script>
    </body>
</html>