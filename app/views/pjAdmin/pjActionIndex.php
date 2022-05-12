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
	?>
	<div class="dashboard_header">
		<div class="dashboard_header_item">
			<div class="dashboard_icon dashboard_properties"></div>
			<div class="dashboard_info"><abbr><?php echo (int) @$tpl['info_arr'][0]['calendars']; ?></abbr><?php (int) @$tpl['info_arr'][0]['calendars'] !== 1 ? __('lblDashCalendars') : __('lblDashCalendar'); ?>
			</div>
		</div>
		<div class="dashboard_header_item">
			<div class="dashboard_icon dashboard_reservations"></div>
			<div class="dashboard_info"><abbr><?php echo (int) @$tpl['info_arr'][0]['reservations']; ?></abbr><?php (int) @$tpl['info_arr'][0]['reservations'] !== 1 ? __('lblDashReservations') : __('lblDashReservation'); ?></div>
		</div>
		<div class="dashboard_header_item dashboard_header_item_last">
			<div class="dashboard_icon dashboard_users"></div>
			<div class="dashboard_info"><abbr><?php echo (int) @$tpl['info_arr'][0]['users']; ?></abbr><?php (int) @$tpl['info_arr'][0]['users'] !== 1 ? __('lblDashUsers') : __('lblDashUser'); ?></div>
		</div>
		<!-- <div class="dashboard_header_item dashboard_header_item_last">
			<div class="dashboard_icon dashboard_users"></div>
			<div class="dashboard_info"><abbr><?php echo $_SESSION['usuario_servicio']; ?></abbr><?php echo "Id Usuario Servicio"; ?></div>
		</div> -->
	</div>

	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('dashboard_quick_links'); ?></div>
			<div class="dashboard_column_top"><?php __('lblDashLatestReservations'); ?></div>
			<div class="dashboard_column_top dashboard_column_top_last"><?php __('lblDashTopUsers'); ?></div>
		</div>
		<div class="dashboard_middle">
			<div class="dashboard_column _quick">
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionDashboard"><?php __('dashboard_view_availability'); ?></a></p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionIndex&amp;current_week=1"><?php __('dashboard_this_week_reservations'); ?></a></p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionIndex&amp;last_7days=1"><?php __('dashboard_last_7days_reservations'); ?></a></p>
                <p>&nbsp;</p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionCreate"><?php __('dashboard_new_reservation'); ?></a></p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionIndex&amp;status=Pending"><?php __('dashboard_pending_reservations'); ?></a></p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionIndex&amp;status=Cancelled"><?php __('dashboard_cancelled_reservations'); ?></a></p>
                <p>&nbsp;</p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&amp;action=pjActionCreate"><?php __('dashboard_new_calendar'); ?></a></p>
				<p><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminUsers&amp;action=pjActionCreate"><?php __('dashboard_new_user'); ?></a></p>
			</div>
			<div class="dashboard_column">
				<?php
				$cnt = count($tpl['reservation_arr']);
				if ($cnt === 0)
				{
					?><p class="m10"><?php __('lblReservationNotFound'); ?></p><?php
				}
				foreach ($tpl['reservation_arr'] as $k => $item)
				{
					$nights = (strtotime($item['date_to']) - strtotime($item['date_from'])) / 86400;
					?>
					<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
						<div class="dashboard_resr_left">
							<div class="bold fs13 lh19 verdana"><?php echo stripslashes($item['c_name']); ?></div>
							<div class="italic"><?php echo pjSanitize::html($item['calendar_name']); ?></div>
							<table>
								<tr>
									<td style="padding: 5px 10px 5px 0"><?php __('lblReservationFrom'); ?></td>
									<td style="padding: 5px 0"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($item['date_from'])); ?></td>
								</tr>
								<tr>
									<td style="padding: 5px 10px 5px 0"><?php __('lblReservationTo'); ?></td>
									<td style="padding: 5px 0"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($item['date_to'])); ?></td>
								</tr>
							</table>
							<div class=""><a class="bold fs13" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminReservations&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><?php echo $item['status']; ?></a></div>
						</div>
						<div class="dashboard_resr_right"><abbr><?php echo $nights; ?></abbr><?php (int) $nights !== 1 ? __('lblDashNights') : __('lblDashNight'); ?></div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="dashboard_column dashboard_column_last">
				<?php
				if (!$controller->isOwner())
				{
					$cnt = count($tpl['user_arr']);
					foreach ($tpl['user_arr'] as $k => $item)
					{
						?>
						<div class="dashboard_row<?php echo $k + 1 !== $cnt ? NULL : ' dashboard_row_last'; ?>">
							<div class="bold fs13 lh19 verdana"><?php echo stripslashes($item['name']); ?></div>
							<div class="t10"><?php
							if ($controller->isAdmin())
							{
								?><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminUsers&amp;action=pjActionUpdate&amp;id=<?php echo $item['id']; ?>"><?php echo pjSanitize::html($item['email']); ?></a><?php
							} else {
								echo pjSanitize::html($item['email']);
							}
							?></div>
							<div class="t5 gray"><?php __('lblDashLastLogin'); ?>: <?php echo date($tpl['option_arr']['o_date_format'], strtotime($item['last_login'])); ?></div>
							<div class="t5"><?php
							if ($controller->isAdmin())
							{
								?><a href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCalendars&amp;action=pjActionIndex&amp;user_id=<?php echo $item['id']; ?>"><?php echo (int) $item['calendars']; ?></a><?php
							} else {
								echo (int) $item['calendars'];
							}
							?> <?php (int) $item['calendars'] !== 1 ? __('lblDashCalendars') : __('lblDashCalendar'); ?></div>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	<?php
	$months = __('months', true);
	$days = __('days', true);
	?>
	<div class="clear_left t20 overflow">
		<div class="float_left black pt15">
			<span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span>
			<?php
			list($month_index, $other) = explode("_", date("n_d, Y H:i", strtotime($_SESSION[$controller->defaultUser]['last_login'])));
			printf("%s %s", $months[$month_index], $other);
			?>
		</div>
		<div class="float_right overflow">
		<?php
		list($hour, $day, $month_index, $other) = explode("_", date("H:i_w_n_d, Y"));
		?>
			<div class="dashboard_date">
				<abbr><?php echo @$days[$day]; ?></abbr>
				<?php printf("%s %s", $months[$month_index], $other); ?>
			</div>
			<div class="dashboard_hour"><?php echo $hour; ?></div>
		</div>
	</div>
	<?php
}
?>