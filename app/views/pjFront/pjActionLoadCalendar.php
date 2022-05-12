<?php
ob_start();
?>
<div id="abWrapper_<?php echo $_GET['cid']; ?>" class="abWrapper">
	<div id="abLoader_<?php echo $_GET['cid']; ?>" class="abLoader">
		<div class="abLoaderInner">
		  	<div class="spinner-container container1">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container2">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
		  	<div class="spinner-container container3">
		    	<div class="circle1"></div>
		    	<div class="circle2"></div>
		    	<div class="circle3"></div>
		    	<div class="circle4"></div>
		  	</div>
	  	</div>
	  	<span class="abLoaderMessage"></span>
	</div>
	<div id="abCalendar_<?php echo $_GET['cid']; ?>" class="abCalendar"></div>
</div>
<?php
$front_err = str_replace(array('"', "'"), array('\"', "\'"), __('front_err', true, true));
$days = str_replace(array('"', "'"), array('\"', "\'"), __('days', true, true));
$template = ob_get_contents();
ob_end_clean();

$template = preg_replace('/\r\n|\n|\t/', '', $template);
$template = str_replace("'", "\"", $template);
if (isset($tpl['option_arr']['private_key']))
{
	unset($tpl['option_arr']['private_key']);
}
$arr = array(
	'opts' => $tpl['option_arr'],
	'template' => $template,
	'limits' => $tpl['limit_arr'],
	'days' => $days,
	'error_msg' => $front_err
);
pjAppController::jsonResponse($arr);
?>