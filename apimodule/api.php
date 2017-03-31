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
$c = Context::getContext()->currency->iso_code;
/*echo "<pre>";
print_r($c->iso_code);
echo "</pre>"*/;

$number = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); // 31
echo "Всего {$number} дней в ". date('M'). " " . date('Y'). " года";
die();
/*$url = Context::getContext()->link->getModuleLink('apimodule', 'auth',$params);
Tools::redirect($url);*/