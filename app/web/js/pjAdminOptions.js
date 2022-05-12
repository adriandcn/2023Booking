var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var tabs = ($.fn.tabs !== undefined),
			miniColors = ($.fn.miniColors !== undefined),
			spinner = ($.fn.spinner !== undefined),
			dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			$frmTerms = $("#frmTerms"),
			$tabs = $("#tabs"),
			$dialogCopyOptions = $("#dialogCopyOptions");
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		if (miniColors) {
			$(".field-color").miniColors();
		}
		
		if (spinner) {
			$(".field-int").spinner({
				min: 0
			});
			$("input[name='value-int-o_bookings_per_day']").spinner("option", "min", 1);
			$("input[name='min_nights[]']:visible, input[name='max_nights[]']:visible").each(function (i, el) {
				var $el = $(el);
				$el.spinner({
					min: 1,
					max: $el.data("max")
				});
			});
		}
		
		if ($frmTerms.length > 0 && validate) {
			$frmTerms.validate();
		}
		
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		
		function onChange() {
			var $box, base_code, new_code,
				$cal = $("#install_calendar"),
				$loc = $("#install_locale"),
				$mon = $("#install_months"),
				$area = $("textarea"),
				cal = $cal.find("option:selected").val(),
				loc = $loc.find("option:selected").val(),
				mon = $mon.find("option:selected").val();
			
			if (cal == 'all') {
				$mon.attr("disabled", "disabled").closest("p").hide();
				$box = $("#boxAvailability");
				base_code = $box.text();
				new_code = base_code;
			} else {
				$mon.removeAttr("disabled").closest("p").show();
				$box = $("#boxStandard");
				base_code = $box.text();
				new_code = base_code.replace(/{CID}/g, cal).replace(/{VIEW}/g, mon);
			}

			if (loc.length > 0) {
				new_code = new_code.replace(/{LOCALE}/g, '&locale=' + loc);
			} else {
				new_code = new_code.replace(/{LOCALE}/g, '');
			}
			
			$area.text(new_code);
		}
		
		if ($("#boxStandard").length > 0) {
			onChange.call(null);
		}
		
		$("#content").on("focus", ".textarea_install", function (e) {
			var $this = $(this);
			$this.select();
			$this.mouseup(function() {
				$this.unbind("mouseup");
				return false;
			});
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "input[name='value-bool-o_allow_paypal']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxPaypal").show();
			} else {
				$(".boxPaypal").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_authorize']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxAuthorize").show();
			} else {
				$(".boxAuthorize").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_bank']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxBank").show();
			} else {
				$(".boxBank").hide();
			}
		}).on("change", "select[name='options_cid']", function (e) {
			var cid = $("option:selected", this).val(),
				tab = $("input[name='tab']").val();
			window.location.href = ["index.php?controller=pjAdminOptions&action=pjActionIndex&tab=", tab, "&cid=", cid].join("");
			
		}).on("change", "#install_calendar", function (e) {	
			onChange.call(null);
		}).on("change", "#install_locale", function (e) {
			onChange.call(null);
		}).on("change", "#install_months", function (e) {
			onChange.call(null);
		}).on("click", "#btnCopyOptions", function () {
			if ($dialogCopyOptions.length > 0 && dialog) {
				$dialogCopyOptions.dialog("open");
			}
		}).on("click", ".btnAddLimit", function (e) {
			$("#tblClone tr:first").clone().appendTo(".pj-table tbody");
			$(".pj-table tbody tr:last").find("input[name='min_nights[]']:visible, input[name='max_nights[]']:visible").spinner({
				min: 1
			});
		}).on("click", ".lnkRemoveRow", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $tr = $(this).closest("tr");
			$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
				$tr.remove();
			});
			return false;
		}).on("focusin", ".datepick", function () {
			if (datepicker) {
				var $this = $(this),
					dOpts = {
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev")
					};
				
				$this.datepicker($.extend(dOpts, {
					beforeShow: function (input, ui) {
						var dt,
							$chain,
							name = ui.input.attr("name");
						
						if (name == "date_from[]") {
							$chain = ui.input.closest("tr").find("input[name='date_to[]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "maxDate", $chain.val());
							}
						} else if (name == "date_to[]") {
							$chain = ui.input.closest("tr").find("input[name='date_from[]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "minDate", $chain.val());
							}
						}
					},
					onSelect: function (dateText, inst) {
						
						var first, second, $minNights, $maxNights,
							$tr = inst.input.closest("tr"),
							currentName = inst.input.attr("name");
						if (currentName == "date_from[]") {
							first = inst.input.datepicker("getDate");
							second = $tr.find("input[name='date_to[]']").datepicker("getDate");
							$minNights = $tr.find("input[name='min_nights[]']");
							$maxNights = $tr.find("input[name='max_nights[]']");
						} else {
							first = $tr.find("input[name='date_from[]']").datepicker("getDate");
							second = inst.input.datepicker("getDate");
							$minNights = $tr.find("input[name='min_nights[]']");
							$maxNights = $tr.find("input[name='max_nights[]']");
						}

						if (first !== null && second !== null) {
							var diff = (second - first) / (1000*60*60*24);
							$minNights.spinner("option", {
								max: diff
							});
							$maxNights.spinner("option", {
								max: diff
							});
						}
					}
				}));
			}
		});
		
		if ($dialogCopyOptions.length > 0 && dialog) {
			var buttons = {};
			buttons[myLabel.btnCopy] = function () {
				var $this = $(this),
					tab_id = $("input[name='copy_tab_id']").val();
				$.post("index.php?controller=pjAdminOptions&action=pjActionCopy", {
					"calendar_id": $("option:selected", $("select[name='copy_calendar_id']")).val(),
					"tab_id": tab_id
				}).done(function (data) {
					$this.dialog("close");
					window.location.href = "index.php?controller=pjAdminOptions&tab=" + tab_id;
				});
			};
			buttons[myLabel.btnCancel] = function () {
				$(this).dialog("close");
			};
			$dialogCopyOptions.dialog({
				resizable: false,
				draggable: false,
				autoOpen: false,
				modal: true,
				buttons: buttons
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			$(this).parent().siblings("input[type='text']").trigger("focusin").trigger("focus");//datepicker("show");
		});
	});
})(jQuery_1_8_2);