<?php
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form clear_both">
	<input type="hidden" name="options_update" value="1" />
	<input type="hidden" name="next_action" value="pjActionIndex" />
	<input type="hidden" name="tab" value="<?php echo @$_GET['tab']; ?>" />

	<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
		<thead>
			<tr>
				<th><?php __('limit_from'); ?></th>
				<th><?php __('limit_to'); ?></th>
				<th><?php __('limit_min'); ?></th>
				<th><?php __('limit_max'); ?></th>
				<th style="width: 4%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($tpl['arr'] as $limit)
		{
			$diff = abs(strtotime($limit['date_to']) - strtotime($limit['date_from'])) / 86400;
			?>
			<tr>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_from[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($limit['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_to[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo pjUtil::formatDate($limit['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</td>
				<td><input type="text" name="min_nights[]" class="pj-form-field w50 r5" value="<?php echo $limit['min_nights']; ?>" data-max="<?php echo $diff; ?>" /> <?php $tpl['option_arr']['o_price_based_on'] == 'nights' ? __('limit_nights') : __('limit_days'); ?></td>
				<td><input type="text" name="max_nights[]" class="pj-form-field w50 r5" value="<?php echo $limit['max_nights']; ?>" data-max="<?php echo $diff; ?>" /> <?php $tpl['option_arr']['o_price_based_on'] == 'nights' ? __('limit_nights') : __('limit_days'); ?></td>
				<td><a class="pj-table-icon-delete lnkRemoveRow" href="#"></a></td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" class="p10"><input type="button" class="pj-button btnAddLimit" value="<?php __('limit_add'); ?>" /></td>
			</tr>
		</tfoot>
	</table>
	
	<input type="submit" class="pj-button" value="<?php __('btnSave'); ?>" />
</form>

<table style="display: none" id="tblClone">
	<tbody>
		<tr>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date_from[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</td>
			<td>
				<span class="pj-form-field-custom pj-form-field-custom-after">
					<input type="text" name="date_to[]" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
					<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
				</span>
			</td>
			<td><input name="min_nights[]" type="text" class="pj-form-field w50 r5" /> <?php $tpl['option_arr']['o_price_based_on'] == 'nights' ? __('limit_nights') : __('limit_days'); ?></td>
			<td><input name="max_nights[]" type="text" class="pj-form-field w50 r5" /> <?php $tpl['option_arr']['o_price_based_on'] == 'nights' ? __('limit_nights') : __('limit_days'); ?></td>
			<td><a href="#" class="pj-table-icon-delete lnkRemoveRow"></a></td>
		</tr>
	</tbody>
</table>