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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionIndex"><?php __('menuCalendars'); ?></a></li>
			<li class="ui-state-default ui-corner-top">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionCreate"><?php __('lblAddCalendar'); ?></a></li>
		</ul>
	</div>

	<?php pjUtil::printNotice(@$titles['ACR10'], @$bodies['ACR10']); ?>

	<div class="b10">
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		<div class="float_right t5"></div>
		<br class="clear_both" />
	</div>
	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.currentCalendarId = <?php echo $controller->getForeignId(); ?>;
	console.log(pjGrid.currentCalendarId);
	pjGrid.queryString = "";
	var myLabel = myLabel || {};
	myLabel.prices = "<?php __('plugin_price_menu', false, true); ?>";
	myLabel.settings = "<?php __('menuSettings'); ?>";
	myLabel.edit = "<?php __('lblEdit'); ?>";
	myLabel.delete = "<?php __('lblDelete'); ?>";
	myLabel.installPreview = "<?php __('menuInstallPreview'); ?>";
	myLabel.viewReservations = "<?php __('lblViewReservations'); ?>";
	myLabel.viewCalendar = "<?php __('lblViewCalendar'); ?>";
	myLabel.more = "<?php __('lblMore'); ?>";
	myLabel.user = "<?php __('lblUser'); ?>";
	myLabel.id = "<?php __('lblID'); ?>";
	myLabel.calendar = "<?php __('lblCalendarName'); ?>";
	myLabel.deleteSelected = "<?php __('lblDeleteSelected'); ?>";
	myLabel.deleteConfirmation = "<?php __('lblDeleteConfirmation'); ?>";
	</script>
	<?php
}
?>