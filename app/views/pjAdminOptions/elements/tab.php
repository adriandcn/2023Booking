<?php
if (isset($tpl['arr']))
{
	if (is_array($tpl['arr']))
	{
		$count = count($tpl['arr']);
		if ($count > 0)
		{
			?>
			<?php if (@$_GET['tab'] == 1 && (int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
			<div class="multilang"></div>
			<div class="clear_right pt10"></div>
			<?php endif; ?>

			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
				<input type="hidden" name="options_update" value="1" />
				<input type="hidden" name="tab" value="<?php echo @$_GET['tab']; ?>" />
				<input type="hidden" name="next_action" value="pjActionIndex" />
				<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
					<thead>
						<tr>
							<th><?php __('lblOption'); ?></th>
							<th><?php __('lblValue'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php if (@$_GET['tab'] == 1) : ?>
						<tr>
							<?php  if($_SESSION["comprobar"] !=1){ ?>
							<td><?php __('lblUser'); ?></td>
							<td>
								<select name="user_id" class="pj-form-field">
									<option value="">-- <?php __('lblChoose'); ?>--</option>
									<?php
									if (isset($tpl['user_arr']) && is_array($tpl['user_arr']))
									{
										foreach ($tpl['user_arr'] as $v)
										{
											?><option value="<?php echo $v['id']; ?>"<?php echo @$tpl['calendar_arr']['user_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v['name']); ?></option><?php
										}
									}
									?>
								</select>
							</td>
							<?php } ?>
						</tr>
						<tr>
							<td><?php __('lblName'); ?></td>
							<td>
							<?php
							if (isset($tpl['lp_arr']) && is_array($tpl['lp_arr']))
							{
								foreach ($tpl['lp_arr'] as $v)
								{
									?>
									<div class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
										<span class="inline_block">
											<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w300" value="<?php echo pjSanitize::html(@$tpl['calendar_arr']['i18n'][$v['id']]['name']); ?>" />
											<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
											<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
											<?php endif;?>
										</span>
									</div>
									<?php
								}
							}
							?>
							</td>
						</tr>
						<tr>
							<td>Agrupamiento</td>
							<td>
								<select name="id_agrupamiento" id="id_agrupamiento" class="pj-form-field required" data-msg-required="Agrupamiento es Requerido" style="width: 100% !important;">
									<option value="">-- <?php echo "Seleccione Agrupamiento" ?>--</option>
									<?php
									if (isset($tpl['agrupamiento_arr']) && is_array($tpl['agrupamiento_arr']))
									{

										foreach ($tpl['agrupamiento_arr'] as $v)
										{
											echo $v['id'];
											?><option value="<?php echo $v['id']; ?>"<?php echo @$tpl['calendar_arr']['id_agrupamiento'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo pjSanitize::html($v['nombre']); ?></option><?php
										}
									}
									?>
								</select>
							</td>
						</tr>

						<tr>
							<td>Correo Operador</td>
							<td>
							<input type="text" name="correo_operador" id="correo_operador" class="pj-form-field w300" value="<?php echo $tpl['calendar_arr'] ["correo_operador"]; ?>" data-msg-required="Correo del Operador es Requerido" />								
							</td>
						</tr>
												
						<tr>
							<td>Descripcion</td>
							<td>
							<textarea name="descripcion" id="descripcion" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Descripcion es Requerido"><?php echo $tpl['calendar_arr'] ["descripcion"];?></textarea>
							</td>
						</tr>

						<tr>
							<td>Descripcion Ingles</td>
							<td>
							<textarea name="descripcion_eng" id="descripcion_eng" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Descripcion Ingles es Requerido"><?php echo $tpl['calendar_arr'] ["descripcion_eng"];?></textarea>
							</td>
						</tr>

						<tr>
						<td>
							<label class="title" style="display: inline-table;"> Agregar Foto</label>
							<div class="image_upload_div" style="width: 50%; display: inline-table" >
								<form action="upload.php" class="dropzone">
							    	</form>
							</div>
						</td>
			<td>
			<div class="image_upload_div" style="display: inline-table;width: 100%;">

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionFotos" class="dropzone" enctype= multipart/form-data >
			<input type="hidden" name="id_usuario_servicio" value="<?php echo $_SESSION['usuario_servicio']; ?> " >
			<input type="hidden" name="calendar_id" value="<?php echo $tpl['calendar_arr'] ['id'];?>" >
			</form>
			</div>

			<td>
						</tr>
					<?php endif; ?>

			<?php

			for ($i = 0; $i < $count; $i++)
			{
				if ((int) $tpl['arr'][$i]['is_visible'] === 0) continue;

				$rowClass = NULL;
				$rowStyle = NULL;
				if (in_array($tpl['arr'][$i]['key'], array('o_smtp_host', 'o_smtp_port', 'o_smtp_user', 'o_smtp_pass')))
				{
					$rowClass = " boxSmtp";
					$rowStyle = "display: none";
					switch ($tpl['option_arr']['o_send_email'])
					{
						case 'smtp':
							$rowStyle = NULL;
							break;
					}
				} elseif (in_array($tpl['arr'][$i]['key'], array('o_authorize_mid', 'o_authorize_tz', 'o_authorize_key', 'o_authorize_hash'))) {
					$rowClass = " boxAuthorize";
					$rowStyle = "display: none";
					switch ($tpl['option_arr']['o_allow_authorize'])
					{
						case '1':
							$rowStyle = NULL;
							break;
					}
				} elseif (in_array($tpl['arr'][$i]['key'], array('o_paypal_address'))) {
					$rowClass = " boxPaypal";
					$rowStyle = "display: none";
					switch ($tpl['option_arr']['o_allow_paypal'])
					{
						case '1':
							$rowStyle = NULL;
							break;
					}
				} elseif (in_array($tpl['arr'][$i]['key'], array('o_bank_account'))) {
					$rowClass = " boxBank";
					$rowStyle = "display: none";
					switch ($tpl['option_arr']['o_allow_bank'])
					{
						case '1':
							$rowStyle = NULL;
							break;
					}
				}
				?>
				<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
					<td>
					<?php
						if($tpl['arr'][$i]['key'] == "o_bf_cedula"){
							echo "Identificacion";
						}elseif ($tpl['arr'][$i]['key'] == "o_bf_lastname"){
							echo "Lastname";
						}else{
							__('opt_' . $tpl['arr'][$i]['key']);
						}
					?>

					</td>
					<td>
						<?php
						switch ($tpl['arr'][$i]['type'])
						{
							case 'string':
								?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w200" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
								break;
							case 'text':
								?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
								break;
							case 'int':
								?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60 digits" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" readonly="readonly" /><?php
								break;
							case 'float':
								switch ($tpl['arr'][$i]['key'])
								{
									case 'o_deposit':
										?><input type="text" name="value-int-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo (int) $tpl['arr'][$i]['value']; ?>" /><?php
										?>
										<select name="value-enum-o_deposit_type" class="pj-form-field">
										<?php
										$default = explode("::", $tpl['o_arr']['o_deposit_type']['value']);
										$enum = explode("|", $default[0]);

										$enumLabels = array();
										if (!empty($tpl['o_arr']['o_deposit_type']['label']) && strpos($tpl['o_arr']['o_deposit_type']['label'], "|") !== false)
										{
											$enumLabels = explode("|", $tpl['o_arr']['o_deposit_type']['label']);
										}

										foreach ($enum as $k => $el)
										{
											?><option value="<?php echo $default[0].'::'.$el; ?>"<?php echo $default[1] == $el ? ' selected="selected"' : NULL; ?>><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										}
										?>
										</select>
										<?php
										break;
									case 'o_security':
										?>
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
											<input type="text" name="value-int-<?php echo $tpl['arr'][$i]['key']; ?>" value="<?php echo (int) $tpl['arr'][$i]['value']; ?>" class="pj-form-field number w60" />
										</span>
										<?php
										break;
									case 'o_tax':
										?>
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text">%</abbr></span>
											<input type="text" name="value-int-<?php echo $tpl['arr'][$i]['key']; ?>" value="<?php echo (int) $tpl['arr'][$i]['value']; ?>" class="pj-form-field field-float w60" />
										</span>
										<?php
										break;
									default:
										?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
								}
								break;
							case 'enum':
								?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
								<?php
								$default = explode("::", $tpl['arr'][$i]['value']);
								$enum = explode("|", $default[0]);

								$enumLabels = array();
								if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
								{
									$enumLabels = explode("|", $tpl['arr'][$i]['label']);
								}

								foreach ($enum as $k => $el)
								{
									?><option value="<?php echo $default[0].'::'.$el; ?>"<?php echo $default[1] == $el ? ' selected="selected"' : NULL; ?>><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
								}
								?>
								</select>
								<?php
								if (in_array($tpl['arr'][$i]['key'], array('o_bf_adults', 'o_bf_children')))
								{
									__('lblMaxValue'); ?>: <input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>_max" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['o_arr'][$tpl['arr'][$i]['key'].'_max']['value'])); ?>" /><?php
								}
								break;
							case 'bool':
								?><input type="checkbox" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>"<?php echo $tpl['arr'][$i]['value'] == '1|0::1' ? ' checked="checked"' : NULL; ?> value="1|0::1" /><?php
								break;
							case 'color':
								?>
								<span class="pj-form-field-custom pj-form-field-custom-after">
									<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-color w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
									<span class="pj-form-field-after"></span>
								</span>
								<?php
								break;
						}
						?>
					</td>

				</tr>
				<?php
			}
			?>
					</tbody>
				</table>





				<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
			</form>



			<?php if (@$_GET['tab'] == 1 && (int) $tpl['option_arr']['o_multi_lang'] === 1) : ?>
<br><br>
<?php if (@$_GET['tab'] == 1) : ?>



		<?php if(isset($tpl['fotos_calendario']) &&  $tpl['contador_fotos_calendario']>0){ ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCalendars&amp;action=pjActionDeleteFotos" >
			<div class="container">
    				<div class="row">
        					<div class="span12">
        					<div id="owl-demo" class="owl-carousel">
        					<?php  for($i=0; $i<$tpl['contador_fotos_calendario']; $i++){
					$url = "app/web/icon/". $tpl['fotos_calendario'][$i]['filename'];
                				?>
                				  <div class="item">
                    <img src="app/web/img/x.png" onclick='alertaConfirm( <?php echo $tpl["fotos_calendario"][$i]["id"]; ?>)'
                    	    style=" width:25px; height:29px; position:absolute; top:2px; right:0px; cursor:pointer;" alt='' />
                    <img src="<?php echo $url ?>" href='#' >

                    <input type="text" value="<?php echo $tpl["descripcion_fotografia"][$i]["id"]; ?>" name="descripcion_fotografia_$tpl['fotos_calendario'][$i]['id']"
                    		style='height: 15px;width: 100%;' placeholder='Descripcion'
                    		onchange="AjaxSaveDetailsFotografia('deleteImage',<?php echo $tpl["fotos_calendario"][$i]["id"]; ?>)">

                </div>


					<?php }?>
					</div>
			        		</div>
    				</div>
			</div>
			</form>
	<style>
    	#owl-demo .item{
        		margin: 3px;
        		padding: 5%;
    	}
    	#owl-demo .item img{
	        display: block;
	        width: 100%;
	        height: auto;
	}
	</style>
	<script>
	 $(document).ready(function() {
		    $("#owl-demo").owlCarousel({
		    autoPlay: 5000,
		            items : 4,
		            itemsDesktop : [1199, 3],
		            itemsDesktopSmall : [979, 3]
		    });
	 });
	</script>
	<script>
	            function alertaConfirm(id){
	            var r = confirm("Está seguro de que desea eliminar esta imagen?");
	                    if (r == true) {
	            var resp = AjaxContainerRetrunMessage("deleteImage", id);
	            console.log(resp);
	            location.reload();
	            		//$("#flag_image").val('1');
	                    } else {
	            txt = "Cancelado";
	                    }
	            }
	</script>



                 	<?php }?>

		<?php endif; ?>

<br><br>


             <script>
         	$(document).ready(function () {
             	   GetDataAjaxImagenes("<?php echo $tpl['calendar_arr'] ['id'];?>");
    	});
    	</script>

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
				});
			})(jQuery_1_8_2);
			</script>
			<?php endif; ?>
			<?php
		}
	}
}
?>
