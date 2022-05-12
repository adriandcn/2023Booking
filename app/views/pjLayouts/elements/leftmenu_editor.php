<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionDashboard" class="<?php echo $_GET['controller'] == 'pjAdminReservations' && $_GET['action'] == 'pjActionDashboard' ? 'menu-focus' : NULL; ?>"><span class="menu-availability">&nbsp;</span><?php __('menuAvailability'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReservations&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminReservations' && $_GET['action'] != 'pjActionDashboard') || ($_GET['controller'] == 'pjInvoice' && in_array($_GET['action'], array('pjActionInvoices', 'pjActionUpdate', 'pjActionCreateInvoice'))) ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>
<?php
if (isset($tpl['calendars']) && !empty($tpl['calendars']))
{
	?>
	<div class="leftmenu-top"></div>
	<div class="leftmenu-middle">
		<div class="menu">
			<select class="select w200 setForeignId" name="calendar_id" data-controller="pjAdmin" data-action="pjActionIndex">
			<?php
				foreach ($tpl['calendars'] as $calendar)
				{
					?><option value="<?php echo $calendar['id']; ?>"<?php echo $calendar['id'] != $controller->getForeignId() ? NULL : ' selected="selected"'; ?>><?php echo stripslashes($calendar['name']); ?></option><?php
				}
			?>
			</select>
		</div>
	</div>
	<div class="leftmenu-bottom"></div>
	<?php
}
?>