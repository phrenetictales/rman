<?php

require_once dirname(__DIR__)."/vendor/autoload.php";

$app = new \Slim\Slim();
$app->get('/', function() {
	print "Hello World<br/>\n";
});

$app->run();
