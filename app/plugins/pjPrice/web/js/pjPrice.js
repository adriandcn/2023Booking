var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var dialog = ($.fn.dialog !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			$datepick = $(".datepick"),
			$dialogPrices = $("#dialogPrices"),
			$dialogPricesDelete = $("#dialogPricesDelete"),
			$dialogPricesSeasonDelete = $("#dialogPricesSeasonDelete"),
			$dialogPricesStatus = $("#dialogPricesStatus"),
			$frmCreatePrice = $("#frmCreatePrice"),
			dOpts = {};

		if ($datepick.length > 0) {
			dOpts = $.extend(dOpts, {
				firstDay: $datepick.attr("rel"),
				dateFormat: $datepick.attr("rev")
			});
		}

		function errorHandler() {
			var $form = this;
			$form.find(":input").removeAttr("readonly");
	    	$form.find(".pj-button").removeAttr("disabled");
	    	
	    	$dialogPricesStatus
				.find(".bxPriceStatusStart, .bxPriceStatusEnd").hide().end()
				.find(".bxPriceStatusFail").show();
	    	
	    	$dialogPricesStatus.dialog("option", "close", function () {
	    		$(this).dialog("option", "buttons", {});
	    	});
	    	$dialogPricesStatus.dialog("option", "buttons", {
	    		'Close': function () {
	    			$(this).dialog("close");
	    		}
	    	});
		}
		
		function resetValication() {
			this.find("tr").removeClass("pjPrice_duplicate");
		}
		
		if ($frmCreatePrice.length > 0 && validate) {
			$frmCreatePrice.validate({
				ignore: ".ignore",
				errorLabelContainer: $(".bxPriceErrors"),
				submitHandler: function (form) {

					$dialogPricesStatus.dialog("open");
					
					var post, len, num, $current, $tr, i, 
						total = 0,
						$form = $(form),
						$tabs = $form.find("#tabs").find("div[id^='tabs-']"),
						perLoop = 100 //Keep this even value
					;
					
					$form.find(":input").not(".pj-button").attr("readonly", "readonly");
					$form.find(".pj-button").attr("disabled", "disabled");
					
					//Validation adults & children
					resetValication.call($form);
					var $x_tabs, $x_adults, $x_children, x_str, x_tabid, x_match, x_adults, x_children,
						$x_from, $x_to, x_from, x_to,
						x_stack = [], 
						x_duplicates = [],
						x_arr = [],
						x_dates = [];
					$tabs.each(function (index) {
						$current = $(this);
						$x_tabs = $current.find("input[name^='tabs[']");
						x_match = $x_tabs.attr("name").match(/tabs\[(\d+)\]/);
						if (x_match !== null) {
							x_tabid = x_match[1];
						}
						//--------
						$current.find(".pj-table select[name*='_adults[']").each(function (i) {
							$x_adults = $(this);
							$x_children = $x_adults.closest("td").find("select[name*='_children[']");
							x_adults = $x_adults.find("option:selected").val();
							x_children = $x_children.find("option:selected").val();
							x_str = [x_tabid, x_adults, x_children].join("_");
							if ($.inArray(x_str, x_stack) !== -1) {
								x_duplicates.push({
									"tab_id": parseInt(x_tabid, 10), 
									"adults": parseInt(x_adults, 10),
									"children": parseInt(x_children, 10),
									"row": $x_adults.closest("tbody").find("tr").index($x_adults.closest("tr").get(0))
								});
							}
							x_stack.push(x_str);
						});
						//--------
						$x_from = $current.find("input[name*='_date_from[']")
						$x_to = $current.find("input[name*='_date_to[']");
						x_from = $x_from.val();
						x_to = $x_to.val();
						x_str = [x_from, x_to].join("_");
						if ($.inArray(x_str, x_arr) !== -1) {
							x_dates.push({
								"tab_id": parseInt(x_tabid, 10), 
								"from": x_from,
								"to": x_to
							});
						}
						x_arr.push(x_str);
						//--------
					});
					
					if (x_duplicates.length > 0) {
						for (var x = 0, xCnt = x_duplicates.length; x < xCnt; x++) {
							$tabs.eq(x_duplicates[x].tab_id - 1).find(".pj-table tbody tr").eq(x_duplicates[x].row).next().addBack().addClass("pjPrice_duplicate");
							if (x === 0) {
								$form.find("#tabs").tabs("option", "active", x_duplicates[x].tab_id - 1);
							}
						}
					}
					
					if (x_dates.length > 0) {
						
					}
					
					if (x_duplicates.length > 0 || x_dates.length > 0) {
						errorHandler.call($form);
						return;
					}
					
					$.post("index.php?controller=pjPrice&action=pjActionDeleteAll").done(function () {
						
						$tabs.each(function (index) {
							len = $(this).find("tbody > tr").length;
							total += len > perLoop ? Math.ceil(len / perLoop) : 1;
						});
						
						$tabs.each(function (index) {
							$current = $(this);
							i = 0;
							$tr = $(this).find("tbody > tr");
							len = $tr.length;
							num = len > perLoop ? Math.ceil(len / perLoop) : 1;
							
							setPrices.call(null);
						});
					});
			
					function setPrices() {
						$.ajaxSetup({async:false});
						post = $current.find("input.datepick, :input[name^='tabs[']").serialize();
						post += "&" + $tr.slice(i * perLoop, (i + 1) * perLoop).find(":input").serialize();
						
						i++;
						$.post("index.php?controller=pjPrice&action=pjActionBeforeSave", post, callback);
					}
					
					function callback(data) {
						if (data.status === "ERR") {
							errorHandler.call($form);
							return;
						}
						
						total--;
						num--;
						if (num > 0) {
					        setPrices.call(null);
					    }
						
						if (total === 0) {
					    	$.post("index.php?controller=pjPrice&action=pjActionSave").done(function (data) {
					    		$form.find(":input").removeAttr("readonly");
						    	$form.find(".pj-button").removeAttr("disabled");
						    	
						    	$dialogPricesStatus
									.find(".bxPriceStatusStart, .bxPriceStatusFail").hide().end()
									.find(".bxPriceStatusEnd").show();
						    	
						    	$dialogPricesStatus.dialog("option", "close", function () {
						    		$(this).dialog("option", "buttons", {});
									window.location.reload();
						    	});
						    	$dialogPricesStatus.dialog("option", "buttons", {
						    		'Close': function () {
						    			$(this).dialog("close");
						    		}
						    	});
					    	});
					        return;
					    }
					}
				}
			});
		}
		
		$("#content").on("submit", "#frmCreatePrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("focusin", ".datepick", function (e) {
			if (datepicker) {
				$(this).datepicker($.extend(dOpts, {
					beforeShow: function (input, ui) {
						var dt,
							$chain,
							name = ui.input.attr("name"),
							m3 = name.match(/(\d+)_date_from\[\]/),
							m4 = name.match(/(\d+)_date_to\[\]/);
						
						if (m3 !== null) {
							//2_date_from[]
							$chain = $("input[name='" + m3[1] + "_date_to[]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "maxDate", $chain.val());
							}
						} else if (m4 !== null) {
							//2_date_to[]
							$chain = $("input[name='" + m4[1] + "_date_from[]']");
							dt = $chain.datepicker(dOpts).datepicker("getDate");
							if (dt != null) {
								ui.input.datepicker("option", "minDate", $chain.val());
							}
						}
					}
				}));
			}
		}).on("click", ".lnkAddPrice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $clone = $("#tmplDefault tbody tr:first").clone(),
				rand = 'x_' + Math.ceil(Math.random() * 999999),
				h = $clone.html().replace(/\{INDEX\}/g, $(this).attr("rel")).replace(/\{RAND\}/g, rand);
			$clone = $('<tr>' + h + '</tr>');
			$clone.appendTo($(this).closest(".pj-table").find("tbody"));
			
			$clone = $("#tmplDefault tbody tr:nth-child(2)").clone();
			h = $clone.html().replace(/\{INDEX\}/g, $(this).attr("rel")).replace(/\{RAND\}/g, rand);
			$clone = $('<tr>' + h + '</tr>');
			$clone.appendTo($(this).closest(".pj-table").find("tbody"));
			return false;
		}).on("click", ".lnkRemoveRow", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($dialogPricesDelete.length > 0 && dialog) {
				$dialogPricesDelete.data('that', this).dialog("open");
			}
			return false;
		}).on("click", "#tabs span.ui-icon-close", function (e) {
			if ($dialogPricesSeasonDelete.length > 0 && dialog) {
				$dialogPricesSeasonDelete.data('that', this).dialog("open");
			}
		});
		
		function addTab() {
			var id = $(".ui-tabs-panel:last").attr("id"),
				spl = id.split("-");
		
			var $tabs = $("#tabs"),
				i = parseInt(spl[1], 10) + 1;
			$tabs.find('ul').append(['<li><a href="#tabs-', i, '">', $tab_title_input.val(), '</a> <span class="ui-icon ui-icon-close">Remove Tab</span></li>'].join(''));
			$tabs.append(['<div id="tabs-', i, '"></div>'].join(''));
			$tabs.tabs('refresh');
		
			var $clone = $("#tmplSeason").clone(),
				h = $clone.html()
					.replace(/\{INDEX\}/g, i)
					.replace(/\{RAND\}/g, 'x_' + Math.ceil(Math.random() * 999999))
					.replace(/\{TAB_TITLE\}/g, $tab_title_input.val());
			$clone = $(h);
			$clone.appendTo($tabs.children('div:last'));
			$tabs.tabs('option', 'active', $tabs.find('ul li').length - 1);
		}
		
		if ($dialogPrices.length > 0 && dialog) {
			var $tab_title_input = $("#tab_title"),
				validator,
				$tabs, $dialog, $form;
		
			$tabs = $("#tabs").tabs();
			
			$dialog = $dialogPrices.dialog({
				autoOpen: false,
				modal: true,
				width: 400,
				buttons: {
					'Add': function() {
						if (validator.form()) {
							addTab();
							$(this).dialog("close");
						}
					},
					'Cancel': function() {
						$(this).dialog("close");
					}
				},
				open: function() {
					$tab_title_input.focus();
				},
				close: function() {
					$form[0].reset();
				}
			});
			
			$form = $dialog.find("form").submit(function() {
				addTab();
				$dialog.dialog("close");
				return false;
			});
			validator = $form.validate({
				rules: {
					"tab_title": "required"
				}
			});
			
			$(".button_add_season").click(function() {
				$dialog.dialog("open");
			});
		}
		
		if ($dialogPricesDelete.length > 0 && dialog) {
			$dialogPricesDelete.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				buttons: {
					'Delete': function () {
						var $tr = $($dialogPricesDelete.data("that")).closest("tr").next().andSelf();
						$tr.css("backgroundColor", "#FFB4B4").fadeOut("slow", function () {
							$tr.remove();
							$dialogPricesDelete.dialog("close");
						});
					},
					'Cancel': function () {
						$(this).dialog("close");
					}
				}
			});
		}
		if ($dialogPricesSeasonDelete.length > 0 && dialog) {
			$dialogPricesSeasonDelete.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				buttons: {
					'Delete': function () {
						var $tabs = $("#tabs"),
							index = $tabs.find("li").index($($dialogPricesSeasonDelete.data("that")).parent());
						
						$tabs.find('ul li').eq(index).remove();
						$tabs.children('div').eq(index).remove();
						$tabs.tabs('refresh');

						$(this).dialog("close");
					},
					'Cancel': function () {
						$(this).dialog("close");
					}
				}
			});
		}
		if ($dialogPricesStatus.length > 0 && dialog) {
			$dialogPricesStatus.dialog({
				autoOpen: false,
				modal: true,
				resizable: false,
				draggable: false,
				open: function () {
					$dialogPricesStatus
						.find(".bxPriceStatusFail, .bxPriceStatusEnd").hide().end()
						.find(".bxPriceStatusStart").show();
				},
				close: function () {
					$(this).dialog("option", "buttons", {});
					window.location.reload();
				},
				buttons: {}
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			$(this).parent().siblings("input[type='text']").trigger("focusin").trigger("focus");//datepicker("show");
		});
	});
})(jQuery_1_8_2);