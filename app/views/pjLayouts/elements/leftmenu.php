<?php
if ($controller->isAdmin() && pjObject::getPlugin('pjOneAdmin') !== NULL)
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
switch (true)
{
	case $controller->isAdmin():
		include dirname(__FILE__) . '/leftmenu_admin.php';
		break;
	case $controller->isEditor():
		include dirname(__FILE__) . '/leftmenu_editor.php';
		break;
	case $controller->isOwner():
		include dirname(__FILE__) . '/leftmenu_owner.php';
		break;
}
?>