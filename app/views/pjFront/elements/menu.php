<?php
$hasReferer = isset($_GET['index']) && (int) $_GET['index'] > 0;
if ((
		(in_array($_GET['action'], array('pjActionGetCalendar')) && isset($_GET['view']) && (int) $_GET['view'] === 1)
		|| in_array($_GET['action'], array('pjActionGetAvailability', 'pjActionGetSummaryForm', 'pjActionGetBookingForm'))
	)
	&& isset($_GET['locale'])
	&& (int) $_GET['locale'] > 0
	&& !$hasReferer
	)
{
	//skip
} else {
	if($_GET['action'] == 'pjActionGetCalendar')
	{
		?>
		<!--<div class="abCalendarNote"><?php __('lblCalendarMessage');?></div> -->
		<?php
		?>
		<div class="abCalendarNote"> </div>
		<?php
	}
	$front_err = str_replace(array('"', "'"), array('\"', "\'"), __('front_err', true, true));
	?>
	<div class="abMenu">
		<div class="abErrorMessage" style="display: none" data-msg="<?php echo htmlentities(pjAppController::jsonEncode($front_err)); ?>"></div>
		<?php
		if ($hasReferer)
		{
			if($_GET['action'] == 'pjActionGetCalendar')
			{
				?><a href="#" class="abReturnToAvailability">&laquo;&nbsp;<?php __('lblBackToCalendars'); ?></a><?php
			}else if(in_array($_GET['action'], array('pjActionGetSummaryForm', 'pjActionGetBookingForm'))){
				?><a href="#" class="abReturnToCalendar">&laquo;&nbsp;<?php __('lblBackToCalendar'); ?></a><?php
			}
		}else{
			if(in_array($_GET['action'], array('pjActionGetSummaryForm', 'pjActionGetBookingForm'))){
				?><a href="#" class="abReturnToCalendar">&laquo;&nbsp;<?php __('lblBackToCalendar'); ?></a><?php
			}
		}
		if ($_GET['action'] == 'pjActionGetCalendar' && isset($_GET['view']) && (int) $_GET['view'] > 1)
		{
			?>
			<ul class="abMenuNav abMenuList">
				<li class="abMenuNavPrev"><a href="#" class="abCalendarLinkMonth" data-cid="<?php echo $_GET['cid']; ?>" data-year="<?php echo $prev_year; ?>" data-month="<?php echo $prev_month; ?>"><i class="fa fa-chevron-left"></i></a></li>
				<li class="abMenuNavNext"><a href="#" class="abCalendarLinkMonth" data-cid="<?php echo $_GET['cid']; ?>" data-year="<?php echo $next_year; ?>" data-month="<?php echo $next_month; ?>"><i class="fa fa-chevron-right"></i></a></li>
			</ul>
			<?php
		}
		if (!isset($_GET['locale']) || (int) $_GET['locale'] <= 0)
		{
			if (isset($tpl['locale_arr']) && is_array($tpl['locale_arr']) && !empty($tpl['locale_arr']))
			{
				?>
				<ul class="abMenuLocale abMenuList"><?php
				foreach ($tpl['locale_arr'] as $locale)
				{
					?><li><a href="#" class="abSelectorLocale<?php echo $controller->pjActionGetLocale() == $locale['id'] ? ' abLocaleFocus' : NULL; ?>" data-id="<?php echo $locale['id']; ?>" title="<?php echo pjSanitize::html($locale['title']); ?>"><img src="<?php echo PJ_INSTALL_URL . 'core/framework/libs/pj/img/flags/' . $locale['file'] ?>" alt="<?php echo pjSanitize::html($locale['title']); ?>" /></a></li><?php
				}
				?>
				</ul>
				<?php
			}
		}
		?>
	</div>
	<?php
}
?>