<!doctype html>
<html>
	<head>
		<title>iWaNaTrip - Viaja y vive la experiencia de conocer Ecuador - Booking</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

                        <link rel="apple-touch-icon" href="https://iwanatrip.com/public/img/60x60_logo_iwana.png">

        <link rel="apple-touch-icon" sizes="76x76" href="https://iwanatrip.com/public/img/76x76logo_iwana.png">
        <link rel="apple-touch-icon" sizes="120x120" href="https://iwanatrip.com/public/img/120x120logo_iwana.png">
        <link rel="apple-touch-icon" sizes="180x180" href="https://iwanatrip.com/public/img/180x180logo_iwana.png">
        <link rel="icon" sizes="180x180" href="https://iwanatrip.com/public/img/180x180logo_iwana.png">

        <link rel="shortcut icon" href="https://iwanatrip.com/public/favicon.ico" />
        <link rel="apple-touch-icon" href="https://iwanatrip.com/public/images/favicon.png" />        
        
		<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/dropzone2.css">
        		<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.carousel.css">
                	<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.theme.css">
                         <link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.transitions.css">
                         <link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/prettify.css">
                        <link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/style.css">
                         <!--<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
                         <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/dropzone2.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/Compartido.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/jquery-1.9.1.min.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/owl.carousel.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/bootstrap-transition.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/bootstrap-collapse.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/bootstrap-tab.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/prettify.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/imageAjax/application.js"></script>
                          <script src="<?php echo PJ_URL_BOOKING; ?>/app/web/js/sliderTop/jssor.slider.min.js"></script>
                          <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->

		<?php
		foreach ($controller->getCss() as $css)
		{
			echo '<link type="text/css" rel="stylesheet" href="'.(isset($css['remote']) && $css['remote'] ? NULL : PJ_INSTALL_URL).$css['path'].htmlspecialchars($css['file']).'" />';
		}
		foreach ($controller->getJs() as $js)
		{
			echo '<script src="'.(isset($js['remote']) && $js['remote'] ? NULL : PJ_INSTALL_URL).$js['path'].htmlspecialchars($js['file']).'"></script>';
		}
		?>
		<!--[if gte IE 9]>
  		<style type="text/css">.gradient {filter: none}</style>
		<![endif]-->
		<script>
                                jQuery(document).ready(function ($) {

                                    var jssor_1_SlideshowTransitions = [
                                      {$Duration:1800,$Opacity:2}
                                    ];

                                    var jssor_1_options = {
                                      $AutoPlay: true,
                                      $SlideshowOptions: {
                                        $Class: $JssorSlideshowRunner$,
                                        $Transitions: jssor_1_SlideshowTransitions,
                                        $TransitionsOrder: 1
                                      },
                                      $ArrowNavigatorOptions: {
                                        $Class: $JssorArrowNavigator$
                                      },
                                      $BulletNavigatorOptions: {
                                        $Class: $JssorBulletNavigator$
                                      }
                                    };

                                    var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

                                    //responsive code begin
                                    //you can remove responsive code if you don't want the slider scales while window resizing
                                    function ScaleSlider() {
                                        var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
                                        if (refSize) {
                                            refSize = Math.min(refSize, 1360);
                                            jssor_1_slider.$ScaleWidth(refSize);
                                        }
                                        else {
                                            window.setTimeout(ScaleSlider, 30);
                                        }
                                    }
                                    ScaleSlider();
                                    $(window).bind("load", ScaleSlider);
                                    $(window).bind("resize", ScaleSlider);
                                    $(window).bind("orientationchange", ScaleSlider);
                                    //responsive code end
                                });
                        </script>
                        <style>
                             body{
                                    font: 75%/150% "Open Sans", Arial, Helvetica, sans-serif;
                                    color: #939faa;
                                }
                                .jssorb05 {
                                    position: absolute;
                                }
                                .jssorb05 div, .jssorb05 div:hover, .jssorb05 .av {
                                    position: absolute;
                                    /* size of bullet elment */
                                    width: 16px;
                                    height: 16px;
                                    background:url ("app/web/img/internas/b05.png") no-repeat;
                                    overflow: hidden;
                                    cursor: pointer;
                                }
                                .jssorb05 div { background-position: -7px -7px; }
                                .jssorb05 div:hover, .jssorb05 .av:hover { background-position: -37px -7px; }
                                .jssorb05 .av { background-position: -67px -7px; }
                                .jssorb05 .dn, .jssorb05 .dn:hover { background-position: -97px -7px; }
                                .jssora12l, .jssora12r {
                                    display: block;
                                    position: absolute;
                                    /* size of arrow element */
                                    width: 30px;
                                    height: 46px;
                                    cursor: pointer;
                                    background:url("app/web/img/internas/a12.png") no-repeat;
                                    overflow: hidden;
                                }
                                .jssora12l { background-position: -16px -37px; }
                                .jssora12r { background-position: -75px -37px; }
                                .jssora12l:hover { background-position: -136px -37px; }
                                .jssora12r:hover { background-position: -195px -37px; }
                                .jssora12l.jssora12ldn { background-position: -256px -37px; }
                                .jssora12r.jssora12rdn { background-position: -315px -37px; }


/* recent posts */
.recent-posts > li {
  width: 100%;
  margin-bottom: 1px;
  background: #edf6ff;
  padding: 8px;
}
.recent-posts > li:last-child {
  margin-bottom: 0;
}
.recent-posts .post-author-avatar {
  display: table-cell;
  padding-right: 12px;
  vertical-align: middle;
}
.recent-posts .post-author-avatar span {
  border: 4px solid rgba(255, 255, 255, 0.1);
  width: 48px;
  height: 48px;
  -webkit-border-radius: 50% 50% 50% 50%;
  -moz-border-radius: 50% 50% 50% 50%;
  -ms-border-radius: 50% 50% 50% 50%;
  border-radius: 50% 50% 50% 50%;
  display: block;
}
.recent-posts .post-author-avatar img {
  width: 100%;
  height: auto;
  -webkit-border-radius: 50% 50% 50% 50%;
  -moz-border-radius: 50% 50% 50% 50%;
  -ms-border-radius: 50% 50% 50% 50%;
  border-radius: 50% 50% 50% 50%;
  -moz-transition: all 0.25s ease 0s;
  -o-transition: all 0.25s ease 0s;
  -webkit-transition: all 0.25s ease 0s;
  -ms-transition: all 0.25s ease 0s;
  transition: all 0.25s ease 0s;
}
.recent-posts .post-author-avatar:hover img {
  filter: alpha(opacity=80);
  -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
  -moz-opacity: 0.8;
  -khtml-opacity: 0.8;
  opacity: 0.8;
  /*border: 4px solid rgba(red($theme-skin-color), green($theme-skin-color), blue($theme-skin-color), 0.85);*/
}
.recent-posts .post-content {
  display: table-cell;
  vertical-align: middle;
}
.recent-posts .post-title {
  margin-bottom: 4px;
  display: block;
  font-size: 1.1667em;
  color: #1b4268;
}
.recent-posts .post-title:hover {
  color: #ff6600;
}
.recent-posts .post-meta {
  font-size: 0.8333em;
  margin-bottom: 0;
}

                        </style>
                        <style type="text/css">
                                #menu{
                                	float: left;
                                	background-image:url("app/web/img/fondo-menu.png");
                                	width: 100%;
                                	height: 38px;
                                	margin-bottom: 20px;
                                }
                                #menu-ul{

                                	margin-left:234px;
                                	float:left;
                                	padding: 0px;
                                	margin-top:0px;
                                	margin-bottom:5px;
                                	height:37px;
                                }
                                .page-title-container{
                                    background: url("app/web/img/style1-pattern.png") repeat;
                                    position: relative;
                                    overflow: visible;
                                }
                        </style>
	</head>
	<body>
                            <!-- INICIO CONTAINER -->
                	<div id="container" style="background-color: #fff; color:#939faa;">
                                        <div class="page-title-container">
                                                <div id="jssor_1" style="position: relative; margin: 0 auto; top: 0px; left: 0px; width: 1360px; height: 235px; overflow: hidden; visibility: hidden;">
                                                    <!-- Loading Screen -->
                                                    <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
                                                        <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
                                                    </div>
                                                    <div data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 1360px; height: 235px; overflow: hidden;">
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-1.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-11.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-3.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-4.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-5.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-6.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-7.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-8.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-9.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-13.png" />
                                                            </div>
                                                            <div data-p="112.50" style="display: none;">
                                                                <img data-u="image" src="app/web/img/internas/banner-15.png" />
                                                            </div>
                                                    </div>
                                                    <!-- Bullet Navigator -->
                                                    <div data-u="navigator" class="jssorb05" style="bottom:16px;right:16px;" data-autocenter="1">
                                                        <!-- bullet navigator item prototype -->
                                                        <div data-u="prototype" style="width:16px;height:16px;"></div>
                                                    </div>
                                                    <!-- Arrow Navigator -->
                                                    <span data-u="arrowleft" class="jssora12l" style="top:0px;left:0px;width:30px;height:46px;" data-autocenter="2"></span>
                                                    <span data-u="arrowright" class="jssora12r" style="top:0px;right:0px;width:30px;height:46px;" data-autocenter="2"></span>
                                                </div>
                                                    <ul class="breadcrumbs">
                                                        <li><a href="<?php echo PJ_URL_BOOKING.'/index.php?controller=pjAdmin&action=pjActionIndex'; ?>" style="text-decoration: none;">Inicio</a></li>
                                                        <li class="active"><a href="<?php echo PJ_URL_LARAVEL.'/servicios/serviciooperador/'.$_SESSION['usuario_servicio'].'/'.$_SESSION['catalogo']; ?>" style="color:#ff6600;text-decoration: none;"> <?php echo $_SESSION['nombre_servicio']; ?> </a></li>
                                                    </ul>
                            	</div>
                                        <br><br>
                                        <div style="text-align: center;">
                                                <h1 style="text-align: center;font-weight: bold;color: #ff6600 !important;font-size: 2vw !important;">
                                                    Booking para: <?php echo $_SESSION['nombre_servicio']; ?> </h1>
                                        </div>
                                        <!-- INICIO PARTE CENTRAL DEL BOOKING -->
                            	<div id="middle">
                            		<div id="leftmenu">
                            			<?php require PJ_VIEWS_PATH . 'pjLayouts/elements/leftmenu.php'; ?>
                            		</div>
                            		<div id="right" >
                            			<div class="content-top"></div>
                            			<div class="content-middle" id="content" >
                            			<?php require $content_tpl; ?>
                            			</div>
                            			<div class="content-bottom"></div>
                            		</div> <!-- content -->
                            		<div class="clear_both"></div>
                            	</div>
                                        <!-- FIN PARTE CENTRAL DEL BOOKING -->
                	</div>
                        <footer id="footer" class="style4" style="width: 100% !important;height: auto">
                                        <div class="footer-wrapper">
                                            <div class="container">
                                                <div class="row add-clearfix same-height" style="display: block;">
                                                        <div class="col-sm-6 col-md-3" style="width: 20% !important;display: inline-table;margin-left: 5%;">
                                                                    <h5 class="section-title box" style="font-weight: 600;font-size: 14px;line-height: 1.1428em;margin-bottom: 10%;">Recent Posts</h5>
                                                                    <ul class="recent-posts">
                                                                        <li>
                                                                            <a href="#" class="post-author-avatar"><span><img src="http://placehold.it/50x50" alt=""></span></a>
                                                                            <div class="post-content">
                                                                                <a href="#" class="post-title" style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'>Website design trends for 2014</a>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#" class="post-author-avatar"><span><img src="http://placehold.it/50x50" alt=""></span></a>
                                                                            <div class="post-content">
                                                                                <a href="#" class="post-title" style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'>UI experts and modern designs</a>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <a href="#" class="post-author-avatar"><span><img src="http://placehold.it/50x50" alt=""></span></a>
                                                                            <div class="post-content">
                                                                                <a href="#" class="post-title" style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'>Mircale is available in wordpress</a>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                        </div>
                                                        <div class="col-sm-6 col-md-3" style="width: 20% !important;display: inline-table;">
                                                                        <h5 class="section-title box" style="font-weight: 600;font-size: 14px;line-height: 1.1428em;margin-bottom: 10%;">Popular Tags</h5>
                                                                        <div class="tags">
                                                                            <a href="#" class="tag">masonry</a>
                                                                            <a href="#" class="tag">responsive</a>
                                                                            <a href="#" class="tag">Ecommerce</a>
                                                                            <a href="#" class="tag">web design</a>
                                                                            <a href="#" class="tag">wordpres</a>
                                                                            <a href="#" class="tag">mobile</a>
                                                                            <a href="#" class="tag">retina</a>
                                                                            <a href="#" class="tag">multi-purpose</a>
                                                                            <a href="#" class="tag">blog posts</a>
                                                                            <a href="#" class="tag">new sliders</a>
                                                                            <a href="#" class="tag">popular</a>
                                                                            <a href="#" class="tag">recent</a>
                                                                            <a href="#" class="tag">modern</a>
                                                                            <a href="#" class="tag">themeforest</a>
                                                                        </div>
                                                        </div>
                                                        <div class="col-sm-6 col-md-3" style="width: 18% !important;display: inline-table;">
                                                                        <h5 class="section-title box" style="font-weight: 600;font-size: 14px;line-height: 1.1428em;margin-bottom: 10%;">Useful Links</h5>
                                                                        <ul class="arrow useful-links">
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">About SoapTheme</a></li>
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">Video Overview</a></li>
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">Customer Support</a></li>
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">Theme Features</a></li>
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">Breaking News</a></li>
                                                                            <li style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'><a href="#">Upcoming Updates</a></li>
                                                                        </ul>
                                                        </div>
                                                        <div class="col-sm-6 col-md-3" style="width: 22% !important;display: inline-table;">
                                                                    <h5 class="section-title box" style="font-weight: 600;font-size: 14px;line-height: 1.1428em;margin-bottom: 10%;">About iWanaTrip</h5>
                                                                    <div style='display: block; font-size: 1.0833em;line-height: 1.8;margin-bottom: 15px;                                        color: #939faa;font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size: 14px;'>
                                                                        Iguana Trip surge como una alternativa turistica comunitaria para defender y revalorizar los recursos culturales y naturales. Planea tu viaje y aprende sobre la historia y la cultura del país mientras te involucras en la realidad del mismo.
                                                                    </div>
                                                                    <div class="social-icons" style="margin-bottom: 10px;">
                                                                        <a href="#" class="social-icon"><i class="fa fa-twitter has-circle" data-toggle="tooltip" data-placement="top" title="Twitter"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-facebook has-circle" data-toggle="tooltip" data-placement="top" title="Facebook"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-google-plus has-circle" data-toggle="tooltip" data-placement="top" title="GooglePlus"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-linkedin has-circle" data-toggle="tooltip" data-placement="top" title="LinkedIn"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-skype has-circle" data-toggle="tooltip" data-placement="top" title="Skype"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-dribbble has-circle" data-toggle="tooltip" data-placement="top" title="Dribbble"></i></a>
                                                                        <a href="#" class="social-icon"><i class="fa fa-tumblr has-circle" data-toggle="tooltip" data-placement="top" title="Tumblr"></i></a>
                                                                    </div>
                                                                    <div class="tags">
                                                                        <a href="#" class="btn btn-sm style4 tag">Contact Us</a>
                                                                        <a href="#" class="btn btn-sm style4 tag">Purchase</a>
                                                                    </div>

                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="footer-bottom-area">
                                            <div class="container">
                                                <div class="copyright-area">
                                                    <div class="copyright" >
                                                        <div style="display: inline-table;">  &copy; 2015 iWaNaTrip <b>by</b> <a href="http://www.iwanatrip.com"> iWaNaTrip Group</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        </footer>



	</body>
</html>