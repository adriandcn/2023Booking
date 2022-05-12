<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>

		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionIndex" class="<?php
			echo in_array($_GET['controller'], array('pjAdminCalendars', 'pjPrice', 'pjPeriod'))
				|| ($_GET['controller'] == 'pjAdminOptions' && (isset($_GET['tab']) && in_array($_GET['tab'], array(1,2,3,4,5,6,7,10))))
			? 'menu-focus' : NULL; ?>"><span class="menu-browse">&nbsp;</span><?php __('menuCalendars'); ?></a>
		</li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionIndex" class="<?php
			echo in_array($_GET['controller'], array('pjAdminGroup', 'pjPrice', 'pjPeriod'))
				|| ($_GET['controller'] == 'pjAdminOptions' && (isset($_GET['tab']) && in_array($_GET['tab'], array(1,2,3,4,5,6,7,10))))
			? 'menu-focus' : NULL; ?>"><span class="menu-add">&nbsp;</span><?php echo "Agrupamientos" ?></a>
		</li>
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
		<ul class="menu">
			<?php
			if ($tpl['option_arr']['o_price_plugin'] == 'price' && pjObject::getPlugin('pjPrice') !== NULL)
			{
				?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjPrice' ? 'menu-focus' : NULL; ?>"><span class="menu-prices">&nbsp;</span><?php __('plugin_price_menu'); ?></a></li><?php
			}
			if ($tpl['option_arr']['o_price_plugin'] == 'period' && pjObject::getPlugin('pjPeriod') !== NULL)
			{
				?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPeriod&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjPeriod' ? 'menu-focus' : NULL; ?>"><span class="menu-periods">&nbsp;</span><?php __('plugin_period_menu'); ?></a></li><?php
			}
			?>
		</ul>
	</div>
	<div class="leftmenu-bottom"></div>
	<?php
}
?>