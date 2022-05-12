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

	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservations'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
			<?php
			if (pjObject::getPlugin('pjInvoice') !== NULL)
			{
				?><li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices&amp;foreign_id="><?php __('plugin_invoice_menu_invoices'); ?></a></li><?php
			}
			?>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>

	<?php
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);

	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	pjUtil::printNotice(@$titles['AR21'], @$bodies['AR21']);

	$export_formats = __('export_formats', true, false);
	$export_types = __('export_types', true, false);
	$export_periods = __('export_periods', true, false);
	?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionExport" method="post" id="frmExportReservations" class="form pj-form">
		<input type="hidden" name="reservation_export" value="1" />
		<p>
			<label class="title"><?php __('lblCalendar'); ?></label>
			<span class="inline_block">
				<select name="calendar_id" class="pj-form-field w200">
					<!-- <option value="0">-- <?php __('lblAll');?> --</option> -->
					<?php
					foreach ($tpl['calendar_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id']; ?>"<?php echo isset($_POST['calendar_id']) && $_POST['calendar_id'] == $v['id'] ? ' selected="selected"' : null; ?>><?php echo pjSanitize::html($v['name']); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblFormat'); ?></label>
			<span class="inline_block">
				<select name="format" id="format" class="pj-form-field w100">
					<?php
					foreach ($export_formats as $k => $v)
					{
						?><option value="<?php echo $k; ?>"<?php echo isset($_POST['format']) && $_POST['format'] == $k ? ' selected="selected"' : null; ?>><?php echo pjSanitize::html($v); ?></option><?php
					}
					?>
				</select>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblType'); ?></label>
			<span class="inline_block t5">
				<span class="block float_left r10">
					<input type="radio" name="type" id="file" value="file"<?php echo isset($_POST['type']) ? ($_POST['type'] == 'file' ? ' checked="checked"' : null) : ' checked="checked"'; ?> class="float_left r3"/>
					<span class="inline_block">
						<label for="file"><?php echo $export_types['file'];?></label>
					</span>
				</span>
				<span class="block float_left">
					<input type="radio" name="type" id="feed" value="feed"<?php echo isset($_POST['type']) ? ($_POST['type'] == 'feed' ? ' checked="checked"' : null) : null; ?> class="float_left r3"/>
					<span class="inline_block">
						<label for="feed"><?php echo $export_types['feed'];?></label>
					</span>
				</span>
			</span>
		</p>
		<p class="abPassowrdContainer" style="display:<?php echo isset($_POST['type']) ? ($_POST['type'] == 'file' ? ' none' : ' block' ) : ' none'; ?>">
			<label class="title"><?php __('lblEnterPassword');?></label>
			<span class="pj-form-field-custom pj-form-field-custom-before">
				<span class="pj-form-field-before"><abbr class="pj-form-field-icon-password"></abbr></span>
				<input type="text" id="feed_password" name="password" class="pj-form-field w200" value="<?php echo isset($_POST['password']) ? $_POST['password'] : null; ?>"/>
			</span>
		</p>
		<p>
			<label class="title"><?php __('lblReservations'); ?></label>
			<span class="inline_block">
				<span class="block float_left overflow r20">
					<select name="period" id="export_period" class="pj-form-field w200 r20 float_left">
						<option value="all"<?php echo isset($_POST['period']) ? ($_POST['period'] == 'all' ? ' selected="selected"' : null) : ' selected="selected"'; ?>>-- <?php echo pjSanitize::html($export_periods['all']); ?> --</option>
						<option value="range"<?php echo isset($_POST['period']) ? ($_POST['period'] == 'range' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['range']); ?></option>
						<option value="next"<?php echo isset($_POST['period']) ? ($_POST['period'] == 'next' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['next']); ?></option>
						<option value="last"<?php echo isset($_POST['period']) ? ($_POST['period'] == 'last' ? ' selected="selected"' : null) : null; ?>><?php echo pjSanitize::html($export_periods['last']); ?></option>
					</select>
				</span>
				<span id="next_label" class="block float_left overflow r20" style="display:<?php echo isset($_POST['period']) ? ($_POST['period'] == 'next' ? ' block' : ' none') : ' none'; ?>;">
					<select name="coming_period" id="coming_period" class="pj-form-field w150">
						<?php
						foreach(__('coming_arr', true) as $k => $v)
						{
							?><option value="<?php echo $k;?>"<?php echo isset($_POST['coming_period']) ? ($_POST['coming_period'] == $k ? ' selected="selected"' : null) : null; ?>><?php echo $v;?></option><?php
						}
						?>
					</select>
				</span>
				<span id="last_label" class="block float_left overflow r20" style="display:<?php echo isset($_POST['period']) ? ($_POST['period'] == 'last' ? ' block' : ' none') : ' none'; ?>;">
					<select name="made_period" id="made_period" class="pj-form-field w150">
						<?php
						foreach(__('made_arr', true) as $k => $v)
						{
							?><option value="<?php echo $k;?>"<?php echo isset($_POST['made_period']) ? ($_POST['made_period'] == $k ? ' selected="selected"' : null) : null; ?>><?php echo $v;?></option><?php
						}
						?>
					</select>
				</span>
				<span id="range_label" class="block float_left overflow r20" style="display:<?php echo isset($_POST['period']) ? ($_POST['period'] == 'range' ? ' block' : ' none') : ' none'; ?>;">
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_from" class="pj-form-field pointer datepick w80" readonly="readonly" value="<?php echo date($tpl['option_arr']['o_date_format'], time());?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_to" class="pj-form-field pointer datepick w80" readonly="readonly" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime("+30 days") );?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</span>
			</span>
		</p>

		<p>
			<label class="title">&nbsp;</label>
			<input type="submit" id="abSubmitButton" value="<?php isset($_POST['type']) ? ($_POST['type'] == 'file' ? __('btnExport') : __('btnGetFeedURL') ) :  __('btnExport'); ?>" class="pj-button" />
		</p>
		<?php
		if(isset($_POST['type']) && $_POST['type'] == 'feed')
		{
			?>
			<div class="abFeedContainer">
				<br/>
				<?php pjUtil::printNotice(__('infoReservationFeedTitle', true), __('infoReservationFeedDesc', true)); ?>
				<span class="inline_block">
					<textarea id="reservations_feed" name="reservations_feed" class="pj-form-field h80" style="width: 726px;"><?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionExportFeed&amp;format=<?php echo$_POST['format']; ?>&amp;calendar_id=<?php echo $_POST['calendar_id'];?>&amp;type=<?php echo $_POST['period'] == 'next' ? '1' : '2'; ?>&amp;period=<?php echo $_POST['period'] == 'next' ? $_POST['coming_period'] : $_POST['made_period']; ?>&amp;p=<?php echo isset($tpl['password']) ? $tpl['password'] : null;?></textarea>
				</span>
			</div>
			<?php
		}
		?>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.btn_export = "<?php __('btnExport'); ?>";
	myLabel.btn_get_url = "<?php __('btnGetFeedURL'); ?>";
	</script>
	<?php
}
?>
