<?php


/* SSL Management */
$useSSL = true;

require('../../config/config.inc.php');
Tools::displayFileAsDeprecated();

// init front controller in order to use Tools::redirect
$controller = new FrontController();
$controller->init();
$params=[
	'action' => 'auth'
];
//echo Context::getContext()->link->getModuleLink('apimodule', 'api',$params);

$url = Context::getContext()->link->getModuleLink('apimodule', 'api',$params);
Tools::redirect($url);