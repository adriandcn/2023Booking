<div class="bold b10"><?php echo pjSanitize::html( @$tpl['calendars'][$controller->getForeignId()]['name'] ); ?></div>
<?php

$active = ' ui-tabs-active ui-state-active';
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminCalendars' && $_GET['action'] == 'pjActionView' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionView"><?php __('menuCalendar'); ?></a></li>

		<?php
		if($_SESSION["comprobar"] == 1){ ?>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && isset($_GET['tab']) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;tab=1"><?php __('menuSettings');  ?> </a></li>
		<?php }else{ ?>
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminOptions' && isset($_GET['tab']) ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;tab=1"><?php __('menuSettings'); ?></a></li>
		<?php
		}
		if ($tpl['option_arr']['o_price_plugin'] == 'price' && pjObject::getPlugin('pjPrice') !== NULL)
		{
			?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjPrice' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPrice&amp;action=pjActionIndex"><?php __('plugin_price_menu'); ?></a></li><?php
		} elseif ($tpl['option_arr']['o_price_plugin'] == 'period' && pjObject::getPlugin('pjPeriod') !== NULL) {
			?><li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjPeriod' ? $active : NULL; ?>"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjPeriod&amp;action=pjActionIndex"><?php __('plugin_price_menu'); ?></a></li><?php
		}
		?>
	</ul>
</div>