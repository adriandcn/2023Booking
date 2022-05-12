var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	"use strict";
	$(function () {
		var $frmCreateUser = $("#frmCreateUser"),
			$frmUpdateUser = $("#frmUpdateUser"),
			multiselect = ($.fn.multiselect !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		$("#content").on("change", "#role_id", function (e) {
			var role_id = $("option:selected", this).val();
			getNotifications.call(null, role_id, 'notify_email', '#boxEmail');
			getNotifications.call(null, role_id, 'notify_sms', '#boxSms');
		});
		
		function getNotifications(role_id, name, selector) {
			$.get("index.php?controller=pjAdminUsers&action=pjActionGetNotifications", {
				"id": $("input[name='id']").val(),
				"role_id": role_id,
				"name": name
			}).done(function (data) {
				$(selector).html(data);
				initMultiselect.call(null);
			});
		}
		
		function initMultiselect() {
			if (multiselect) {
				$("select[name^='notify_email'], select[name^='notify_sms']").multiselect();
			}
		}
		
		initMultiselect.call(null);
		
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		
		if ($frmCreateUser.length > 0 && validate) {
			$frmCreateUser.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminUsers&action=pjActionCheckEmail"
					}
				},
				messages: {
					"email": {
						remote: myLabel.emailValidation
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		if ($frmUpdateUser.length > 0 && validate) {
			$frmUpdateUser.validate({
				rules: {
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminUsers&action=pjActionCheckEmail&id=" + $frmUpdateUser.find("input[name='id']").val()
					}
				},
				messages: {
					"email": {
						remote: myLabel.emailValidation
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		function formatRole (str) {
			return ['<span class="label-status user-role-', str, '">', str, '</span>'].join("");
		}
		
		function formatStatus (str, obj) {
			return str;
		}
		
		function onBeforeShow (obj) {
			var id = parseInt(obj.id, 10);
			if (id === pjGrid.currentUserId || id === 1) {
				return false;
			}
			return true;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", title: myLabel.edit, url: "index.php?controller=pjAdminUsers&action=pjActionUpdate&id={:id}"},
				          {type: "delete", title: myLabel.delete, url: "index.php?controller=pjAdminUsers&action=pjActionDeleteUser&id={:id}", beforeShow: onBeforeShow}
				          ],
				columns: [{text: myLabel.name, type: "text", sortable: true, editable: true},
				          {text: myLabel.email, type: "text", sortable: true, editable: true},
				          {text: myLabel.role, type: "text", sortable: true, renderer: formatRole, width: 60},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, renderer: formatStatus, width: 80, options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminUsers&action=pjActionGetUser",
				dataType: "json",
				fields: ['name', 'email', 'role', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.deleteSelected, url: "index.php?controller=pjAdminUsers&action=pjActionDeleteUserBulk", render: true, confirmation: myLabel.deleteConfirmation},
					   {text: myLabel.revert, url: "index.php?controller=pjAdminUsers&action=pjActionStatusUser", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminUsers&action=pjActionExportUser", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminUsers&action=pjActionSaveUser&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminUsers&action=pjActionGetUser", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminUsers&action=pjActionGetUser", "name", "ASC", content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminUsers&action=pjActionGetUser", "id", "ASC", content.page, content.rowCount);
			return false;
		}).on("focus", "select[data-name='status']", function () {
			var $this = $(this), 
				id = $this.closest("tr").data("id").replace('id_', '');
			if (id == pjGrid.currentUserId) {
				$this.find("option").removeAttr("disabled").filter(function (index) {
					return this.value == "F";
				}).attr("disabled", "disabled");
			} else {
				$this.find("option").removeAttr("disabled");
			}
		});
	});
})(jQuery_1_8_2);