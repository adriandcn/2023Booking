<!doctype html>
<html>
	<head>
		<title>Availability Booking Calendar by PHPJabbers.com</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<?php
		if (isset($_GET['cid']))
		{
			if ((int) $_GET['cid'] > 0)
			{
				?><link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss&cid=<?php echo @$_GET['cid']; ?>" type="text/css" rel="stylesheet" /><?php
			} elseif ($_GET['cid'] == 'all') {
				?><link type="text/css" rel="stylesheet" href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailabilityCss" /><?php
			}
		}
		?>
	</head>
	<body>
	<?php
	if (isset($_GET['cid']))
	{
		if ((int) $_GET['cid'] > 0)
		{
			$style = "width: 600px; height: 450px";
			if (isset($_GET['view']))
			{
				switch ($_GET['view'])
				{
					case 3:
						$style = "max-width: 800px; height: 650px";
						break;
					case 6:
						$style = "max-width: 800px; height: 650px";
						break;
					case 12:
						$style = "max-width: 800px; height: 650px";
						break;
				}
			}
			?>
			<div style="<?php echo $style; ?>">
				<script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad&cid=<?php echo @$_GET['cid']; ?><?php echo isset($_GET['view']) && (int) $_GET['view'] > 0 ? '&view=' . (int) $_GET['view'] : NULL; ?><?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? '&locale=' . (int) $_GET['locale'] : NULL; ?>"></script>
			</div>
			<?php
		} elseif ($_GET['cid'] == 'all') {
			?>
			<div style="max-width: 800px; height: 650px">
				<script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadAvailability<?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? '&locale=' . (int) $_GET['locale'] : NULL; ?>"></script>
			</div>
			<?php
		}
	}
	?>
	</body>
</html>