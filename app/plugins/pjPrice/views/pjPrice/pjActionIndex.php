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

	<?php pjUtil::printNotice(@$titles['PPR03'], @$bodies['PPR03']); ?>
	<?php
	if (isset($tpl['overlap_arr']) && !empty($tpl['overlap_arr']))
	{
		pjUtil::printNotice(@$titles['PPR09'], @$bodies['PPR09']);
	}

	$titulo = "Deshabilitar Dias";
	$cuerpo = "Estimado Usuario, al configurar un precio igual a 0.16, automaticamente se deshabilitara el dia en el calendario.";
		pjUtil::printNotice($titulo, $cuerpo);
	?>

	<style type="text/css">
	.ui-tabs .ui-tabs-panel {padding: 5px 3px}
	#tabs{margin: 0 0 10px}
	#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
	</style>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionCreate" method="post" class="form" id="frmCreatePrice">
		<input type="hidden" name="price_create" value="1" />
		<div id="tabs">
			<ul>
				<?php
				$count = count($tpl['arr']);
				if ($count > 0)
				{
					$idx = array();
					foreach ($tpl['arr'] as $season => $season_arr)
					{
						$idx[] = $season_arr[0]['tab_id'];
					}
					$idx = array_unique($idx);
					sort($idx, SORT_NUMERIC);

					$br = 0;
					foreach ($tpl['arr'] as $season => $season_arr)
					{
						$index = $br > 0 ? $idx[$br] : 1;
						?>
						<li>
							<a href="#tabs-<?php echo $index; ?>"><?php echo stripslashes($season); ?></a>
							<?php
							if ($br > 0)
							{
								?><span class="ui-icon ui-icon-close"></span><?php
							}
							?>
						</li>
						<?php
						$br++;
					}
				} else {
					?><li><a href="#tabs-1"><?php __('plugin_price_default'); ?></a></li><?php
				}
				?>
			</ul>
			<?php
			$tmp = __('plugin_price_days', true);
			$days = array(
				'monday' => $tmp['monday'],
				'tuesday' => $tmp['tuesday'],
				'wednesday' => $tmp['wednesday'],
				'thursday' => $tmp['thursday'],
				'friday' => $tmp['friday'],
				'saturday' => $tmp['saturday'],
				'sunday' => $tmp['sunday']
			);
			if ($count > 0)
			{
				//$index = 1;
				$br = 0;
				foreach ($tpl['arr'] as $season => $season_arr)
				{
					$index = $br > 0 ? $idx[$br] : 1;
					?>
					<div id="tabs-<?php echo $index; ?>">
						<?php
						if ($index > 1)
						{
							?>
							<div class="t10 b10">
								<label><?php __('plugin_price_season_name'); ?>:</label>
								<input type="text" class="pj-form-field w300" name="tabs[<?php echo $season_arr[0]['tab_id']; ?>]" value="<?php echo htmlspecialchars(stripslashes($season)); ?>" />
							</div>
							<fieldset class="fieldset white t10 b10 overflow">
								<legend><?php __('plugin_price_date_range'); ?></legend>
								<span class="block float_left pt10 r5"><?php __('plugin_price_from'); ?>:</span>
								<span class="pj-form-field-custom pj-form-field-custom-after float_left">
									<input type="text" name="<?php echo $index; ?>_date_from[<?php echo $season_arr[0]['id']; ?>]" class="pj-form-field pointer datepick required w80" value="<?php echo !empty($season_arr[0]['date_from']) && $season_arr[0]['date_from'] != '0000-00-00' ? pjUtil::formatDate($season_arr[0]['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']) : NULL; ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
								<span class="block float_left pt10 r5 l10"><?php __('plugin_price_to'); ?>:</span>
								<span class="pj-form-field-custom pj-form-field-custom-after">
									<input type="text" name="<?php echo $index; ?>_date_to[<?php echo $season_arr[0]['id']; ?>]" class="pj-form-field pointer datepick required w80" value="<?php echo !empty($season_arr[0]['date_to']) && $season_arr[0]['date_to'] != '0000-00-00' ? pjUtil::formatDate($season_arr[0]['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']) : NULL; ?>" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								</span>
							</fieldset>
							<?php
						} else {
							?><input type="hidden" name="tabs[<?php echo $season_arr[0]['tab_id']; ?>]" value="<?php echo htmlspecialchars(stripslashes($season)); ?>" /><?php
						}
						?>
						<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 99%">
							<thead>
								<tr>
									<?php
									foreach ($days as $k => $v)
									{
										?><th class="sub"><?php echo $v; ?></th><?php
									}
									?>
									<th class="sub" style="width: 4%">&nbsp;</th>
								</tr>
							</thead>
							<tbody>
							<?php
							foreach ($season_arr as $key => $price)
							{
								if ($key == 0)
								{
								?>
								<tr>
									<td colspan="7">
									<?php
										if ($index > 1)
										{
											__('plugin_price_default');
											?>
											<input type="hidden" name="<?php echo $index; ?>_adults[<?php echo $price['id']; ?>]" value="<?php echo (int) $price['adults']; ?>" />
											<input type="hidden" name="<?php echo $index; ?>_children[<?php echo $price['id']; ?>]" value="<?php echo (int) $price['children']; ?>" />
											<?php
										} else {
											?>
											<?php __('plugin_price_default'); ?>
											<input type="hidden" name="1_date_from[<?php echo $price['id']; ?>]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
											<input type="hidden" name="1_date_to[<?php echo $price['id']; ?>]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
											<input type="hidden" name="1_adults[<?php echo $price['id']; ?>]" value="0" />
											<input type="hidden" name="1_children[<?php echo $price['id']; ?>]" value="0" />
											<?php
										}
										?>
										</td>
										<td rowspan="2"></td>
									</tr>
									<?php
									} else {
										?>
									<tr>
										<td colspan="7">
										<?php __('plugin_price_adults'); ?>:
										<select name="<?php echo $price['tab_id']; ?>_adults[<?php echo $price['id']; ?>]" class="pj-form-field w50">
										<?php
										foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
										{
											?><option value="<?php echo $i; ?>"<?php echo $price['adults'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
										}
										?>
										</select>
										<?php __('plugin_price_children'); ?>:
										<select name="<?php echo $price['tab_id']; ?>_children[<?php echo $price['id']; ?>]" class="pj-form-field w50">
										<?php
										foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
										{
											?><option value="<?php echo $i; ?>"<?php echo $price['children'] == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
										}
										?>
										</select>
									</td>
										<td rowspan="2"><a class="pj-table-icon-delete lnkRemoveRow" href="#"></a></td>
									</tr>
									<?php
									}
									?>
								<tr>
									<?php
									$i = 1;
									foreach ($days as $k => $v)
									{
										if ($i > 6)
										{
											$i = 0;
										}
										/*?><td><input type="text" name="<?php echo $price['tab_id']; ?>_day_<?php echo $i; ?>[<?php echo $price['id']; ?>]" value="<?php echo $price[substr($k, 0, 3)]; ?>" class="pj-form-field w50 align_right" /></td><?php*/
										?><td>
											<span class="pj-form-field-custom pj-form-field-custom-before w87">
												<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
												<input type="text" name="<?php echo $price['tab_id']; ?>_day_<?php echo $i; ?>[<?php echo $price['id']; ?>]" value="<?php echo $price[substr($k, 0, 3)]; ?>" class="pj-form-field align_right w46 required number" />
											</span>
										</td><?php
										$i++;
									}
									?>
								</tr>
								<?php
								if ($key == 0)
								{
									?>
									<tr>
										<td colspan="9" class="bold"><?php __('plugin_price_special_price'); ?></td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="9" class="p10"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionIndex" class="pj-button lnkAddPrice" rel="<?php echo $price['tab_id']; ?>"><?php __('plugin_price_adults_children'); ?></a></td>
								</tr>
							</tfoot>
						</table>
					</div> <!-- tabs-x -->
					<?php
					//$index++;
					$br++;
				}
			} else {
				$rand = 'x_' . rand(100000, 999999);
				?>
				<div id="tabs-1">
					<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 99%">
						<thead>
							<tr>
								<?php
								foreach ($days as $k => $v)
								{
									?><th class="sub"><?php echo $v; ?></th><?php
								}
								?>
								<th class="sub" style="width: 4%">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="7"><?php __('plugin_price_default'); ?>
									<input type="hidden" name="1_adults[<?php echo $rand; ?>]" value="0" />
									<input type="hidden" name="1_children[<?php echo $rand; ?>]" value="0" />
									<input type="hidden" name="1_date_from[<?php echo $rand; ?>]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
									<input type="hidden" name="1_date_to[<?php echo $rand; ?>]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
								</td>
								<td rowspan="2">&nbsp;</td>
							</tr>
							<tr>
								<?php
								$i = 1;
								foreach ($days as $k => $v)
								{
									if ($i > 6)
									{
										$i = 0;
									}
									/*?><td><input type="text" name="1_day_<?php echo $i; ?>[<?php echo $rand; ?>]" value="" class="pj-form-field w50 align_right" /></td><?php*/
									?><td>
										<span class="pj-form-field-custom pj-form-field-custom-before w87">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
											<input type="text" name="1_day_<?php echo $i; ?>[<?php echo $rand; ?>]" value="" class="pj-form-field align_right w46 required number" />
										</span>
									</td><?php
									$i++;
								}
								?>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="9" class="p10"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionIndex" class="pj-button icon-prices lnkAddPrice" rel="1"><?php __('plugin_price_adults_children'); ?></a></td>
							</tr>
						</tfoot>
					</table>
					<input type="hidden" name="tabs[1]" value="<?php __('plugin_price_default', false, true); ?>" />
				</div>
				<?php
			}
			?>
		</div>
		<input type="submit" class="pj-button" value="<?php __('plugin_price_save'); ?>" />
		<input type="button" class="pj-button button_add_season" value="<?php __('plugin_price_add_season'); ?>" />
	</form>

	<div class="bxPriceErrors" style="height: 0 !important; display: none; overflow: hidden"></div>

	<div id="dialogPrices" title="<?php __('plugin_price_season_title'); ?>" style="display: none">
		<form class="form" action="" method="get">
			<p>
				<label class="title" for="tab_title"><?php __('plugin_price_season_name'); ?></label>
				<input type="text" name="tab_title" id="tab_title" value="" class="pj-form-field w200 l10" />
			</p>
		</form>
	</div>
	<div id="dialogPricesDelete" title="<?php echo pjSanitize::html(__('plugin_price_delete_title', true)); ?>" style="display: none"><?php __('plugin_price_delete_content'); ?></div>
	<div id="dialogPricesSeasonDelete" title="<?php echo pjSanitize::html(__('plugin_price_delete_season_title', true)); ?>" style="display: none"><?php __('plugin_price_delete_season_content'); ?></div>
	<div id="dialogPricesStatus" title="<?php echo pjSanitize::html(__('plugin_price_status_title', true)); ?>" style="display: none">
		<span class="bxPriceStatus bxPriceStatusStart" style="display: none"><?php __('plugin_price_status_start'); ?></span>
		<span class="bxPriceStatus bxPriceStatusEnd" style="display: none"><?php __('plugin_price_status_end'); ?></span>
		<span class="bxPriceStatus bxPriceStatusFail" style="display: none"><?php __('plugin_price_status_fail'); ?></span>
	</div>

	<div id="tmplSeason" style="display: none">
	<?php
	$prices_include = 'season';
	include dirname(__FILE__) . '/elements/prices_tpl.php';
	?>
	</div>
	<div id="tmplDefault" style="display: none">
	<?php
	$prices_include = 'default';
	include dirname(__FILE__) . '/elements/prices_tpl.php';
	?>
	</div>
	<?php
}
?>