<?php
$active = ' ui-tabs-active ui-state-active';
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminGroup' && $_GET['action'] == 'pjActionView' ? $active : NULL; ?>"><a href="#"><?php echo "Editar Agrupamiento"; ?></a></li>
	</ul>
</div>