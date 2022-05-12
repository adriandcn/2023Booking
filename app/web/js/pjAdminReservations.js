var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var $frmCreateReservation = $("#frmCreateReservation"),
			$frmUpdateReservation = $("#frmUpdateReservation"),
			$frmExportReservations = $("#frmExportReservations"),
			$dialogMessage = $("#dialogMessage"),
			$dialogCalculate = $("#dialogCalculate"),
			$dialogResend = $("#dialogResend"),
			$tabs = $("#tabs"),
			tipsy = ($.fn.tipsy !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tabs = ($.fn.tabs !== undefined),
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined);

		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
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
		if (spinner) {
			$(".field-int").spinner({
				min: 1
			});
		}
		function getDashboard() {
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetDashboard", {
				year: arguments[0],
				month: arguments[1]
			}).done(function (data) {
				$("#boxDashboard").html(data);
			});
		}
		
		function calcPrices(callback) {
			$.post("index.php?controller=pjAdminReservations&action=pjActionCalcPrice", $(this).closest("form").serialize()).done(function (data) {
				if (data.status === "OK") {
					handleBalance.call(null, myLabel.invoice_total, data.amount);
					$("#amount").val(data.amount.toFixed(2));
					$("#deposit").val(data.deposit.toFixed(2));
					$("#security").val(data.security.toFixed(2));
					$("#tax").val(data.tax.toFixed(2));
					$("#total").val(data.total.toFixed(2));
				}

				if (callback !== undefined && typeof callback === "function") {
					callback();
				}
			});
		}
		
		function handleBalance(invoice_total, amount) {
			if (invoice_total < amount) {
				$(".btnBalancePayment").show();
				$(".btnAddInvoice").hide();
			} else {
				$(".btnBalancePayment").hide();
				$(".btnAddInvoice").show();
			}
		}

		$("#content").on("click", ".btnCalculate", function () {
			if ($dialogCalculate.length > 0 && dialog) {
				$dialogCalculate.data("btn", this).dialog("open");
			} else {
				calcPrices.call(this);
			}
		}).on("focusin", ".datepick", function (e) {
			var minDateTime, maxDateTimes,
				$this = $(this),
				custom = {},
				o = {
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					
				};
			switch ($this.attr("name")) {
			case "date_from":
				if($(".datepick[name='date_to']").val() != '')
				{
					var maxDate = $(".datepick[name='date_to']").datepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev"),
					}).datepicker("getDate");
					$(".datepick[name='date_to']").datepicker("destroy").removeAttr("id");
					if (maxDate !== null) {
						custom.maxDate = maxDate;
					}
				}
				break;
			case "date_to":
				if($(".datepick[name='date_from']").val() != '')
				{
					var minDate = $(".datepick[name='date_from']").datepicker({
						firstDay: $this.attr("rel"),
						dateFormat: $this.attr("rev")
					}).datepicker("getDate");
					$(".datepick[name='date_from']").datepicker("destroy").removeAttr("id");
					if (minDate !== null) {
						custom.minDate = minDate;
					}
				}
				break;
			}
			$(this).datepicker($.extend(o, custom));
		}).delegate(".cal-prev, .cal-next", "click", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			getDashboard.apply(null, [$this.data("year"), $this.data("month")]);
			return false;
		}).on("click", ".btnCreateInvoice", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			$.post("index.php?controller=pjAdminReservations&action=pjActionCreateInvoice", {
				"id": $this.data("id")
			}).done(function (data) {
				$this.siblings("span:first").text(data.text).show().fadeOut(2500);
			});
			return false;
		}).on("click", ".btnAddInvoice", function () {
			$("#frmAddInvoice").trigger("submit");
		}).on("click", ".btnBalancePayment", function () {
			$("#frmBalancePayment").trigger("submit");
		}).on("click", ".btnResend", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if (dialog && $dialogResend.length > 0) {
				$dialogResend.dialog("open");
			}
			return false;
		});
		
		if ($dialogResend.length > 0 && dialog) {
			$dialogResend.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				modal: true,
				width: 510,					
				open: function () {
					$.post("index.php?controller=pjAdminReservations&action=pjActionGetMessage&locale_id=" + $dialogResend.find("select[name='locale_id']").val(), $frmUpdateReservation.serialize()).done(function (data) {
						$dialogResend
							.find("textarea").text(data.body)
							.end()
							.find("input[type='text']").val(data.subject);
					});
				},
				close: function () {
					$dialogResend.find("textarea").text("").end().find("input[type='text']").val("");
				},
				buttons: (function () {
					var btn = {};
					btn[myLabel.btnSend] = function() {
						var c_email = null;
						if($('#c_email').length > 0)
						{
							c_email = $('#c_email').val();
						}
						var post_data = {
							message: $dialogResend.find("textarea").eq(0).val(),
							subject: $dialogResend.find("input[type='text']").eq(0).val(),
							c_email: c_email
						};
						$.post("index.php?controller=pjAdminReservations&action=pjActionSendMessage", post_data).done(function (data) {
							$dialogResend.dialog('close');
						});
					};
					btn[myLabel.btnCancel] = function() {
						$(this).dialog('close');
					};
					return btn;
				})()
			});
		}
		
		if (validate) {
			$.validator.addMethod("validDates", function (value, element) {
				return parseInt(value, 10) === 1; 
			}, myLabel.dateRangeValidation);
		}
		if ($frmCreateReservation.length > 0 && validate) {
			$frmCreateReservation.validate({
				rules: {
					"dates": "validDates",
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminReservations&action=pjActionCheckUnique"
					}
				},
				messages:{
					"uuid":{
						remote: myLabel.duplicatedUniqueID
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
		if ($frmUpdateReservation.length > 0 && validate) {
			$frmUpdateReservation.validate({
				rules: {
					"dates": "validDates",
					"uuid": {
						required: true,
						remote: "index.php?controller=pjAdminReservations&action=pjActionCheckUnique&id=" + $frmUpdateReservation.find("input[name='id']").val()
					}
				},
				messages:{
					"uuid":{
						remote: myLabel.duplicatedUniqueID
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
			
			$frmUpdateReservation.bind("submit.custom", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (dialog && $dialogMessage.length > 0) {
					if($frmUpdateReservation.valid())
					{
						$dialogMessage.dialog("open");
					}
				}
				return false;
			}).on("keyup", "#amount", function () {
				handleBalance.call(null, myLabel.invoice_total, parseFloat($(this).val()));
			});
			
			if ($dialogMessage.length > 0) {
				var buttons = {};
				buttons[myLabel.btnContinue] = function() {
					var $this = $(this);
					if ($this.find("#dialog_confirm").is(":checked")) {
						var qs = ["&message=", $this.find("textarea").eq(0).val(), "&subject=", $this.find("input[type='text']").eq(0).val()].join("");
						$.post("index.php?controller=pjAdminReservations&action=pjActionSendMessage", $frmUpdateReservation.serialize() + qs).done(function (data) {
							$frmUpdateReservation.unbind(".custom").submit();
							$this.dialog('close');
						});
					} else {
						$frmUpdateReservation.unbind(".custom").submit();
						$this.dialog('close');
					}
				};
				buttons[myLabel.btnCancel] = function() {
					$(this).dialog('close');
				};
				$dialogMessage.dialog({
					autoOpen: false,
					resizable: false,
					draggable: false,
					modal: true,
					width: 510,					
					open: function () {
						$.post("index.php?controller=pjAdminReservations&action=pjActionGetMessage", $frmUpdateReservation.serialize()).done(function (data) {
							$dialogMessage
								.find("textarea").text(data.body)
								.end()
								.find("input[type='text']").val(data.subject);
						});
					},
					close: function () {
						$dialogMessage.find("textarea").text("").end().find("input[type='text']").val("");
					},
					buttons: buttons
				});
			}
		}

		function checkDates(date_from, date_to, calendar_id, id) {
			$.get("index.php?controller=pjAdminReservations&action=pjActionCheckDates", {
				"date_from": date_from,
				"date_to": date_to,
				"calendar_id": calendar_id,
				"id": id
			}).done(function (data) {
				if (data.code === undefined) {
					return;
				}
				switch (data.code) {
				case 200:
					$("input#dates").val('1');
					break;
				case 100:
					$("input#dates").val('0');
					break;
				}
			});
		}
		
		if ($frmCreateReservation.length > 0 || $frmUpdateReservation.length > 0) {
			var $date_from = $("#date_from");
			$date_from.datepicker({
				firstDay: $date_from.attr("rel"),
				dateFormat: $date_from.attr("rev"),
				onSelect: function (dateText, inst) {
					var to, $form, cal_id, res_id, 
						$dt = $("#date_to"),
						d = $dt.datepicker("getDate");
					
					$dt.datepicker("option", "minDate", dateText);
					
					if (d !== null) {
						to = [d.getFullYear(), d.getMonth() + 1, d.getDate()].join("-");
						$form = $(this).closest("form");
						
						res_id = $form.find("input[name='id']").val();  
						if(res_id>0) cal_id = $form.find("input[name='calendar_id']").val();
						else  cal_id = $form.find("select[name='calendar_id']").val();
						
						checkDates.call(null, 
							[inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join("-"), 
							to, 
							cal_id,
							res_id
						);
					}
				}
			});
			
			var $date_to = $("#date_to");
			$date_to.datepicker({
				firstDay: $date_to.attr("rel"),
				dateFormat: $date_to.attr("rev"),
				minDate: new Date(),
				onSelect: function (dateText, inst) {
					var from, $form, cal_id, res_id, 
						d = $("#date_from").datepicker("getDate");
					
					if (d !== null) {
						from = [d.getFullYear(), d.getMonth() + 1, d.getDate()].join("-");
						$form = $(this).closest("form");
						
						res_id = $form.find("input[name='id']").val();
						if(res_id>0) cal_id = $form.find("input[name='calendar_id']").val();
						else  cal_id = $form.find("select[name='calendar_id']").val();
						
						checkDates.call(null, 
							from, 
							[inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay].join("-"), 
							cal_id,
							res_id
						);
					}
				}
			});
		}

		var $PM = $("#payment_method");
		if ($PM.length > 0) {
			$PM.bind("change", function () {
				if ($("option:selected", this).val() == 'creditcard') {
					$(".vrCC").show();
				} else {
					$(".vrCC").hide();
				}
			});	
		}
		
		function formatDefault (str) {
			return myLabel[str] || str;
		}
		
		function formatId (str) {
			return ['<a href="index.php?controller=pjInvoice&action=pjActionUpdate&id=', str, '">#', str, '</a>'].join("");
		}
		
		function formatTotal (str, obj) {
			return obj.total_formated;
		}
		
		function formatCreated(str) {
			if (str === null || str.length === 0) {
				return myLabel.empty_datetime;
			}
			
			if (str === '0000-00-00 00:00:00') {
				return myLabel.invalid_datetime;
			}
			
			if (str.match(/\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}/) !== null) {
				var x = str.split(" "),
					date = x[0],
					time = x[1],
					dx = date.split("-"),
					tx = time.split(":"),
					y = dx[0],
					m = parseInt(dx[1], 10) - 1,
					d = dx[2],
					hh = tx[0],
					mm = tx[1],
					ss = tx[2];
				return $.datagrid.formatDate(new Date(y, m, d, hh, mm, ss), pjGrid.jsDateFormat + ", hh:mm:ss");
			}
		}
		
		if ($("#grid_invoices").length > 0 && datagrid) {
			var $grid_invoices = $("#grid_invoices").datagrid({
				buttons: [{type: "edit", title: 'Edit', url: "index.php?controller=pjInvoice&action=pjActionUpdate&id={:id}"},
				          {type: "delete", title: 'Delete', url: "index.php?controller=pjInvoice&action=pjActionDelete&id={:id}"}],
				columns: [
				    {text: myLabel.num, type: "text", sortable: true, editable: false, renderer: formatId},
				    {text: myLabel.order_id, type: "text", sortable: true, editable: false},
				    {text: myLabel.issue_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.due_date, type: "date", sortable: true, editable: false, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				    {text: myLabel.created, type: "text", sortable: true, editable: false, renderer: formatCreated},
				    {text: myLabel.status, type: "text", sortable: true, editable: false, renderer: formatDefault},	
				    {text: myLabel.total, type: "text", sortable: true, editable: false, align: "right", renderer: formatTotal}
				],
				dataUrl: "index.php?controller=pjInvoice&action=pjActionGetInvoices&q=" + $frmUpdateReservation.find("input[name='uuid']").val(),
				dataType: "json",
				fields: ['id', 'order_id', 'issue_date', 'due_date', 'created', 'status', 'total'],
				paginator: {
					actions: [
					   {text: myLabel.delete_title, url: "index.php?controller=pjInvoice&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_body}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		function formatCalendar(val, obj) {
			if (pjGrid.isAdmin === 0) {
				return val + '<br/>' + obj.uuid;
			}
			return ['<a href="index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionView&calendar_id=', obj.calendar_id, '&nextParams=', encodeURIComponent("id=" + obj.calendar_id), '">', val, '</a>', '<br/>', obj.uuid].join("");
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", title: myLabel.edit, url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", title: myLabel.delete, url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"}
				          ],
				columns: [{text: myLabel.client_name, type: "text", sortable: true, editable: true},
				          {text: myLabel.calendar, type: "text", sortable: true, editable: false, renderer: formatCalendar},
				          {text: myLabel.from, type: "date", sortable: true, editable: true,
								jqDateFormat: pjGrid.jqDateFormat,
								width: 100,
								editableWidth: 80, 
								renderer: $.datagrid._formatDate, 
								editableRenderer: $.datagrid._formatDate,
								dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.to, type: "date", sortable: true, editable: true, 
								jqDateFormat: pjGrid.jqDateFormat,
								width: 100,
								editableWidth: 80,
								renderer: $.datagrid._formatDate, 
								editableRenderer: $.datagrid._formatDate,
								dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 90, options: [
				                                                                                     {label: myLabel.pending, value: "Pending"}, 
				                                                                                     {label: myLabel.confirmed, value: "Confirmed"},
				                                                                                     {label: myLabel.cancelled, value: "Cancelled"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminReservations&action=pjActionGetReservation" + pjGrid.queryString,
				dataType: "json",
				fields: ['c_name', 'calendar', 'date_from', 'date_to', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.exportSelected, url: "index.php?controller=pjAdminReservations&action=pjActionExportReservation", ajax: false},
					   {text: myLabel.deleteSelected, url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservationBulk", render: true, confirmation: myLabel.deleteConfirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminReservations&action=pjActionSaveReservation&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
			
			$(document).on("click", ".btn-all", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					q: "",
					uuid: "",
					status: "",
					calendar_id: "",
					c_name: "",
					c_email: "",
					date: "",
					date_from: "",
					date_to: "",
					amount_from: "",
					amount_to: "",
					current_week: "",
					last_7days: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-today", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache"),
					today = new Date();
				$.extend(cache, {
					date: [today.getFullYear(), today.getMonth() + 1, today.getDate()].join("-")
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-confirmed", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					date: "",
					status: "Confirmed"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-pending", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					date: "",
					status: "Pending"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("click", ".btn-cancelled", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
				var content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					date: "",
					status: "Cancelled"
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
				return false;
			}).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val(),
					uuid: "",
					status: "",
					calendar_id: "",
					c_name: "",
					c_email: "",
					date: "",
					date_from: "",
					date_to: "",
					amount_from: "",
					amount_to: "",
					current_week: "",
					last_7days: ""
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "ASC", content.page, content.rowCount);
				return false;
			}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
				e.stopPropagation();
				var $advForm = $(".pj-form-filter-advanced");
				if ($advForm.is(":visible")) {
					$advForm.hide().find("input[type='text'], select").val("");
				} else {
					$advForm.show();
				}
			}).on("submit", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var obj = {q: ""},
					$this = $(this),
					arr = $this.serializeArray(),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
					obj[arr[i].name] = arr[i].value;
				}
				$.extend(cache, obj);
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "ASC", content.page, content.rowCount);
				return false;
			}).on("reset", ".frm-filter-advanced", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				$(".pj-button-detailed").trigger("click");
				return false;
			});
			
		}
		
		if ($dialogCalculate.length > 0 && dialog) {
			var btn = {};
			btn[myLabel.btnContinue] = function () {
				calcPrices.call($dialogCalculate.data("btn"), function () {
					$dialogCalculate.dialog("close");
				});
			};
			btn[myLabel.btnCancel] = function () {
				$(this).dialog("close");
			};
			$dialogCalculate.dialog({
				modal: true,
				autoOpen: false,
				resizable: false,
				draggable: false,
				buttons: btn
			});
		}

		if ($frmExportReservations.length > 0 && validate) {
			$frmExportReservations.validate({
				rules: {
					"password": {
						required: function(){
							if($('#feed').is(':checked'))
							{
								return true;
							}else{
								return false;
							}
						}
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ".ignore"
			});
		}
		
		$(document).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on("click change", "#dialog_confirm", function (e) {
			if ($(this).is(":checked")) {
				$dialogMessage.find("textarea, input[type='text']").removeAttr("readonly").removeClass("pj-form-field-readonly");
			} else {
				$dialogMessage.find("textarea, input[type='text']").attr("readonly", "readonly").addClass("pj-form-field-readonly");
			}
		}).on("change", "#calendar_id", function (e) {
			var $form = $(this).closest("form"),
				d,
				s,
				from = '',
				to = '';
				
				d = $("#date_from").datepicker("getDate");
				if(d !== null) from = [d.getFullYear(), d.getMonth() + 1, d.getDate()].join("-");
	
				s = $("#date_to").datepicker("getDate");
				if(s !== null) to = [s.getFullYear(), s.getMonth() + 1, s.getDate()].join("-");  
	
			if($(this).val() != '' && from != '' && to)
			{
				checkDates.call(null, 
						from, 
						to, 
						$(this).val(),
						$form.find("input[name='id']").val()
					);
			}
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetAdults", {
				"id": $(this).val()
			}).done(function (data) {
				$('#boxAdults').html(data);
			});
			
			$.get("index.php?controller=pjAdminReservations&action=pjActionGetChildren", {
				"id": $(this).val()
			}).done(function (data) {
				$('#boxChildren').html(data);
			});
			
		}).on("change", "#export_period", function (e) {
			var period = $(this).val();
			if(period == 'last')
			{
				$('#last_label').show();
				$('#next_label').hide();
				$('#range_label').hide();
			}else if(period == 'all'){
				$('#last_label').hide();
				$('#next_label').hide();
				$('#range_label').hide();
			}else if(period == 'range'){
				$('#last_label').hide();
				$('#next_label').hide();
				$('#range_label').show();
			}else{
				$('#last_label').hide();
				$('#next_label').show();
				$('#range_label').hide();
			}
		}).on("click", "#file", function (e) {
			$('#abSubmitButton').val(myLabel.btn_export);
			$('.abFeedContainer').hide();
			$('.abPassowrdContainer').hide();
			$("#export_period option[value='all']").show();
			$("#export_period option[value='range']").show();
		}).on("click", "#feed", function (e) {
			$('.abPassowrdContainer').show();
			$('#abSubmitButton').val(myLabel.btn_get_url);
			if($('#export_period').val() == 'all' || $('#export_period').val() == 'range')
			{
				$('#export_period').val('next');
				$('#last_label').hide();
				$('#range_label').hide();
				$('#next_label').show();
			}
			$("#export_period option[value='all']").hide();
			$("#export_period option[value='range']").hide();
		}).on("focus", "#reservations_feed", function (e) {
			$(this).select();
		}).on("change", "#resend_language", function (e) {
			$.post("index.php?controller=pjAdminReservations&action=pjActionGetMessage&locale_id=" + $dialogResend.find("select[name='locale_id']").val(), $frmUpdateReservation.serialize()).done(function (data) {
				$dialogResend
					.find("textarea").text(data.body)
					.end()
					.find("input[type='text']").val(data.subject);
			});
		});
	});
})(jQuery_1_8_2);