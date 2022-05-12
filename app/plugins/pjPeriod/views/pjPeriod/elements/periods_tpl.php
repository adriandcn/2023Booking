<tr class="mainPeriod">
	<td>
		<span class="pj-form-field-custom pj-form-field-custom-after">
			<input type="text" name="start_date[{INDEX}]" class="pj-form-field pointer datepick w80" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
			<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
		</span>
	</td>
	<td>
		<span class="pj-form-field-custom pj-form-field-custom-after">
			<input type="text" name="end_date[{INDEX}]" class="pj-form-field pointer datepick w80" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
			<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
		</span>
	</td>
	<td>
		<select name="from_day[{INDEX}]" class="pj-form-field">
		<?php
		foreach ($days as $index => $day)
		{
			?><option value="<?php echo $index; ?>"><?php echo $day; ?></option><?php
		}
		?>
		</select>
	</td>
	<td>
		<select name="to_day[{INDEX}]" class="pj-form-field">
		<?php
		foreach ($days as $index => $day)
		{
			?><option value="<?php echo $index; ?>"><?php echo $day; ?></option><?php
		}
		?>
		</select>
	</td>
	<td class="w30"><a class="pj-table-icon-delete btnRemovePeriod" href="#"></a></td>
</tr>
<tr>
	<td colspan="2" class="align_right"><?php __('plugin_period_default'); ?></td>
	<td colspan="3">
		<span class="pj-form-field-custom pj-form-field-custom-before">
			<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
			<input type="text" name="default_price[{INDEX}]" class="pj-form-field align_right w70 number" />
		</span>
	</td>
</tr>
<tr>
	<td colspan="2" class="align_right"><input type="button" class="pj-button btnAdultsChildren" value="<?php __('plugin_period_adults_children'); ?>" /></td>
	<td colspan="3"></td>
</tr>