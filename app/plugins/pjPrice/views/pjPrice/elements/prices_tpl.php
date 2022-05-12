<?php
switch ($prices_include)
{
	case 'season':
		?>
		<div class="t10 b10">
			<label><?php __('plugin_price_season_name'); ?>:</label>
			<input type="text" class="pj-form-field w300" name="tabs[{INDEX}]" value="{TAB_TITLE}" />
		</div>
		<fieldset class="fieldset white t10 b10 overflow">
			<legend><?php __('plugin_price_date_range'); ?></legend>
			<span class="block float_left pt10 r5"><?php __('plugin_price_from'); ?>:</span>
			<span class="pj-form-field-custom pj-form-field-custom-after float_left">
				<input type="text" name="{INDEX}_date_from[{RAND}]" class="pj-form-field pointer datepick required w80" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>
			<span class="block float_left pt10 r5 l10"><?php __('plugin_price_to'); ?>:</span>
			<span class="pj-form-field-custom pj-form-field-custom-after float_left">
				<input type="text" name="{INDEX}_date_to[{RAND}]" class="pj-form-field pointer datepick required w80" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
			</span>
		</fieldset>
		<?php
		break;
	case 'default':
		?>
		<input type="hidden" name="tabs[{INDEX}]" value="{TAB_TITLE}" />
		<?php
		break;
}
?>
<table class="pj-table b10" cellspacing="0" cellpadding="0" style="width: 99%">
	<thead>
		<tr>
			<?php
			foreach ($days as $k => $v)
			{
				?><th width="9%"><?php echo $v; ?></th><?php
			}
			?>
			<th class="sub">&nbsp;</th>
  		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="7">
			<?php
			switch ($prices_include)
			{
				case 'season':
					include dirname(__FILE__) . '/prices_season.php';
					break;
				case 'default':
					include dirname(__FILE__) . '/prices_default.php';
					break;
			}
			?>
			</td>
			<td rowspan="2"><?php
			if ($prices_include == 'season')
			{
				?>&nbsp;<?php
			} else {
				?><a class="pj-table-icon-delete lnkRemoveRow" href="#"></a><?php
			}
			?></td>
		</tr>
		<tr>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_1[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_2[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_3[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_4[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_5[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_6[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
			<td><span class="pj-form-field-custom pj-form-field-custom-before w87"><span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span><input type="text" name="{INDEX}_day_0[{RAND}]" class="pj-form-field align_right w46 required number" /></span></td>
		</tr>
	</tbody>
	<tfoot>
		<tr><td colspan="9" class="p10"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionIndex" class="pj-button icon-prices lnkAddPrice" rel="{INDEX}"><?php __('plugin_price_adults_children'); ?></a></td></tr>
	</tfoot>
</table>