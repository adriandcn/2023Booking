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
	$plugin_menu = PJ_VIEWS_PATH . sprintf('pjLayouts/elements/menu_%s.php', $controller->getConst('PLUGIN_NAME'));
	if (is_file($plugin_menu))
	{
		include $plugin_menu;
	}
	
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	
	<?php pjUtil::printNotice(@$titles['PPE03'], @$bodies['PPE03']); ?>
	
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPeriod"><?php __('plugin_period_menu'); ?></a></li>
		</ul>
	</div>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPeriod&amp;action=pjActionIndex" method="post" class="form pj-form" id="frmPeriods">
		<input type="hidden" name="period_create" value="1" />
		<table id="tblPeriods" class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th class="sub"><?php __('plugin_period_start_date'); ?></th>
					<th class="sub"><?php __('plugin_period_end_date'); ?></th>
					<th class="sub"><?php __('plugin_period_from_day'); ?></th>
					<th class="sub"><?php __('plugin_period_to_day'); ?></th>
					<th class="sub">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$days = __('days', true);
			$days[7] = $days[0];
			$days[0] = NULL;
			unset($days[0]);
			foreach ($tpl['period_arr'] as $period)
			{
				?>
				<tr class="mainPeriod">
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="start_date[<?php echo $period['id']; ?>]" class="pj-form-field pointer datepick w80" value="<?php echo pjUtil::formatDate($period['start_date'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</td>
					<td>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="end_date[<?php echo $period['id']; ?>]" class="pj-form-field pointer datepick w80" value="<?php echo pjUtil::formatDate($period['end_date'], "Y-m-d", $tpl['option_arr']['o_date_format']); ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</td>
					<td>
						<select name="from_day[<?php echo $period['id']; ?>]" class="pj-form-field">
						<?php
						foreach ($days as $index => $day)
						{
							?><option value="<?php echo $index; ?>"<?php echo $period['from_day'] == $index ? ' selected="selected"' : NULL; ?>><?php echo $day; ?></option><?php
						}
						?>
						</select>
					</td>
					<td>
						<select name="to_day[<?php echo $period['id']; ?>]" class="pj-form-field">
						<?php
						foreach ($days as $index => $day)
						{
							?><option value="<?php echo $index; ?>"<?php echo $period['to_day'] == $index ? ' selected="selected"' : NULL; ?>><?php echo $day; ?></option><?php
						}
						?>
						</select>
					</td>
					<td class="w30"><a class="pj-table-icon-delete btnDeletePeriod" data-id="<?php echo $period['id']; ?>" href="#"></a></td>
				</tr>
				<tr>
					<td colspan="2" class="align_right"><?php __('plugin_period_default'); ?></td>
					<td colspan="3">
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="default_price[<?php echo $period['id']; ?>]" class="pj-form-field align_right w70 number" value="<?php echo $period['default_price']; ?>" />
						</span>
					</td>
				</tr>
				<?php
				if (isset($period['price_arr']))
				{
					foreach ($period['price_arr'] as $item)
					{
						?>
						<tr>
							<td colspan="2" class="align_right">
								<?php __('plugin_period_adults'); ?>:
								<select name="adults[<?php echo $period['id']; ?>][]" class="pj-form-field w60">
								<?php
								foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
								{
									?><option value="<?php echo $i; ?>"<?php echo $item['adults'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
								}
								?>
								</select>
								<?php __('plugin_period_children'); ?>:
								<select name="children[<?php echo $period['id']; ?>][]" class="pj-form-field w60"><?php
								foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
								{
									?><option value="<?php echo $i; ?>"<?php echo $item['children'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
								}
								?>
								</select>
							</td>
							<td colspan="2">
								<span class="pj-form-field-custom pj-form-field-custom-before">
									<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
									<input type="text" name="price[<?php echo $period['id']; ?>][]" class="pj-form-field align_right w70 number" value="<?php echo $item['price']; ?>" />
								</span>
							</td>
							<td><a href="#" class="pj-table-icon-delete btnRemoveAdultsChildren"></a></td>
						</tr>
						<?php
					}
				}
				?>
				<tr>
					<td colspan="2" class="align_right"><input type="button" class="pj-button btnAdultsChildren" value="<?php __('plugin_period_adults_children'); ?>" /></td>
					<td colspan="3"></td>
				</tr>
				<?php
			}
			if (count($tpl['period_arr']) === 0)
			{
				ob_start();
				include dirname(__FILE__) . '/elements/periods_tpl.php';
				$content = ob_get_contents();
				ob_end_clean();
				echo str_replace('{INDEX}', 'new_'.rand(1, 99999), $content);
			}
			?>
			</tbody>
		</table>
		
		<div>
			<input type="submit" value="<?php __('plugin_period_save'); ?>" class="pj-button" />
			<input type="button" value="<?php __('plugin_period_add_period'); ?>" class="pj-button btnAddPeriod" />
			<span class="bxPeriodStatus bxPeriodStatusStart" style="display: none"><?php __('plugin_period_status_start'); ?></span>
			<span class="bxPeriodStatus bxPeriodStatusEnd" style="display: none"><?php __('plugin_period_status_end'); ?></span>
		</div>
	</form>
	
	<div id="dialogDeletePeriod" style="display: none" title="<?php __('plugin_period_del_title'); ?>"><?php __('plugin_period_del_desc'); ?></div>
	
	<table id="periodAdults" style="display: none">
		<tbody>
			<tr>
				<td colspan="2" class="align_right">
					<?php __('plugin_period_adults'); ?>:
					<select name="adults[{INDEX}][]" class="pj-form-field w60">
					<?php
					foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
					</select>
					<?php __('plugin_period_children'); ?>:
					<select name="children[{INDEX}][]" class="pj-form-field w60"><?php
					foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
					{
						?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
					}
					?>
					</select>
				</td>
				<td colspan="2">
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[{INDEX}][]" class="pj-form-field align_right w70 number" />
					</span>
				</td>
				<td><a href="#" class="pj-table-icon-delete btnRemoveAdultsChildren"></a></td>
			</tr>
		</tbody>
	</table>
	<table id="periodDefault" style="display: none">
		<tbody><?php include dirname(__FILE__) . '/elements/periods_tpl.php'; ?></tbody>
	</table>
	<?php
}
?>