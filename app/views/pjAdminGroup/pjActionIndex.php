<?php
$active = ' ui-tabs-active ui-state-active';
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
		if($_GET['err'] == "ACR01"){
			$titles = "Creacion de Agrupamiento";
			$bodies = "Agrupamiento Creado Exitosamente";
			pjUtil::printNotice(@$titles, @$bodies);
		}elseif($_GET['err'] == "ACR02"){
			$titles = "Actualizacion de Agrupamiento";
			$bodies = "Agrupamiento Actualizado Exitosamente";
			pjUtil::printNotice(@$titles, @$bodies);
		}elseif ($_GET['err'] == "ACR04") {
			$titles = "Creacion de Agrupamiento";
			$bodies = "Ha ocurrido un error al crear el Agrupamiento";
			pjUtil::printNotice(@$titles, @$bodies);
		}

	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminGroup' && $_GET['action'] == 'pjActionIndex' ? $active : NULL; ?>">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionIndex"><?php echo "Agrupamientos" ?></a></li>
			<li class="ui-state-default ui-corner-top<?php echo $_GET['controller'] == 'pjAdminGroup' && $_GET['action'] == 'pjActionCreate' ? $active : NULL; ?>">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionCreate"><?php echo "Nuevo Agrupamiento"; ?></a></li>
		</ul>
	</div>

	<?php //pjUtil::printNotice(@$titles['ACR10'], @$bodies['ACR10']); ?>

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
	pjGrid.queryString = "";
	var myLabel = myLabel || {};
	myLabel.settings = "<?php __('menuSettings'); ?>";
	myLabel.edit = "<?php __('lblEdit'); ?>";
	myLabel.delete = "<?php __('lblDelete'); ?>";
	myLabel.viewCalendar = "<?php __('lblViewCalendar'); ?>";
	myLabel.id = "<?php __('lblID'); ?>";
	myLabel.calendar = "<?php echo 'Nombre Agrupamiento'; ?>";
	myLabel.descripcion = "<?php echo 'Descripcion Agrupamiento'; ?>";
	myLabel.deleteSelected = "<?php __('lblDeleteSelected'); ?>";
	myLabel.deleteConfirmation = "<?php __('lblDeleteConfirmation'); ?>";
	</script>
	<?php
}
?>