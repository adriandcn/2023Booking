<?php __('plugin_price_adults'); ?>:
<select name="{INDEX}_adults[{RAND}]" class="pj-form-field w50">
<?php
foreach (range(1, $tpl['option_arr']['o_bf_adults_max']) as $i)
{
	?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
}
?>
</select>
<?php __('plugin_price_children'); ?>:
<select name="{INDEX}_children[{RAND}]" class="pj-form-field w50"><?php
foreach (range(0, $tpl['option_arr']['o_bf_children_max']) as $i)
{
	?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
}
?>
</select>
<input type="hidden" name="{INDEX}_date_from[{RAND}]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />
<input type="hidden" name="{INDEX}_date_to[{RAND}]" value="<?php echo pjUtil::formatDate('0000-00-00', 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" />