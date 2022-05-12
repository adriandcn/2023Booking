<div id="abWrapper_<?php echo $_GET['cid']; ?>" class="abWrapper<?php echo (int) @$_GET['view'] > 1 ? ' abWrapper13' : NULL; ?>">
	<div id="abLoader_<?php echo $_GET['cid']; ?>" class="abLoader">
		<div class="abLoaderInner">
		  	<div class="spinner-container container1">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container2">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container3">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
	  	</div>
	  	<span class="abLoaderMessage"></span>
	</div>
	<div id="abCalendar_<?php echo $_GET['cid']; ?>" class="abCalendar"></div>
</div>
<?php
$front_err = str_replace(array('"', "'"), array('\"', "\'"), __('front_err', true, true));
$days = str_replace(array('"', "'"), array('\"', "\'"), __('days', true, true));
?>
<script type="text/javascript">
var pjQ = pjQ || {},
	ABCalendar_<?php echo $_GET['cid']; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor);
	var loadScript = function(url, callback) {
		var scr = document.getElementsByTagName("script"),
			s = scr[scr.length - 1],
			script = document.createElement("script");
		script.type = "text/javascript";
		if (script.readyState) {
			script.onreadystatechange = function () {
				if (script.readyState == "loaded" || script.readyState == "complete") {
					script.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			script.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		script.src = url;
		s.parentNode.insertBefore(script, s.nextSibling);
	},
	isMSIE = function() {
		var ua = window.navigator.userAgent,
        	msie = ua.indexOf("MSIE ");

        if (msie !== -1) {
            return true;
        }

		return false;
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id", "<?php echo session_id(); ?>");
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		cid: <?php echo $_GET['cid']; ?>,
		view: <?php echo isset($_GET['view']) && (int) $_GET['view'] > 0 ? (int) $_GET['view'] : 1; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : 'null'; ?>,
		index: <?php echo isset($_GET['index']) && (int) $_GET['index'] > 0 ? (int) $_GET['index'] : 0; ?>,
		year: <?php echo isset($_GET['year']) && preg_match('/^(19|20)\d{2}$/', $_GET['year']) ? (int) $_GET['year'] : date("Y"); ?>,
		month: <?php echo isset($_GET['month']) && preg_match('/^(0?[1-9]|1[012])$/', $_GET['month']) ? (int) $_GET['month'] : date("n"); ?>,

		booking_behavior: <?php echo (int) @$tpl['option_arr']['o_booking_behavior']; ?>,
		price_based_on: "<?php echo @$tpl['option_arr']['o_price_based_on']; ?>",
		price_plugin: "<?php echo @$tpl['option_arr']['o_price_plugin']; ?>",
		accept_bookings: <?php echo (int) @$tpl['option_arr']['o_accept_bookings']; ?>,
		show_prices: <?php echo (int) @$tpl['option_arr']['o_show_prices']; ?>,
		week_start: <?php echo (int) @$tpl['option_arr']['o_week_start']; ?>,
		date_format: "<?php echo @$tpl['option_arr']['o_date_format']; ?>",
		thankyou_page: "<?php echo @$tpl['option_arr']['o_thankyou_page']; ?>",
		limits: <?php echo pjAppController::jsonEncode($tpl['limit_arr']); ?>,
		days: <?php echo pjAppController::jsonEncode($days); ?>,
		error_msg: <?php echo pjAppController::jsonEncode($front_err); ?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();

	?>
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('storage_polyfill'); ?>storagePolyfill.min.js", function () {
		if (isSafari) {
			createSessionId();
			options.session_id = getSessionId();
		}else{
			options.session_id = "";
		}
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
			window.pjQ.$.browser = {
				msie: isMSIE()
			};
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjABCalendar.js", function () {
					ABCalendar_<?php echo $_GET['cid']; ?> = new ABCalendar(options);
				});
			});
		});
	});
})();
</script>