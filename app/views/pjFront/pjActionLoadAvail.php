<?php
ob_start();
?>
<div id="abAvailability_<?php echo $_GET['index']; ?>" class="abAvailability">
	<div class="abLoader">
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
	  	<span class="abLoaderMessage">{MSG}</span>
	</div>
</div>
<?php
$template = ob_get_contents();
ob_end_clean();

$template = preg_replace('/\r\n|\n|\t/', '', $template);
$template = str_replace("'", "\"", $template);
$arr = array(
	'template' => $template
);
pjAppController::jsonResponse($arr);
?>