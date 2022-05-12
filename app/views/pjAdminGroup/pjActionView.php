<?php
if (isset($tpl['status'])){
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
			$titles = "Actualizacion de Agrupamiento";
			$bodies = "Agrupamiento Actualizado Exitosamente";
			pjUtil::printNotice(@$titles, @$bodies);
		}elseif ($_GET['err'] == "ACR04") {
			$titles = "Actualizacion de Agrupamiento";
			$bodies = "Ha ocurrido un error al actualizar el Agrupamiento";
			pjUtil::printNotice(@$titles, @$bodies);
		}
	}
}
?>

<?php include PJ_VIEWS_PATH . 'pjLayouts/elements/calmenuGroup.php'; ?>

<?php //pjUtil::printNotice(@$titles['ACR12'], @$bodies['ACR12']); ?>

<div style="width: 100% !important:">
<?php
if (isset($tpl['arr'])){
	if (is_array($tpl['arr']))	{
		$count = count($tpl['arr']);
		if ($count > 0)	{
			?>

			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionUpdate" method="post" class="form pj-form">
				<input type="hidden" name="options_update" value="1" />
				<input type="hidden" name="id_usuario_servicio" value="<?php echo $tpl['arr'][0] ["id_usuario_servicio"];?>" />
				<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%;" >
					<thead>
						<tr>
							<th><?php __('lblOption'); ?></th>
							<th><?php __('lblValue'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td>Nombre</td>
							<td>
							<input type="text" name="nombre" class="pj-form-field w300"  value="<?php echo $tpl['arr'][0] ["nombre"];?>" placeholder="Nombre Agrupamiento" data-msg-required="Nombre es Requerido"/>
							</td>
						</tr>
						<tr>
						<td>Nombre Ingles</td>
							<td>
							<input type="text" name="nombre_eng" class="pj-form-field w300"  value="<?php echo $tpl['arr'][0] ["nombre_eng"];?>" placeholder="Nombre Ingles Agrupamiento" data-msg-required="Nombre Ingles es Requerido"/>
							</td>
						</tr>						
						<tr>
							<td>Descripcion</td>
							<td>
							<textarea name="descripcion" id="descripcion" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Descripcion es Requerido"><?php echo $tpl['arr'][0] ["descripcion"];?></textarea>
							</td>
						</tr>
						<tr>
							<td>Descripcion Ingles</td>
							<td>
							<textarea name="descripcion_eng" id="descripcion_eng" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Descripcion Ingles es Requerido"><?php echo $tpl['arr'][0] ["descripcion_eng"];?></textarea>
							</td>
						</tr>
						<tr>
							<td>Tags</td>
							<td>
							<input type="text" name="tags" id="tags" class="pj-form-field w300" data-msg-required="Tags es Requerido" title="Palabras clave o referencias separadas por comas" placeholder="#ruta del sol, #museos" value="<?php echo $tpl['arr'] [0]["tags"];?>" >
						</tr>
						<tr>
							<td>Tags Ingles</td>
							<td>
							<input type="text" name="tags_eng" id="tags_eng" class="pj-form-field w300" data-msg-required="Tags Ingles es Requerido" title="Palabras clave o referencias separadas por comas" placeholder="#ruta del sol, #museos" value="<?php echo $tpl['arr'] [0]["tags_eng"];?>" >
						</tr>
						<tr>
							<td>Keywords</td>
							<td>
							<input type="text" name="key_words" id="key_words" class="pj-form-field w300" data-msg-required="Keywords es Requerido" title="Keywords" placeholder="Keywords" value="<?php echo $tpl['arr'] [0]["key_words"];?>" >
						</tr>
						<tr>
							<td>Keywords Ingles</td>
							<td>
							<input type="text" name="key_words_eng" id="key_words_eng" class="pj-form-field w300" data-msg-required="Keywords Ingles es Requerido" title="Keywords" placeholder="Keywords" value="<?php echo $tpl['arr'] [0]["key_words_eng"];?>" >
						</tr>
						<tr>
							<td>Orden</td>
							<td>
							<input type="number"  min="0" name="orden" id="orden" class="pj-form-field w300" data-msg-required="Orden" title="Orden" placeholder="Orden" value="<?php echo $tpl['arr'] [0]["orden"];?>" >
						</tr>
						<tr>
							<td>Dias Bloqueados</td>
							<td>
							<input type="number"  min="0" name="block_days" id="block_days" class="pj-form-field w300" data-msg-required="Orden" title="Dias Bloqueados" placeholder="Dias Bloqueados" value="<?php echo $tpl['block_days'];?>" >
						</tr>
						<tr>
							<td>Latitud</td>
							<td>
							<input type="text" name="lat" class="pj-form-field w300"  value="<?php echo $tpl['arr'][0] ["lat"];?>" placeholder="Latitud Agrupamiento" data-msg-required="Latitud es Requerido"/>
							</td>
						</tr>						
						<tr>
							<td>Longitud</td>
							<td>
							<input type="text" name="lng" class="pj-form-field w300"  value="<?php echo $tpl['arr'][0] ["lng"];?>" placeholder="Longitud Agrupamiento" data-msg-required="Longitud es Requerido"/>
							</td>
						</tr>						
						<tr>
							<td>Instrucciones Punto de Encuentro</td>
							<td>
							<textarea name="instrucciones" id="instrucciones" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Instrucciones Punto de Encuentro es Requerido"><?php echo $tpl['arr'][0] ["instrucciones"];?></textarea>
							</td>
						</tr>
						<tr>
							<td>Instrucciones Punto de Encuentro Ingles</td>
							<td>
							<textarea name="instrucciones_eng" id="instrucciones_eng" style="resize: none;text-align: left;" rows="4" cols="43" class="pj-form-field" data-msg-required="Instrucciones Punto de Encuentro Ingles es Requerido"><?php echo $tpl['arr'][0] ["instrucciones_eng"];?></textarea>
							</td>
						</tr>						
						<tr>
							<td>Activo</td>
							<td>
							<?php
								if($tpl['arr'] [0]["estado"] == 1){
									$check = true;
								}else{
									$check = false;
								}
							?>
							<input type="checkbox" name="activo" id="activo" value="1|0::1" class="pj-form-field w300" data-msg-required="Activo es Requerido"
							<?php echo $check == '1|0::1' ? ' checked="checked"' : NULL; ?> />
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

							<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionFotos" class="dropzone" enctype= multipart/form-data >
								<input type="hidden" name="id_usuario_servicio" value="<?php echo $_SESSION['usuario_servicio']; ?> " >
								<input type="hidden" name="agrupamiento_id" value="<?php echo $tpl['arr'][0] ['id'];?>" >
							</form>
							</div>

							</td>
						</tr>
					</tbody>
				</table>
				<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
			</form>
		<br><br>
		<?php if(isset($tpl['fotos_calendario']) &&  $tpl['contador_fotos_calendario']>0){ ?>

				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminGroup&amp;action=pjActionDeleteFotos" >
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
				            var resp = AjaxContainerRetrunMessageAgrupamiento("deleteImage", id);
				            console.log(resp);
				            location.reload();
				            		//$("#flag_image").val('1');
				                    } else {
				            txt = "Cancelado";
				                    }
				            }
				</script>



	               <?php }?>
		<br><br>
	             <script>
		         	$(document).ready(function () {
		             	   GetDataAjaxImagenesAgrupamiento("<?php echo $tpl['arr'][0]['id'];?>");
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

			<?php
		}
	}
}
?>
</div>
<div class="clear_both"></div>
