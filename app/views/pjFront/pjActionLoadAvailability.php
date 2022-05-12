<?php
$index = rand(1,99999);
?>
<div id="abAvailability_<?php echo $index; ?>" class="abAvailability"></div>
<script type="text/javascript">
var pjQ = pjQ || {},
	ABCalendarAvailability_<?php echo $index; ?>;
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
		index: <?php echo $index; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : 'null'; ?>,
		year: <?php echo isset($_GET['year']) && preg_match('/^(19|20)\d{2}$/', $_GET['year']) ? (int) $_GET['year'] : date("Y"); ?>,
		month: <?php echo isset($_GET['month']) && preg_match('/^(0?[1-9]|1[012])$/', $_GET['month']) ? (int) $_GET['month'] : date("n"); ?>
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
			loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjABCalendar.Availability.js", function () {
				ABCalendarAvailability_<?php echo $index; ?> = new ABCalendarAvailability(options);
			});
		});
	});
})();
</script>