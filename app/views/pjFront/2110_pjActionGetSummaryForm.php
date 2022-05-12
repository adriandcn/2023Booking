<?php
include dirname(__FILE__) . '/elements/menu.php';
$STORAGE = @$_SESSION[$controller->defaultCalendar];
?>
<form action="" method="post" class="abForm">
	<div class="abBox abWhite abHeading"><?php __('bf_booking'); ?></div>
	<div class="abBox abGray">
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle"><?php __('bf_start_date'); ?></label>
				<span class="abValue"><?php echo date($tpl['option_arr']['o_date_format'], @$STORAGE['start_dt']); ?></span>
			</div>
		</div>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle"><?php __('bf_end_date'); ?></label>
				<span class="abValue"><?php echo date($tpl['option_arr']['o_date_format'], @$STORAGE['end_dt']); ?></span>
			</div>
		</div>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle">&nbsp;</label>
				<span class="abValue">
				<?php
				$nights = ceil(($STORAGE['end_dt'] - $STORAGE['start_dt']) / 86400);
		    	if ($tpl['option_arr']['o_price_based_on'] == 'days')
		    	{
		    		$nights += 1;
		    		printf("%u %s", $nights, $nights > 1 ? __('bf_days', true) : __('bf_day', true));
		    	} else {
		    		printf("%u %s", $nights, $nights > 1 ? __('bf_nights', true) : __('bf_night', true));
		    	}
		    	?>&nbsp;(<a href="#" class="abSelectorChangeDates"><?php __('lblChangeDates');?></a>)
		    	</span>
		    </div>
		</div>
		<?php
		if ((int) $tpl['option_arr']['o_bf_adults'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_adults'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_adults']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_children'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_children'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_children']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_disable_payments'] !== 1 || (int) $tpl['option_arr']['o_show_prices'] === 1)
		{
			?><div class="abSelectorPrice"><?php include dirname(__FILE__) . '/pjActionGetPrice.php'; ?></div><?php
		}
		?>
	</div>
	<div class="abBox abWhite">
		<?php
		if ((int) $tpl['option_arr']['o_bf_name'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_name'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_name']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_email'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_email'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_email']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_phone'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_phone'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_phone']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_address'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_address'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_address']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_zip'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_zip'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_zip']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_city'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_city'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_city']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_state'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_state'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$STORAGE['c_state']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_country'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_country'); ?></label>
					<span class="abValue"><?php echo stripslashes(@$tpl['country_arr']['name']); ?></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_bf_notes'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_notes'); ?></label>
					<span class="abControl"><span class="abValue"><?php echo stripslashes(@$STORAGE['c_notes']); ?></span></span>
				</div>
			</div>
			<?php
		}
		if ((int) $tpl['option_arr']['o_disable_payments'] !== 1)
		{
			?>
			<div class="abParagraph">
				<div class="abParagraphInner">
					<label class="abTitle"><?php __('bf_payment'); ?></label>
					<span class="abValue">
					<?php
					foreach (__('payment_methods', true) as $k => $v)
					{
						if ($k == @$STORAGE['payment_method'])
						{
							echo $v;
							break;
						}
					}
					?>
					</span>
				</div>
			</div>
			<?php
			switch (@$STORAGE['payment_method'])
			{
				case 'creditcard':
					?>
					<div class="abParagraph">
						<div class="abParagraphInner">
							<label class="abTitle"><?php __('bf_cc_type'); ?></label>
							<span class="abValue">
								<?php
								foreach (__('cc_types', true) as $k => $v)
								{
									if (@$STORAGE['cc_type'] == $k)
									{
										echo $v;
										break;
									}
								}
								?>
							</span>
						</div>
					</div>
					<div class="abParagraph">
						<div class="abParagraphInner">
							<label class="abTitle"><?php __('bf_cc_num'); ?></label>
							<span class="abValue"><?php echo htmlspecialchars(@$STORAGE['cc_num']); ?></span>
						</div>
					</div>
					<div class="abParagraph">
						<div class="abParagraphInner">
							<label class="abTitle"><?php __('bf_cc_sec'); ?></label>
							<span class="abValue"><?php echo htmlspecialchars(@$STORAGE['cc_code']); ?></span>
						</div>
					</div>
					<div class="abParagraph">
						<div class="abParagraphInner">
							<label class="abTitle"><?php __('bf_cc_exp'); ?></label>
							<span class="abValue"><?php echo htmlspecialchars(@$STORAGE['cc_exp_month']); ?>/<?php echo htmlspecialchars(@$STORAGE['cc_exp_year']); ?></span>
						</div>
					</div>
					<?php
					break;
				case 'bank':
					?>
					<div class="abParagraph">
						<div class="abParagraphInner">
							<label class="abTitle"><?php __('bf_bank_account'); ?></label>
							<span class="abValue"><?php echo stripslashes(nl2br($tpl['option_arr']['o_bank_account'])); ?></span>
						</div>
					</div>
					<?php
					break;
			}
		}
		?>
		<div class="abParagraph">
			<div class="abParagraphInner">
				<label class="abTitle">&nbsp;</label>
				<span class="abControl">
					<button type="button" class="abButton abButtonDefault abSelectorConfirm abFloatleft abMR5"><?php __('bf_continue'); ?></button>
					<button type="button" class="abButton abButtonCancel abSelectorReturn abFloatleft"><?php __('bf_cancel'); ?></button>
				</span>
			</div>
		</div>
		<div class="abParagraph"></div>
	</div>
</form>