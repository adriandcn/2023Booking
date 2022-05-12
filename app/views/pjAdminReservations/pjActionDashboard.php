<?php
if (isset($tpl['status']))
{
	switch ($tpl['status'])
	{
		case 2:
			Util::printNotice($AC_LANG['status'][2]);
			break;
	}
} else {
	?>
	<style type="text/css">
	.cal-program span{
		cursor: default !important;
	}
	</style>
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('menuAvailability'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<div id="boxDashboard"><?php include dirname(__FILE__) . '/pjActionGetDashboard.php'; ?></div>
		</div>
	</div>
	<?php
}
?>