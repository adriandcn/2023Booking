<?php
if (isset($tpl['price_arr']) && is_array($tpl['price_arr']))
{
	$total = $tpl['price_arr']['amount'] + $tpl['price_arr']['tax'];
	$deposit = $tpl['price_arr']['deposit'] - $tpl['price_arr']['security'];
	?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_price'); ?></label>
			<span class="abValue" id="precio1"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['amount'], 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<?php if ((float) $tpl['option_arr']['o_tax'] > 0) : ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_tax'); ?> (<?php echo $tpl['option_arr']['o_tax']?>%)</label>
			<span class="abValue"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['tax'], 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<?php endif; ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle abBold"><?php __('bf_total'); ?></label>
			<span class="abValue abPrice" id="precio2"><?php echo pjUtil::formatCurrencySign(number_format($total, 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php
			if (isset($tpl['option_arr']['o_require_all_within'])
				&& (int) $tpl['option_arr']['o_require_all_within'] > 0
				&& strtotime(date("Y-m-d")) + (int) $tpl['option_arr']['o_require_all_within'] * 86400 >= @$_SESSION[$controller->defaultCalendar]['start_dt'])
			{
				echo '100% ';
			} elseif ($tpl['option_arr']['o_deposit_type'] == 'percent') {
				echo $tpl['option_arr']['o_deposit'] . '% ';
			}
			?><?php __('bf_deposit'); ?></label>
			<span class="abValue" id="precio3"><?php echo pjUtil::formatCurrencySign(number_format($deposit, 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<?php if ((float) $tpl['option_arr']['o_security'] > 0) : ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle"><?php __('bf_security'); ?></label>
			<span class="abValue"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['security'], 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<?php endif; ?>
	<div class="abParagraph">
		<div class="abParagraphInner">
			<label class="abTitle abBold"><?php __('bf_payment_required'); ?></label>
			<span class="abValue abPrice" id="precio4"><?php echo pjUtil::formatCurrencySign(number_format($tpl['price_arr']['deposit'], 2), $tpl['option_arr']['o_currency']); ?></span>
		</div>
	</div>
	<div class="abParagraph"></div>
	<?php
}
?>