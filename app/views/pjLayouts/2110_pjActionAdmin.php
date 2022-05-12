<!doctype html>
<html>
	<head>
		<title>IguanaTrip - Booking</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />

		<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/dropzone2.css">
        		<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.carousel.css">
                	<link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.theme.css">
                         <link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/owl.transitions.css">
                         <link type="text/css" rel="stylesheet" href="<?php echo PJ_URL_BOOKING; ?>/app/web/css/imageAjax/prettify.css">
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
#footers{
	float: left;
	background-image:url("app/web/img/fondo-menu.png");
	width: 100%;
	height: 30px;
	margin-bottom: 20px;
}
.auspciantes {
    clear: left;
    margin: 0 auto;
    position: relative;
    text-align: center;
    width: 960px;

}
    </style>

	</head>
	<body>
		<div id="container" style="background-color: #323232;">

    <div>
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
                    <div id="menu">
                    <div id="menu-ul">
                        <!--<ul id="seleccionitem">
                            <li><a href="{!!asset('/myProfileOp')!!}">{{ trans('welcome/index.home') }}</a></li>
                            <li><a href="#" onclick="window.location.href = '{!!asset('/myProfileOp')!!}'">Mi perfil</a></li>
                        </ul>-->
                    </div>
                    <div class="sessionName"></div>
                </div>

			<br>
			<div style="text-align: center;">

					<h1 style="text-align: center;font-weight: bold;color:white;font-size: 2vw !important;">
						Booking para: <?php echo $_SESSION['nombre_servicio']; ?> </h1>
			</div>
			</div>

			<div id="middle">
				<div id="leftmenu">
					<?php require PJ_VIEWS_PATH . 'pjLayouts/elements/leftmenu.php'; ?>
				</div>
				<div id="right">
					<div class="content-top"></div>
					<div class="content-middle" id="content">
					<?php require $content_tpl; ?>
					</div>
					<div class="content-bottom"></div>
				</div> <!-- content -->
				<div class="clear_both"></div>
			</div> <!-- middle -->

		</div> <!-- container -->
<div style="width: 100%;background-color: #323232;">
<div class="auspciantes" style="background-color: #323232;">
<img data-u="image" src="app/web/img/internas/logos-base-blanco.png" />
 </div>
 </div>
             <div id="footers">
                <div id="menu-ul">
                    <!--<ul id="seleccionitem">
                    </ul> -->
                </div>
            </div>

               <div class="copyr"  style="background-color: #323232; color: #c8c8c8;">
                  <span id="siteseal"><script type="text/javascript" src="https://seal.starfieldtech.com/getSeal?sealID=bd5KnY2paK8E4hs3jWowi27DXIZfIrdDodFWtM9AqFpjC6kYEYo4NK4wSjVC"></script></span> <span id="uxp_ftr_link_trademark">                  © 2016 iWanaTrip.com Group All rights reserved                </span>
            </div>
<!-- <div id="footer">
 </div> -->

		<!--<div id="footer-wrap">

        		</div> -->

	</body>
</html>