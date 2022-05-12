var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			validate = ($.fn.validate !== undefined),
			$frmCreateCalendar = $("#frmCreateCalendar");

		if ($frmCreateCalendar.length > 0 && validate) {
			$frmCreateCalendar.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				errorClass: "err",
				wrapper: "em",
				onkeyup: false,
				ignore: ".ignore",
				invalidHandler: function (event, validator) {
					$(".pj-multilang-wrap").each(function( index ) {
						if($(this).attr('data-index') == myLabel.localeId)
						{
							$(this).css('display','block');
						}else{
							$(this).css('display','none');
						}
					});
					$(".pj-form-langbar-item").each(function( index ) {
						if($(this).attr('data-index') == myLabel.localeId)
						{
							$(this).addClass('pj-form-langbar-item-active');
						}else{
							$(this).removeClass('pj-form-langbar-item-active');
						}
					});
				}
			});
		}

		$("#content").on("click", ".pj-checkbox", function () {
			var $this = $(this);
			if ($this.find("input[type='checkbox']").is(":checked")) {
				$this.addClass("pj-checkbox-checked");
			} else {
				$this.removeClass("pj-checkbox-checked");
			}
		}).on("click", ".listing-tip", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".abCalendarLinkMonth", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			$.get("index.php?controller=pjAdminCalendars&action=pjActionGetCal", {
				"cid": $this.data("cid"),
				"year": $this.data("year"),
				"month": $this.data("month")
			}).done(function (data) {
				$("#abCalendar_" + $this.data("cid")).html(data);
			});
			return false;
		});

		function formatOwner(val, obj) {
			return ['<a href="index.php?controller=pjAdminUsers&action=pjActionUpdate&id=', obj.user_id, '">', obj.user_name, '</a>'].join("");
		}

		function formatName(val, obj) {
			return ['<a href="index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionView&calendar_id=', obj.id, '&nextParams=', encodeURIComponent("id=" + obj.id), '">', val, '</a>'].join("");
		}

		function onBeforeShow (obj) {
			if (parseInt(obj.id, 10) === pjGrid.currentCalendarId) {
				return false;
			}
			return true;
		}

		if ($("#grid").length > 0 && datagrid) {

			var gridOpts = {
				buttons: [{type: "settings", title: myLabel.settings, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminOptions&nextAction=pjActionIndex&nextParams=tab%3D1&calendar_id={:id}"},
				          {type: "prices", title: myLabel.prices, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionPrices&calendar_id={:id}"},
				          {type: "edit", title: myLabel.edit, url: "index.php?controller=pjAdmin&action=pjActionRedirect&nextController=pjAdminCalendars&nextAction=pjActionView&calendar_id={:id}&nextParams=id%3D{:id}"},
				          {type: "delete", title: myLabel.delete, url: "index.php?controller=pjAdminCalendars&action=pjActionDeleteCalendar&id={:id}", beforeShow: onBeforeShow},
				          {type: "menu", url: "#", text: myLabel.more, items:[
				              {text: myLabel.viewReservations, url: "index.php?controller=pjAdminReservations&action=pjActionIndex&calendar_id={:id}"},
				              {text: myLabel.installPreview, url: "index.php?controller=pjAdminOptions&action=pjActionInstall&calendar_id={:id}"}
				           ]}],
				columns: [{text: myLabel.id, type: "text", sortable: true, editable: false},
				          {text: myLabel.calendar, type: "text", sortable: true, width: 200, renderer: formatName},
				          {text: myLabel.user, type: "text", sortable: true, editable: false, width: 150, renderer: formatOwner}
					//{text: myLabel.descripcion, type: "text", sortable: false, editable: false, width: 150, renderer: formatOwner}
				],
				dataUrl: "index.php?controller=pjAdminCalendars&action=pjActionGetCalendar" + pjGrid.queryString,
				dataType: "json",
				fields: ['id', 'name', 'user_name'],
				paginator: {
					actions: [
						{text: myLabel.deleteSelected, url: "index.php?controller=pjAdminCalendars&action=pjActionDeleteCalendarBulk", render: true, confirmation: myLabel.deleteConfirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminCalendars&action=pjActionSaveCalendar&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			};

			var $grid = $("#grid").datagrid(gridOpts);

			$(document).on("submit", ".frm-filter", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid.datagrid("option", "content"),
					cache = $grid.datagrid("option", "cache");
				$.extend(cache, {
					q: $this.find("input[name='q']").val()
				});
				$grid.datagrid("option", "cache", cache);
				$grid.datagrid("load", "index.php?controller=pjAdminCalendars&action=pjActionGetCalendar", "id", "ASC", content.page, content.rowCount);
				return false;
			});

		}

		function formatCurrency(val) {
			if(val == null)
			{
				return pjGrid.currencySign.replace('99', '0.00');
			}else{
				return pjGrid.currencySign.replace('99', val);
			}
		}

		function formatData(val, obj) {
			return ['<div style="line-height: 22px"><span class="w60 bold inline_block">', myLabel.status, ':</span><span class="pj-table-cell-label pj-status pj-status-', obj.status, '" style="display: inline-block">', obj.status, '</span>',
			        '<br><span class="w60 bold inline_block">', myLabel.id, ':</span>', obj.uuid,
			        '<br><span class="w60 bold inline_block">', myLabel.name, ':</span>', obj.c_name,
			        '<br><span class="w60 bold inline_block">', myLabel.email, ':</span>', obj.c_email,
			        '<br><span class="w60 bold inline_block">', myLabel.from, ':</span>', $.datagrid._formatDate(obj.date_from, pjGrid.jsDateFormat),
			        '<br><span class="w60 bold inline_block">', myLabel.to, ':</span>', $.datagrid._formatDate(obj.date_to, pjGrid.jsDateFormat),
			        '<br><span class="w60 bold inline_block">', myLabel.amount, ':</span>', formatCurrency(obj.amount),
			        '</div>'
			        ].join("");
		}

		if ($("#gridReservations").length > 0 && datagrid) {

			var $gridReservations = $("#gridReservations").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminReservations&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminReservations&action=pjActionDeleteReservation&id={:id}"}
				          ],
				columns: [{text: myLabel.today, type: "text", sortable: false, editable: false, width: 270, renderer: formatData}],
				dataUrl: "index.php?controller=pjAdminReservations&action=pjActionGetReservation&calendar_id="+view_calendar_id + pjGrid.queryString,
				dataType: "json",
				fields: ['uuid'],
				paginator: false,
				saveUrl: "index.php?controller=pjAdminReservations&action=pjActionSaveReservation&id={:id}",
				select: false,
				onRender: function () {
					var $a = $(".newReserv"),
						href = $a.attr("href"),
						cache = $gridReservations.datagrid("option", "cache");
					if (cache.time !== undefined && cache.time !== null) {
						var dt = new Date(cache.time * 1000),
							iso = [dt.getFullYear(), dt.getMonth()+1, dt.getDate()+1].join("-");
						$a.attr("href", href.replace(/&date_from=\d{4}\-\d{2}\-\d{2}/, '&date_from=' + iso));
						$gridReservations.find("th:first").html($.datagrid._formatDate(iso, pjGrid.jsDateFormat));

					}
				}
			});

			$("#content").on("click", ".abCalendarLinkDate", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this).parent(),
					content = $gridReservations.datagrid("option", "content"),
					cache = $gridReservations.datagrid("option", "cache");
				content.rowCount = 100;//fix
				if($this.hasClass('abCalendarCellInner'))
				{
					$this = $(this).parent().parent();
				}
				$.extend(cache, {
					"time": $this.data("time")
				});
				$gridReservations.datagrid("option", "cache", cache);
				$gridReservations.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation&calendar_id="+view_calendar_id, "id", "DESC", content.page, content.rowCount);
				return false;
			});

			var m = window.location.search.match(/&time=(\d{10})/);
			if (m !== null) {
				var content = $gridReservations.datagrid("option", "content"),
					cache = $gridReservations.datagrid("option", "cache");
				content.rowCount = 100;//fix
				$.extend(cache, {
					"time": m[1]
				});
				$gridReservations.datagrid("option", "cache", cache);
				$gridReservations.datagrid("load", "index.php?controller=pjAdminReservations&action=pjActionGetReservation", "id", "DESC", content.page, content.rowCount);
			}

		}
	});
})(jQuery_1_8_2);

