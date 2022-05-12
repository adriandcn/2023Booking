<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	if (isset($_GET['month']) && isset($_GET['year']))
	{
		$time = mktime(0, 0, 0, (int) $_GET['month'], 1, (int) $_GET['year']);
	} else {
		$time = time();
	}

	list($year, $month, $numOfDaysInCurrentMonth) = explode("-", date("Y-n-t", $time));

	$next_month = $month + 1 <= 12 ? $month + 1 : $month + 1 - 12;
	$next_year = $month + 1 <= 12 ? $year : $year + 1;
	$prev_month = $month - 1 >= 1 ? $month - 1 : $month - 1 + 12;
	$prev_year = $month - 1 >= 1 ? $year : $year - 1;
}

include dirname(__FILE__) . '/elements/menu.php';
if (isset($tpl['arr']) && !empty($tpl['arr']))
{	
	?>
	<div class="abCal-container abCal-row">
		<div class="abCal-calendars">
			<div class="abCal-title" style="height: 64px"><div class="abCal-note"><?php __('front_availability_note'); ?></div></div>
			<?php
			foreach ($tpl['arr'] as $k => $calendar)
			{
				?><div class="abCal-title"><a href="#" class="abCal-link" data-id="<?php echo $calendar['id']; ?>"><?php echo pjSanitize::html($calendar['title']); ?></a></div><?php
			}
			?>
		</div>
		<div class="abCal-dates">
			<div class="abCal-scroll">
			<?php
			$haystack = array(
				'calendarStatus1' => 'abCalendarDate',
				'calendarStatus2' => 'abCalendarReserved',
				'calendarStatus3' => 'abCalendarPending',
				'calendarStatus_1_2' => 'abCalendarReservedNightsStart',
				'calendarStatus_1_3' => 'abCalendarPendingNightsStart',
				'calendarStatus_2_1' => 'abCalendarReservedNightsEnd',
				'calendarStatus_2_3' => 'abCalendarNightsReservedPending',
				'calendarStatus_3_1' => 'abCalendarPendingNightsEnd',
				'calendarStatus_3_2' => 'abCalendarNightsPendingReserved'
			);
			
			$months = __('months', true);
			$rand = rand(1,9999);
			foreach ($tpl['arr'] as $k => $calendar)
			{
				if ($k == 0)
				{
					?>
					<div class="abCal-head">
						<div class="abCal-head-row">
							<span style="width: 100%; min-width: <?php echo 21 * $numOfDaysInCurrentMonth - 3; ?>px">
								<a href="#" class="abCal-prev-month" data-year="<?php echo $prev_year; ?>" data-month="<?php echo $prev_month; ?>">&laquo;&nbsp;<?php __('lblReservationPrevMonth'); ?></a>
								<?php echo $months[$month]; ?> <?php echo $year; ?>
								<a href="#" class="abCal-next-month" data-year="<?php echo $next_year; ?>" data-month="<?php echo $next_month; ?>"><?php __('lblReservationNextMonth'); ?>&nbsp;&raquo;</a>
							</span>
						</div>
						<div class="abCal-head-row">
						<?php
						# Current month
						foreach (range(1, $numOfDaysInCurrentMonth) as $i)
						{
							//$timestamp = mktime(0, 0, 0, $month, $i, $year);
	    	    			//$suffix = date("S", $timestamp);
							?><span><?php echo $i/* . $suffix*/; ?></span><?php
						}
						?>
						</div>
					</div>
					<?php
				}
				?>
				<div class="abCal-program abCal-id-<?php echo $calendar['id']; ?>">
				<?php
				$date_arr = $calendar['date_arr'];
				if ((int) $calendar['o_bookings_per_day'] === 1)
				{
					$date_arr = pjUtil::fixSingleDay($date_arr);
				}
				
				$imageMap = array(
					'abCalendarReservedNightsStart' => sprintf("%s%s%u_reserved_start.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarReservedNightsEnd' => sprintf("%s%s%u_reserved_end.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarNightsPendingPending' => sprintf("%s%s%u_pending_pending.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarNightsReservedPending' => sprintf("%s%s%u_reserved_pending.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarNightsPendingReserved' => sprintf("%s%s%u_pending_reserved.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarNightsReservedReserved' => sprintf("%s%s%u_reserved_reserved.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarPendingNightsStart' => sprintf("%s%s%u_pending_start.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id']),
					'abCalendarPendingNightsEnd' => sprintf("%s%s%u_pending_end.jpg", PJ_INSTALL_URL, PJ_UPLOAD_PATH, $calendar['id'])
				);
				
				# Current month
				foreach (range(1, $numOfDaysInCurrentMonth) as $d)
				{
					$timestamp = mktime(0, 0, 0, $month, $d, $year);
	    	    	$tomorrow = $timestamp + 86400;
	    	    	$yesterday = $timestamp - 86400;
	    	    	$class = pjUtil::getClass($date_arr, $timestamp, $tomorrow, $yesterday, $calendar['o_bookings_per_day'], $haystack);
	    	    	if (!is_null($class) && array_key_exists($class, $imageMap))
	    	    	{
	    	    		?><span class="abCal-imgOuter"><span class="abCal-imgWrap"><img class="abCal-img" src="<?php echo $imageMap[$class]; ?>?rand=<?php echo $rand; ?>" alt="" /></span></span><?php
	    	    	} else {
	    	    		?><span class="<?php echo $class; ?>">&nbsp;</span><?php
	    	    		
	    	    	}
				}
				?>
				</div>
				<?php
			}
			?>
			</div>
		</div>
	</div>
	<?php
}
?>