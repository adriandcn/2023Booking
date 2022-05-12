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
	$locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
	if (is_null($locale))
	{
		foreach ($tpl['lp_arr'] as $v)
		{
			if ($v['is_default'] == 1)
			{
				$locale = $v['id'];
				break;
			}
		}
	}
	if (is_null($locale))
	{
		$locale = @$tpl['lp_arr'][0]['id'];
	}
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);

	$jquery_validation = __('jquery_validation', true);
	?>

	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionIndex"><?php echo "Agrupamientos" ?></a></li>
			<li class="ui-state-default ui-corner-top">
			<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionCreate"><?php echo "Nuevo Agrupamiento"; ?></a></li>
		</ul>
	</div>

	<?php //pjUtil::printNotice(@$titles['ACR11'], @$bodies['ACR11']); ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionCreate" method="post" id="frmCreateCalendar" class="form pj-form" autocomplete="off">
		<input type="hidden" name="agrupamiento_create" value="1" />
		<input type="hidden" name="estado" value="1" />

		<div class="clear_both">
			<input type="hidden" name="id_usuario_servicio" value=" <?php echo $_SESSION['usuario_servicio']; ?> " >
			<br><br>
			<p>
				<label class="title"><?php echo "Nombre" ?>:</label>
				<span class="inline_block">
					<input type="text" name="nombre" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Nombre es Requerido" placeholder="Nombre Agrupamiento"/>
				</span>
			</p>
			<p>
				<label class="title"><?php echo "Nombre Ingles" ?>:</label>
				<span class="inline_block">
					<input type="text" name="nombre_eng" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Nombre Ingles es Requerido" placeholder="Nombre Ingles Agrupamiento"/>
				</span>
			</p>			
			<p>
				<label class="title"> Descripcion</label>
				<span class="inline_block">
					<textarea name="descripcion" id="descripcion" style="resize: none;" rows="4" cols="43" class="pj-form-field required"
					data-msg-required="Descripcion es Requerido" placeholder="Descripcion del Agrupamiento"></textarea>
				</span>
			</p>
			<p>
				<label class="title"> Descripcion Ingles</label>
				<span class="inline_block">
					<textarea name="descripcion_eng" id="descripcion_eng" style="resize: none;" rows="4" cols="43"  class="pj-form-field required"
					data-msg-required="Descripcion Ingles es Requerido" placeholder="Descripcion Ingles del Agrupamiento"></textarea>
				</span>
			</p>
			<p>
				<label class="title"> Tags</label>
				<span class="inline_block">
					<input type="text" name="tags" id="tags" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Tags es Requerido" title="Palabras clave o referencias separadas por comas" placeholder="#ruta del sol, #museos">
				</span>
			</p>
			<p>
				<label class="title"> Tags Ingles</label>
				<span class="inline_block">
					<input type="text" name="tags_eng" id="tags_eng" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Tags Ingles es Requerido" title="Palabras clave o referencias separadas por comas" placeholder="#ruta del sol, #museos">
				</span>
			</p>
			<p>
				<label class="title"> Keywords</label>
				<span class="inline_block">
					<input type="text" name="key_words" id="key_words" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Keywords es Requerido" title="Keywords separados por comas" placeholder="Keywords">
				</span>
			</p>
			<p>
				<label class="title"> Keywords Ingles</label>
				<span class="inline_block">
					<input type="text" name="key_words_eng" id="key_words_eng" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Keywords Ingles es Requerido" title="Keywords separados por comas" placeholder="Keywords">
				</span>
			</p>
			<p>
				<label class="title"> Orden</label>
				<span class="inline_block">
					<input type="number" min="0" name="orden" id="orden" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Orden es Requerido" title="Keywords separados por comas" placeholder="Orden">
				</span>
			</p>
			<p>
				<label class="title"> Dias Bloqueados</label>
				<span class="inline_block">
					<input type="number" min="0" name="block_days" id="block_days" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Dias Bloqueados es Requerido" title="Dias Bloqueados separados por comas" placeholder="Dias Bloqueados">
				</span>
			</p>
			<p>
				<label class="title">Latitud</label>
				<span class="inline_block">
					<input type="text" name="lat" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Latitud es Requerido" placeholder="Latitud Agrupamiento"/>
				</span>
			</p>
			<p>
				<label class="title">Longitud</label>
				<span class="inline_block">
					<input type="text" name="lng" class="pj-form-field w300<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" data-msg-required="Longitud es Requerido" placeholder="Longitud Agrupamiento"/>
				</span>
			</p>			
			<p>
				<label class="title"> Instrucciones Punto de Encuentro</label>
				<span class="inline_block">
					<textarea name="instrucciones" id="instrucciones" style="resize: none;" rows="4" cols="43" class="pj-form-field required"
					data-msg-required="Instrucciones es Requerido" placeholder="Instrucciones del Agrupamiento"></textarea>
				</span>
			</p>
			<p>
				<label class="title"> Instrucciones Punto de Encuentro Ingles</label>
				<span class="inline_block">
					<textarea name="instrucciones_eng" id="instrucciones_eng" style="resize: none;" rows="4" cols="43"  class="pj-form-field required"
					data-msg-required="Instrucciones Ingles es Requerido" placeholder="Instrucciones Ingles del Agrupamiento"></textarea>
				</span>
			</p>															
			<p>
			<br><br>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
			</p>
		</div>
	</form>


	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.localeId = "<?php echo $controller->getLocaleId(); ?>";
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				tooltip: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet faucibus enim.",
				select: function (event, ui) {
					// Callback, e.g. ajax requests or whatever
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
	</script>
	<?php
}
?>
