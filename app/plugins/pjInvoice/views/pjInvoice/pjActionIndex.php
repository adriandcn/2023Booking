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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], !isset($_GET['errTime']) ? @$bodies[$_GET['err']] : $_SESSION[$controller->invoiceErrors][$_GET['errTime']]);
	}
	include PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	?>
	<form action="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjInvoice&amp;action=pjActionIndex" id="frmInvoiceConfig" method="post" class="pj-form form" enctype="multipart/form-data">
		<input type="hidden" name="invoice_post" value="1" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('plugin_invoice_i_company_information');?></a></li>
				<li><a href="#tabs-2"><?php __('plugin_invoice_i_invoice_config');?></a></li>
				<li><a href="#tabs-3"><?php __('plugin_invoice_i_invoice_template');?></a></li>
			</ul>
			<div id="tabs-1">
				<?php
				pjUtil::printNotice($titles['PIN13'], $bodies['PIN13'], false);
				?>
				<?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<?php endif; ?>
				<p>
					<label class="title"><?php __('plugin_invoice_i_logo'); ?></label>
					<span class="left" id="plugin_invoice_box_logo">
						<?php
						if (!empty($tpl['arr']['y_logo']) && is_file($tpl['arr']['y_logo']))
						{
							?><img src="<?php echo $tpl['arr']['y_logo']; ?>" alt="" class="align_middle" />
							<input type="button" class="pj-button plugin_invoice_delete_logo" value="<?php __('lblDelete'); ?>" /><?php
						} else {
							?><input type="file" name="y_logo" id="y_logo" class="pj-form-field w350"/><?php
						}
						?>
					</span>
				</p>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('plugin_invoice_i_company'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][y_company]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_company']); ?>" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
						</span>
					</p>
					<?php
				}
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('plugin_invoice_i_name'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][y_name]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_name']); ?>" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
						</span>
					</p>
					<?php
				}
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('plugin_invoice_i_street_address'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][y_street_address]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_street_address']); ?>" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
						</span>
					</p>
					<?php
				}
				?>
				<p>
					<label class="title"><?php __('plugin_invoice_i_country'); ?></label>
					<span class="left">
						<select name="y_country" class="pj-form-field w400">
							<option value="">----</option>
							<?php
							foreach ($tpl['country_arr'] as $country)
							{
								?><option value="<?php echo $country['id']; ?>"<?php echo $country['id'] == @$tpl['arr']['y_country'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($country['name']); ?></option><?php
							}
							?>
						</select>
					</span>
				</p>
				<?php 
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('plugin_invoice_i_city'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][y_city]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_city']); ?>" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
						</span>
					</p>
					<?php
				}
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<label class="title"><?php __('plugin_invoice_i_state'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][y_state]" class="pj-form-field w400<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_state']); ?>" />
								<?php if ($tpl['is_flag_ready']) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif; ?>
						</span>
					</p>
					<?php
				}
				?>
				<p>
					<label class="title"><?php __('plugin_invoice_i_zip'); ?></label>
					<span class="left">
						<input type="text" name="y_zip" id="y_zip" class="pj-form-field w400" value="<?php echo pjSanitize::html(@$tpl['arr']['y_zip']); ?>" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('plugin_invoice_i_phone'); ?></label>
					<span class="left">
						<input type="text" name="y_phone" id="y_phone" class="pj-form-field w400" value="<?php echo pjSanitize::html(@$tpl['arr']['y_phone']); ?>" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('plugin_invoice_i_fax'); ?></label>
					<span class="left">
						<input type="text" name="y_fax" id="y_fax" class="pj-form-field w400" value="<?php echo pjSanitize::html(@$tpl['arr']['y_fax']); ?>" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('plugin_invoice_i_email'); ?></label>
					<span class="left">
						<input type="text" name="y_email" id="y_email" class="pj-form-field w400" value="<?php echo pjSanitize::html(@$tpl['arr']['y_email']); ?>" />
					</span>
				</p>
				<p>
					<label class="title"><?php __('plugin_invoice_i_url'); ?></label>
					<span class="left">
						<input type="text" name="y_url" id="y_url" class="pj-form-field w400" value="<?php echo pjSanitize::html(@$tpl['arr']['y_url']); ?>" />
					</span>
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('plugin_invoice_save'); ?>" class="pj-button float_left align_middle" />
				</p>
			</div><!-- tabs-1 -->
			<div id="tabs-2">
				<?php
				pjUtil::printNotice($titles['PIN14'], $bodies['PIN14'], false);
				?>
				<?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<br class="clear_both" /><br />
				<?php endif; ?>
				<table class="pj-table b10" cellpadding="0" cellspacing="0" style="width: 100%">
					<thead>
						<tr>
							<th><?php __('plugin_invoice_i_option'); ?></th>
							<th><?php __('plugin_invoice_i_value'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php __('plugin_invoice_i_accept_payments'); ?></td>
							<td><input type="checkbox" class="align_middle" name="p_accept_payments" value="1"<?php echo (int) $tpl['arr']['p_accept_payments'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_accept_paypal'); ?></td>
							<td><input type="checkbox" name="p_accept_paypal" data-box=".boxPaypal" value="1"<?php echo (int) $tpl['arr']['p_accept_paypal'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr class="boxPaypal" style="display: <?php echo (int) $tpl['arr']['p_accept_paypal'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_paypal_address'); ?></td>
							<td>
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<span class="inline_block">
										<input type="text" name="i18n[<?php echo $v['id']; ?>][p_paypal_address]" class="pj-form-field w200" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['p_paypal_address']); ?>" />
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif; ?>
									</span>
								</p>
								<?php
							}
							?>
							</td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_accept_authorize'); ?></td>
							<td><input type="checkbox" name="p_accept_authorize" data-box=".boxAuthorize" value="1"<?php echo (int) $tpl['arr']['p_accept_authorize'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr class="boxAuthorize" style="display: <?php echo (int) $tpl['arr']['p_accept_authorize'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_authorize_tz'); ?></td>
							<td>
								<select name="p_authorize_tz" class="pj-form-field">
								<?php
								foreach ($tpl['timezones'] as $k => $v)
								{
									?><option value="<?php echo $k; ?>"<?php echo $k == $tpl['arr']['p_authorize_tz'] ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
								}
								?>
								</select>
							</td>
						</tr>
						<tr class="boxAuthorize" style="display: <?php echo (int) $tpl['arr']['p_accept_authorize'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_authorize_key'); ?></td>
							<td><input type="text" name="p_authorize_key" class="pj-form-field w200" value="<?php echo pjSanitize::html($tpl['arr']['p_authorize_key']); ?>" /></td>
						</tr>
						<tr class="boxAuthorize" style="display: <?php echo (int) $tpl['arr']['p_accept_authorize'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_authorize_mid'); ?></td>
							<td><input type="text" name="p_authorize_mid" class="pj-form-field w200" value="<?php echo pjSanitize::html($tpl['arr']['p_authorize_mid']); ?>" /></td>
						</tr>
						<tr class="boxAuthorize" style="display: <?php echo (int) $tpl['arr']['p_accept_authorize'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_authorize_hash'); ?></td>
							<td><input type="text" name="p_authorize_hash" class="pj-form-field w200" value="<?php echo pjSanitize::html($tpl['arr']['p_authorize_hash']); ?>" /></td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_accept_creditcard'); ?></td>
							<td><input type="checkbox" name="p_accept_creditcard" value="1"<?php echo (int) $tpl['arr']['p_accept_creditcard'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_accept_cash'); ?></td>
							<td><input type="checkbox" name="p_accept_cash" value="1"<?php echo (int) $tpl['arr']['p_accept_cash'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_accept_bank'); ?></td>
							<td><input type="checkbox" name="p_accept_bank" data-box=".boxBank" value="1"<?php echo (int) $tpl['arr']['p_accept_bank'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr class="boxBank" style="display: <?php echo (int) $tpl['arr']['p_accept_bank'] === 1 ? NULL : 'none'; ?>">
							<td><?php __('plugin_invoice_i_bank_account'); ?></td>
							<td>
							<?php
							foreach ($tpl['lp_arr'] as $v)
							{
								?>
								<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
									<span class="inline_block">
										<textarea name="i18n[<?php echo $v['id']; ?>][p_bank_account]" class="pj-form-field w250 h50"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['p_bank_account']); ?></textarea>
										<?php if ($tpl['is_flag_ready']) : ?>
										<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
										<?php endif; ?>
									</span>
								</p>
								<?php
							}
							?>
							</td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_use_shipping_details');?></td>
							<td><input type="checkbox" class="align_middle" name="si_include" value="1"<?php echo (int) $tpl['arr']['si_include'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
						<tr>
							<td><?php __('plugin_invoice_i_use_qty_unit_price');?></td>
							<td><input type="checkbox" class="align_middle" name="o_use_qty_unit_price" value="1"<?php echo (int) $tpl['arr']['o_use_qty_unit_price'] === 1 ? ' checked="checked"' : NULL; ?> /></td>
						</tr>
					</tbody>
				</table>
				
				<p>
					<input type="submit" value="<?php __('plugin_invoice_save'); ?>" class="pj-button float_left align_middle" />
				</p>
			</div><!-- tabs-2 -->
			<div id="tabs-3">
				<?php
				pjUtil::printNotice($titles['PIN15'], $bodies['PIN15'], false);
				?>
				<?php if ($tpl['is_flag_ready']) : ?>
				<div class="multilang"></div>
				<br class="clear_both" />
				<?php endif; ?>
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
						<span class="inline_block">
							<textarea name="i18n[<?php echo $v['id']; ?>][y_template]" class="pj-form-field w700 w600 mceEditor<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['y_template']); ?></textarea>
							<?php if ($tpl['is_flag_ready']) : ?>
							<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
							<?php endif; ?>
						</span>
					</p>
					<?php
				}
				?>
				<p>
					<input type="submit" value="<?php __('plugin_invoice_save'); ?>" class="pj-button float_left align_middle" />
				</p>
			</div><!-- tabs-3 -->
		</div>
	</form>
	
	<div id="dialogDeleteLogo" style="display: none" title="<?php __('plugin_invoice_delete_logo_title'); ?>"><?php __('plugin_invoice_delete_logo_body'); ?></div>
	
	<script type="text/javascript">
	<?php if ($tpl['is_flag_ready']) : ?>
	var pjLocale = pjLocale || {};
	pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
	pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: pjLocale.langs,
				flagPath: pjLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
		});
	})(jQuery_1_8_2);
	<?php endif; ?>
	
	var myLabel = myLabel || {};
	myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
	myLabel.btn_delete = "<?php __('lblDelete'); ?>";
	(function ($) {
	$(function() {
		<?php
		if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
		{		
			$tab_id = $_GET['tab_id'];
			$tab_id = $tab_id < 0 ? 0 : $tab_id; 
			?>$("#tabs").tabs("option", "selected", <?php echo str_replace("tabs-", "", $tab_id) - 1;?>);<?php
		}
		?>
	});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>