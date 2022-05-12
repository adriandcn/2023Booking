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
	<style type="text/css">
	#abWrapper_<?php echo $controller->getForeignId(); ?> {
		float: left;
	}
	#abWrapper_<?php echo $controller->getForeignId(); ?> table.abCalendarTable{
		margin: 0 0 10px;
		height: 285px !important;
		width: 380px !important;
	}
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonth{
		height: 35px !important;
	}
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonthPrev a,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarMonthNext a{
		height: 35px !important;
		width: 40px !important;
	}
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarWeekDay,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarWeekNum,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarToday,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPending,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPast,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarEmpty,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarDate,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPendingNightsStart,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPendingNightsEnd,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReservedNightsStart,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReservedNightsEnd,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedPending,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingPending{
		height: 35px !important;
		width: 40px !important;
	}
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPending,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarPast,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsReservedPending,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingReserved,
	#abWrapper_<?php echo $controller->getForeignId(); ?> td.abCalendarNightsPendingPending{
		cursor: pointer !important;
	}
	</style>
	<?php include PJ_VIEWS_PATH . 'pjLayouts/elements/calmenu.php'; ?>
	
	<?php pjUtil::printNotice(@$titles['ACR12'], @$bodies['ACR12']); ?>
	
	<div id="abWrapper_<?php echo $controller->getForeignId(); ?>">
		<div id="abCalendar_<?php echo $controller->getForeignId(); ?>" class="abBackendView">
		<?php include dirname(__FILE__) . '/pjActionGetCal.php'; ?>
		</div>
	</div>
	<?php /*<div id="boxCalendarData" class="clear_left"></div>*/ ?>
	
	<div id="gridReservations" class="float_right w350"></div>
	<div class="float_right w350 t10">
		<a class="pj-button newReserv" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionCreate&amp;calendar_id=<?php echo $controller->getForeignId(); ?>&amp;date_from=<?php echo date("Y-m-d"); ?>"><?php __('lblCalendarNewReserv'); ?></a>
	</div>
	<div class="clear_both"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jqDateFormat = "<?php echo pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
	pjGrid.currencySign = "<?php echo pjUtil::formatCurrencySign(99, $tpl['option_arr']['o_currency']); ?>";
	pjGrid.queryString = "";
	var view_calendar_id = <?php echo $controller->getForeignId(); ?>;
	<?php
	if (!isset($_GET['time']))
	{
		?>pjGrid.queryString += "&time=<?php list($y, $n, $j) = explode("-", date("Y-n-j")); echo mktime(0, 0, 0, $n, $j, $y); ?>";<?php
	}
	?>
	var myLabel = myLabel || {};
	myLabel.today = "<?php echo pjUtil::formatDate(date("Y-m-d"), 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>";
	myLabel.exportSelected = "<?php __('lblExportSelected'); ?>";
	myLabel.deleteSelected = "<?php __('lblDeleteSelected'); ?>";
	myLabel.deleteConfirmation = "<?php __('lblDeleteConfirmation'); ?>";
	myLabel.name = "<?php __('lblName'); ?>";
	myLabel.email = "<?php __('email'); ?>";
	myLabel.id = "<?php __('lblID'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.from = "<?php __('lblReservationFrom'); ?>";
	myLabel.to = "<?php __('lblReservationTo'); ?>";
	myLabel.amount = "<?php __('lblReservationAmount'); ?>";
	</script>
	<?php
}
?>