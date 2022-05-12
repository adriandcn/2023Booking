<?php

if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	$rs = __('reservation_statuses', true);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservations'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
			<?php
			if (pjObject::getPlugin('pjInvoice') !== NULL)
			{
				?><li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices&amp;foreign_id="><?php __('plugin_invoice_menu_invoices'); ?></a></li><?php
			}
			?>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>

	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('lblReservationPlaceholderSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>

		<div class="float_right t5">
			<a href="#" class="pj-button btn-today"><?php __('lblToday'); ?></a>
			<a href="#" class="pj-button btn-all"><?php __('lblAll'); ?></a>
			<a href="#" class="pj-button btn-confirmed"><?php echo $rs['Confirmed']; ?></a>
			<a href="#" class="pj-button btn-pending"><?php echo $rs['Pending']; ?></a>
			<a href="#" class="pj-button btn-cancelled"><?php echo $rs['Cancelled']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<div class="float_left w410">
				<p>
					<label class="title"><?php __('lblReservationUuid'); ?></label>
					<input type="text" name="uuid" class="pj-form-field w200" value="<?php echo isset($_GET['uuid']) ? htmlspecialchars($_GET['uuid']) : NULL; ?>" />
				</p>
				<p>
					<label class="title"><?php __('lblReservationName'); ?></label>
					<input type="text" name="c_name" class="pj-form-field w200" value="<?php echo isset($_GET['c_name']) ? htmlspecialchars($_GET['c_name']) : NULL; ?>" />
				</p>
				<p>
					<label class="title"><?php __('lblReservationEmail'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
						<input type="text" name="c_email" class="pj-form-field email w180" value="<?php echo isset($_GET['c_email']) ? pjSanitize::html($_GET['c_email']) : NULL; ?>" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('lblReservationFilterDates'); ?></label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_from" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : NULL; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_to" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : NULL; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
					<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
				</p>
			</div>
			<div class="float_right w290">
				<p>
					<label class="title" style="width: 130px"><?php __('lblReservationCalendar'); ?></label>
					<select name="calendar_id" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['calendar_arr'] as $v)
						{
							?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['calendar_id']) && (int) $_GET['calendar_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['name']); ?></option><?php
						}
						?>
					</select>
				</p>
				<p>
					<label class="title" style="width: 130px"><?php __('lblReservationStatus'); ?></label>
					<select name="status" class="pj-form-field w150">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('reservation_statuses', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"<?php echo isset($_GET['status']) && $_GET['status'] == $k ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v); ?></option><?php
						}
						?>
					</select>
				</p>
				<p>
					<label class="title" style="width: 130px"><?php __('lblReservationAmount'); ?></label>
					<span class="align_top lh30 inline_block w45"><?php __('lblReservationFrom'); ?></span>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="amount_from" class="pj-form-field w60 number align_right" />
					</span>
				</p>
				<p>
					<label class="title" style="width: 130px">&nbsp;</label>
					<span class="align_top lh30 inline_block w45"><?php __('lblReservationTo'); ?></span>
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="amount_to" class="pj-form-field w60 number align_right" />
					</span>
				</p>
			</div>
			<br class="clear_both" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.queryString = "";
	<?php
	if (isset($_GET['listing_id']) && (int) $_GET['listing_id'] > 0)
	{
		?>pjGrid.queryString += "&listing_id=<?php echo (int) $_GET['listing_id']; ?>";<?php
	}
	if (isset($_GET['calendar_id']) && (int) $_GET['calendar_id'] > 0)
	{
		?>pjGrid.queryString += "&calendar_id=<?php echo (int) $_GET['calendar_id']; ?>";<?php
	}
	if (isset($_GET['date']) && !empty($_GET['date']))
	{
		?>pjGrid.queryString += "&date=<?php echo $_GET['date']; ?>";<?php
	}
	if (isset($_GET['status']) && !empty($_GET['status']))
	{
		?>pjGrid.queryString += "&status=<?php echo $_GET['status']; ?>";<?php
	}
	if (isset($_GET['last_7days']) && !empty($_GET['last_7days']))
	{
		?>pjGrid.queryString += "&last_7days=<?php echo $_GET['last_7days']; ?>";<?php
	}
	if (isset($_GET['current_week']) && !empty($_GET['current_week']))
	{
		?>pjGrid.queryString += "&current_week=<?php echo $_GET['current_week']; ?>";<?php
	}
	?>
	pjGrid.isAdmin = <?php echo $controller->isAdmin() ? 1 : 0; ?>;
	var myLabel = myLabel || {};
	myLabel.client_name = "<?php __('lblReservationName'); ?>";
	myLabel.from = "<?php __('lblReservationFrom'); ?>";
	myLabel.to = "<?php __('lblReservationTo'); ?>";
	myLabel.calendar = "<?php __('lblReservationCalendar'); ?>";
	myLabel.edit = "<?php __('lblEdit'); ?>";
	myLabel.delete = "<?php __('lblDelete'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.pending = "<?php echo $rs['Pending']; ?>";
	myLabel.confirmed = "<?php echo $rs['Confirmed']; ?>";
	myLabel.cancelled = "<?php echo $rs['Cancelled']; ?>";
	myLabel.exportSelected = "<?php __('lblExportSelected'); ?>";
	myLabel.deleteSelected = "<?php __('lblDeleteSelected'); ?>";
	myLabel.deleteConfirmation = "<?php __('lblDeleteConfirmation'); ?>";
	</script>
	<?php
}
?>