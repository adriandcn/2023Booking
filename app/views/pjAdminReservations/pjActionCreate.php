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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex"><?php __('menuReservations'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('lblAddReservation'); ?></a></li>
			<?php
			if (pjObject::getPlugin('pjInvoice') !== NULL)
			{
				?><li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjInvoice&amp;action=pjActionInvoices"><?php __('plugin_invoice_menu_invoices'); ?></a></li><?php
			}
			?>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionExport"><?php __('lblExport'); ?></a></li>
		</ul>
	</div>
	
	<?php
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	pjUtil::printNotice(@$titles['AR18'], @$bodies['AR18']);
	
	$jquery_validation = __('jquery_validation', true);
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate" method="post" id="frmCreateReservation" class="form pj-form">
		<input type="hidden" name="reservation_create" value="1" />
		
		<fieldset class="fieldset white w350 float_left">
			<legend><?php __('lblReservationInfo'); ?></legend>
			<p>
				<label class="title"><?php __('lblReservationCalendar'); ?></label>
				<span class="inline_block">
					<select name="calendar_id" id="calendar_id" class="pj-form-field w170 required" data-msg-required="<?php echo $jquery_validation['required'];?>">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['calendars'] as $calendar)
						{
							?><option value="<?php echo $calendar['id']; ?>"<?php echo !isset($_GET['calendar_id']) || $_GET['calendar_id'] != $calendar['id'] ? NULL : ' selected="selected"'?>><?php echo pjSanitize::html($calendar['name']); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationStatus'); ?></label>
				<span class="inline_block">
					<select name="status" id="status" class="pj-form-field w170 required" data-msg-required="<?php echo $jquery_validation['required'];?>">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('reservation_statuses', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationUuid'); ?></label>
				<span class="inline_block">
					<input type="text" name="uuid" id="uuid" class="pj-form-field w170 required" value="<?php echo pjUtil::uuid(); ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationFrom'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date_from" id="date_from" class="pj-form-field pointer w80 required" data-msg-required="<?php echo $jquery_validation['required'];?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo isset($_GET['date_from']) ? pjUtil::formatDate($_GET['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']) : NULL; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationTo'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date_to" id="date_to" class="pj-form-field pointer w80 required" data-msg-required="<?php echo $jquery_validation['required'];?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					<input type="hidden" name="dates" id="dates" value="0" />
				</span>
			</p>
			<?php if (in_array($tpl['option_arr']['o_bf_adults'], array(2,3))) : ?>
			<p id="boxAdults">
				<label class="title"><?php __('lblReservationAdults'); ?></label>
				<span class="inline_block">
					<select name="c_adults" id="c_adults" class="pj-form-field<?php echo (int) $tpl['option_arr']['o_bf_adults'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach (range(0, $tpl['option_arr']['o_bf_adults_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
					</select>
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_children'], array(2,3))) : ?>
			<p id="boxChildren">
				<label class="title"><?php __('lblReservationChildren'); ?></label>
				<span class="inline_block">
					<select name="c_children" id="c_children" class="pj-form-field<?php echo (int) $tpl['option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>">
					<option value="">-- <?php __('lblChoose'); ?> --</option>
					<?php
					foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
					</select>
				</span>
			</p>
			<?php endif; ?>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCalculate'); ?>" class="pj-button btnCalculate" />
				<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('reservation_calc_tip', true)); ?>"></a>
			</p>
		</fieldset>
		
		<fieldset class="fieldset white w330 float_right">
			<legend><?php __('lblReservationAmount'); ?></legend>
			<p>
				<label class="title"><?php __('lblReservationPayment'); ?></label>
				<span class="inline_block">
					<select name="payment_method" id="payment_method" class="pj-form-field w140">
						<option value="creditcard">Credit Card</option>
						<?php
						/*
						afuera del foreach<option value="">-- <?php __('lblChoose'); ?> --</option>
						foreach (__('payment_methods', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}*/
						?>
					</select>
				</span>
			</p>
			<p class="vrCC" style="display: none">
				<label class="title"><?php __('lblReservationCCType'); ?></label>
				<span class="inline_block">
					<select name="cc_type" class="pj-form-field w140">
						<option value="">---</option>
						<?php
						foreach (__('cc_types', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<p class="vrCC" style="display: none">
				<label class="title"><?php __('lblReservationCCNum'); ?></label>
				<span class="inline_block">
					<input type="text" name="cc_num" id="cc_num" class="pj-form-field w120 digits" />
				</span>
			</p>
			<p class="vrCC" style="display: none">
				<label class="title"><?php __('lblReservationCCCode'); ?></label>
				<span class="inline_block">
					<input type="text" name="cc_code" id="cc_code" class="pj-form-field w120 digits" />
				</span>
			</p>
			<p class="vrCC" style="display: none">
				<label class="title"><?php __('lblReservationCCExp'); ?></label>
				<span class="inline_block">
					<?php
					echo pjTime::factory()
						->attr('name', 'cc_exp_month')
						->attr('id', 'cc_exp_month')
						->attr('class', 'pj-form-field')
						->prop('format', 'M')
						->month();
						
					echo pjTime::factory()
						->attr('name', 'cc_exp_year')
						->attr('id', 'cc_exp_year')
						->attr('class', 'pj-form-field')
						->prop('left', 0)
						->prop('right', 10)
						->year();
					?>
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationAmount'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="amount" id="amount" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationDeposit'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="deposit" id="deposit" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationSecurity'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="security" id="security" class="pj-form-field number w80" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('lblReservationTax'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="tax" id="tax" class="pj-form-field number w80" />
				</span>
			</p>
		</fieldset>
		
		<fieldset class="fieldset white clear_both">
			<legend><?php __('lblReservationClientInfo'); ?></legend>
			<?php if (in_array($tpl['option_arr']['o_bf_name'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationName'); ?></label>
				<span class="inline_block">
					<input type="text" name="c_name" id="c_name" class="pj-form-field w300<?php echo (int) $tpl['option_arr']['o_bf_name'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"/>
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_email'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationEmail'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
					<input type="text" name="c_email" id="c_email" class="pj-form-field email w300<?php echo (int) $tpl['option_arr']['o_bf_email'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_phone'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationPhone'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
					<input type="text" name="c_phone" id="c_phone" class="pj-form-field w200<?php echo (int) $tpl['option_arr']['o_bf_phone'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_address'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationAddress'); ?></label>
				<span class="inline_block">
					<input type="text" name="c_address" id="c_address" class="pj-form-field w400<?php echo (int) $tpl['option_arr']['o_bf_address'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_city'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationCity'); ?></label>
				<span class="inline_block">
					<input type="text" name="c_city" id="c_city" class="pj-form-field w200<?php echo (int) $tpl['option_arr']['o_bf_city'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_country'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationCountry'); ?></label>
				<span class="inline_block">
					<select name="c_country" id="c_country" class="pj-form-field w300">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach ($tpl['country_arr'] as $country)
						{
							?><option value="<?php echo $country['id']; ?>"><?php echo stripslashes($country['name']); ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_state'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationState'); ?></label>
				<span class="inline_block">
					<input type="text" name="c_state" id="c_state" class="pj-form-field w200<?php echo (int) $tpl['option_arr']['o_bf_state'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_zip'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationZip'); ?></label>
				<span class="inline_block">
					<input type="text" name="c_zip" id="c_zip" class="pj-form-field w150<?php echo (int) $tpl['option_arr']['o_bf_zip'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>" />
				</span>
			</p>
			<?php endif; ?>
			<?php if (in_array($tpl['option_arr']['o_bf_notes'], array(2,3))) : ?>
			<p>
				<label class="title"><?php __('lblReservationNotes'); ?></label>
				<span class="inline_block">
					<textarea name="c_notes" id="c_notes" class="pj-form-field w500 h80<?php echo (int) $tpl['option_arr']['o_bf_notes'] === 3 ? ' required' : NULL; ?>" data-msg-required="<?php echo $jquery_validation['required'];?>"></textarea>
				</span>
			</p>
			<?php endif; ?>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.dateRangeValidation = "<?php __('lblReservationDateRangeValidation'); ?>";
	myLabel.duplicatedUniqueID = "<?php __('lblDuplicatedUniqueID'); ?>";
	</script>
	<?php
}
?>