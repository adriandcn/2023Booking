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
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
		
	<?php pjUtil::printNotice(__('lblInstallInfoTitle', true), __('lblInstallInfoDesc', true), false, false); ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="pj-form form" target="_blank">
		<input type="hidden" name="controller" value="pjAdminOptions" />
		<input type="hidden" name="action" value="pjActionPreview" />
		<fieldset class="fieldset white">
			<legend><?php __('lblInstallConfig'); ?></legend>
			<p>
				<label class="title"><?php __('lblInstallConfigCalendar'); ?></label>
				<select class="pj-form-field w200" id="install_calendar" name="cid">
					<option value="all"><?php __('lblInstallConfigAllCalendars'); ?></option>
					<?php
					foreach ($tpl['calendars'] as $calendar)
					{
						?><option value="<?php echo $calendar['id']; ?>"<?php echo !isset($_GET['calendar_id']) || $_GET['calendar_id'] != $calendar['id'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($calendar['name']); ?></option><?php
					}
					?>
				</select>
			</p>
			<p>
				<label class="title"><?php __('lblInstallConfigLocale'); ?></label>
				<select class="pj-form-field w200" id="install_locale" name="locale">
					<option value=""><?php __('lblInstallConfigLang'); ?></option>
					<?php
					foreach ($tpl['locale_arr'] as $locale)
					{
						?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
					}
					?>
				</select>
			</p>
			<p style="display: none">
				<label class="title"><?php __('lblInstallConfigMonths'); ?></label>
				<select class="pj-form-field" id="install_months" name="view" disabled="disabled">
					<option value="1">1</option>
					<option value="3">3</option>
					<option value="6">6</option>
					<option value="12">12</option>
				</select>
			</p>
			<p>
				<label class="title"></label>
				<input type="submit" value="<?php __('menuPreview', false, true); ?>" class="pj-button" />
			</p>
		</fieldset>
	</form>
	
	<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstallPreview'); ?></p>
	<textarea class="pj-form-field w700 textarea_install" style="overflow: auto; height:80px"></textarea>

<div style="display: none" id="boxStandard">&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss&cid={CID}" type="text/css" rel="stylesheet" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad&cid={CID}&view={VIEW}{LOCALE}"&gt;&lt;/script&gt;
</div>

<div style="display: none" id="boxAvailability">&lt;link type="text/css" rel="stylesheet" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailabilityCss" /&gt;
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailability{LOCALE}"&gt;&lt;/script&gt;</div>
	<?php
}
?>