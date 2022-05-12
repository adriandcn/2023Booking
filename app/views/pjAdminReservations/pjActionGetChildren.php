<label class="title"><?php __('lblReservationChildren'); ?></label>
	<span class="inline_block">
		<select name="c_children" id="c_children" class="pj-form-field<?php echo (int) $tpl['option_arr']['o_bf_children'] === 3 ? ' required' : NULL; ?>">
		<option value="">-- <?php __('lblChoose'); ?> --</option>
		<?php
		if(!empty($tpl['option_arr'])){
			foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
			{
				?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
			}
		}
		?>
		</select>
	</span>