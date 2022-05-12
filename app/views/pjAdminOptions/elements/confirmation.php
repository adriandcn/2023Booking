<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
<div class="multilang b10"></div>
<?php endif;?>

<div class="clear_both">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form clear_both">
		<input type="hidden" name="options_update" value="1" />
		<input type="hidden" name="next_action" value="pjActionIndex" />
		<input type="hidden" name="tab" value="<?php echo @$_GET['tab']; ?>" />
		
		<fieldset class="fieldset white">
			<legend><?php __('lblListingConfirmEmail'); ?></legend>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="i18n[<?php echo $v['id']; ?>][confirm_subject]" class="pj-form-field w500 b10" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['confirm_subject']); ?>" />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_reservation'); ?></label>
						<textarea name="i18n[<?php echo $v['id']; ?>][confirm_tokens]" class="pj-form-field w500 h400"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['confirm_tokens']); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</p>
				</div>
				<?php
			}
			?>
		</fieldset>
		
		<fieldset class="fieldset white">
			<legend><?php __('lblListingPaymentEmail'); ?></legend>
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<p>
						<label class="title"><?php __('opt_subject'); ?></label>
						<input type="text" name="i18n[<?php echo $v['id']; ?>][payment_subject]" class="pj-form-field w500 b10" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['payment_subject']); ?>" />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</p>
					<p>
						<label class="title"><?php __('opt_body_new_reservation'); ?></label>
						<textarea name="i18n[<?php echo $v['id']; ?>][payment_tokens]" class="pj-form-field w500 h400"><?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['payment_tokens']); ?></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</p>
				</div>
				<?php
			}
			?>
		</fieldset>
		
		<p>
			<input type="submit" class="pj-button" value="<?php __('btnSave'); ?>" />
		</p>
	</form>
</div>

<?php
$locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
if (is_null($locale))
{
	foreach ($tpl['lp_arr'] as $v)
	{
		if ($v['is_default'] == 1)
		{
			$locale = $v['id'];
			break;
		}
	}
}
if (is_null($locale))
{
	$locale = @$tpl['lp_arr'][0]['id'];
}
?>
<script type="text/javascript">
(function ($) {
	$(function() {
		$(".multilang").multilang({
			langs: <?php echo $tpl['locale_str']; ?>,
			flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
			select: function (event, ui) {
				//callback
			}
		});
		$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
	});
})(jQuery_1_8_2);
</script>