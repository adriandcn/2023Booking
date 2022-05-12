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
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	include PJ_VIEWS_PATH . 'pjLayouts/elements/calmenu.php';
	include_once dirname(__FILE__) . '/elements/settings_menu.php';
	if (isset($tpl['calendars']) && count($tpl['calendars']) > 1 && $_SESSION["comprobar"] != 1)
	{
		?>
		<div class="b5 overflow">
			<input type="hidden" name="copy_tab_id" value="<?php echo @$_GET['tab']; ?>" />
			<input type="button" value="<?php __('lblOptionCopy'); ?>" class="pj-button align_middle r3" id="btnCopyOptions" />
			<select name="copy_calendar_id" class="pj-form-field w300">
			<?php
			foreach ($tpl['calendars'] as $calendar)
			{
				if ($calendar['id'] == $controller->getForeignId())
				{
					continue;
				}
				?><option value="<?php echo $calendar['id']; ?>"><?php echo stripslashes($calendar['name']); ?></option><?php
			}
			?>
			</select>
			<a class="pj-form-langbar-tip listing-tip" href="#" title="<?php echo nl2br(__('lblOptionCopyTip', true)); ?>"></a>
		</div>

		<div id="dialogCopyOptions" style="display:none" title="<?php echo htmlspecialchars(__('lblOptionCopyTitle', true)); ?>"><?php __('lblOptionCopyDesc'); ?></div>
		<?php
	}
	switch (@$_GET['tab'])
	{
		case 5:
			pjUtil::printNotice(@$titles['AO25'], @$bodies['AO25'] . '<br/><br/>' . __('lblAvailableTokens', true, false), false);
			include PJ_VIEWS_PATH . 'pjAdminOptions/elements/confirmation.php';
			break;
		case 6:
			pjUtil::printNotice(@$titles['AO26'], @$bodies['AO26']);
			include PJ_VIEWS_PATH . 'pjAdminOptions/elements/terms.php';
			break;
		case 10:
			pjUtil::printNotice(@$titles['AO27'], @$bodies['AO27']);
			include PJ_VIEWS_PATH . 'pjAdminOptions/elements/limits.php';
			break;
		default:
			switch ($_GET['tab'])
			{
				case 4:
					pjUtil::printNotice(@$titles['AO24'], @$bodies['AO24']);
					break;
				case 3:
					pjUtil::printNotice(@$titles['AO22'], @$bodies['AO22']);
					break;
				case 7:
					pjUtil::printNotice(@$titles['AO23'], @$bodies['AO23']);
					break;
			}
			include PJ_VIEWS_PATH . 'pjAdminOptions/elements/tab.php';
	}
	?>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.btnCopy = "<?php __('btnCopy', false, true); ?>";
	myLabel.btnCancel = "<?php __('btnCancel', false, true); ?>";
	</script>
	<?php
}
?>