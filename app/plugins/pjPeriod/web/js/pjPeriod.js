var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			$datepick = $(".datepick"),
			$dialogDeletePeriod = $("#dialogDeletePeriod"),
			dOpts = {};

		if ($datepick.length > 0) {
			dOpts = $.extend(dOpts, {
				firstDay: $datepick.attr("rel"),
				dateFormat: $datepick.attr("rev")
			});
		}
		
		$("#content").on("click", ".btnAddPeriod", function () {
			var $c = $("#periodDefault tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999));
			$("#tblPeriods").find("tbody").append(r);
		}).on("click", ".btnAdultsChildren", function () {
			var $this = $(this),
				name = $this.closest("tr").prevUntil("tr.mainPeriod").last().prev().find("input[name^='start_date']").attr("name"),
				m = name.match(/\[((new_)?\d+)\]/),
				$c = $("#periodAdults").find("tbody").clone(),
				r = $c.html().replace(/\{INDEX\}/g, m !== null ? m[1] : "");
			
			$this.closest("tr").before(r);
			
		}).on("click", ".btnDeletePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogDeletePeriod.length > 0 && dialog) {
				$dialogDeletePeriod.data("link", $(this)).dialog("open");
			}
			return false;
		}).on("click", ".btnRemovePeriod", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr").nextUntil(".mainPeriod").andSelf();
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		}).on("click", ".btnRemoveAdultsChildren", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});			
			return false;
		}).on("focusin", ".datepick", function (e) {
			if (datepicker) {
				$(this).datepicker($.extend(dOpts, {
					beforeShow: function (input, ui) {
						var dt,
							$chain,
							name = ui.input.attr("name"),
							m1 = name.match(/start_date\[((new_)?\d+)\]/),
							m2 = name.match(/end_date\[((new_)?\d+)\]/);
						
						if (m1 !== null) {
							//start_date[3], start_date[new_2434]
							$chain = $("input[name='end_date[" + m1[1] + "]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "maxDate", $chain.val());
							}
						} else if (m2 !== null) {
							//end_date[3], end_date[new_2434]
							$chain = $("input[name='start_date[" + m2[1] + "]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "minDate", $chain.val());
							}
						}
					}
				}));
			}
		}).on("submit", "#frmPeriods", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var post, num,
				i = 0,
				$form = $(this),
				$tbody = $("#tblPeriods tbody"),
				$tr = $tbody.find("tr"),
				$main = $tr.filter(".mainPeriod"),
				len = $main.length,
				perLoop = 1,
				loops = len > perLoop ? Math.ceil(len / perLoop) : 1;
			
			num = loops;

			$form.find(":input").not(".pj-button").attr("readonly", "readonly");
			$form.find(".pj-button").attr("disabled", "disabled");
			
			$form.find(".bxPeriodStatus").hide();
			$form.find(".bxPeriodStatusStart").show();
			$.post("index.php?controller=pjPeriod&action=pjActionDeleteAll").done(function () {
				setPrices.call(null);
			});
	
			function setPrices() {
				$.ajaxSetup({async:false});
				post = $tr.filter(".mainPeriod").eq(i * perLoop).nextUntil(".mainPeriod").andSelf().find(":input").serialize();
				i++;
				$.post("index.php?controller=pjPeriod&action=pjActionSave", post, callback);
			}
			
			function callback() {
				num--;
				if (num > 0) {
			        setPrices.call(null);
			    } else {
			    	$form.find(":input").removeAttr("readonly");
			    	$form.find(".pj-button").removeAttr("disabled");
			    	$form.find(".bxPeriodStatusStart").hide();
			    	$form.find(".bxPeriodStatusEnd").show().fadeOut(2500);
			        return;
			    }
			}
			return false;
		});
		
		if ($dialogDeletePeriod.length > 0 && dialog) {
			$dialogDeletePeriod.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				buttons: {
					"Delete": function () {
						var $this = $(this),
							$link = $this.data("link"),
							$tr = $link.closest("tr").nextUntil(".mainPeriod").andSelf(),
							id = $link.data("id");
						
						$.post("index.php?controller=pjPeriod&action=pjActionDelete", {
							"id": id
						}).done(function (data) {
							if (data.code === undefined) {
								return;
							}
							switch (data.code) {
								case 200:
									$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
										$tr.remove();
										$this.dialog("close");
									});
									break;
							}
						});
					},
					"Cancel": function () {
						$(this).dialog("close");
					}
				}
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			$(this).parent().siblings("input[type='text']").trigger("focusin").trigger("focus");//datepicker("show");
		});
	});
})(jQuery_1_8_2);