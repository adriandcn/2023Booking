var abApp = abApp || {};
var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		$(".pj-table tbody").on("mouseenter", "tr", function () {
			$(this).addClass("pj-table-row-hover");
		}).on("mouseleave", "tr", function () {
			$(this).removeClass("pj-table-row-hover");
		})
		;
		$(".pj-button").hover(
			function () {
				$(this).addClass("pj-button-hover");
			}, 
			function () {
				$(this).removeClass("pj-button-hover");
			}
		);
		$(".pj-checkbox").hover(
			function () {
				$(this).addClass("pj-checkbox-hover");
			}, 
			function () {
				$(this).removeClass("pj-checkbox-hover");
			}
		);
		$("#content").on("click", ".notice-close", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).closest(".notice-box").fadeOut();
			return false;
		});
		
		$("#leftmenu").on("change", ".setForeignId", function () {
			var cid = $("option:selected", this).val();
			if (cid !== "" && cid.length > 0) {
				var $this = $(this);
				window.location.href = ["index.php?controller=pjAdmin&action=pjActionRedirect&nextController=", $this.data("controller"), "&nextAction=", $this.data("action"), "&calendar_id=", cid, "&nextParams=", encodeURIComponent("id=" + cid)].join("");
			}
		});
	});
})(jQuery_1_8_2);