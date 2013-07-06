<?php

define('INCDIR', dirname(__DIR__));
require_once INCDIR."/vendor/autoload.php";


$app->get('/', function() use ($app) {
	$app->render('index');
});


$mustache = new Mustache_Engine([
	'loader' => new Mustache_Loader_FilesystemLoader(
		INCDIR.'/views/', 
		['extension' => '.ms']
	),
	'partials_loader' => new Mustache_Loader_FilesystemLoader(
		INCDIR.'/views/',
		['extension' => '.ms']
	)
]);

class SlimViewSimple extends \Slim\View
{
	protected $_engine = null;
	
	public function __construct($engine)
	{
		$this->_engine = $engine;
	}
	
	public function render($tpl)
	{
		if ($this->_engine == null) {
			$this->_engine = new Mustache_Engine;
		}
		$page = $this->_engine->loadTemplate('content');
		$m = $this->_engine->loadTemplate($tpl);
		return $page->render(['main' => $m->render($this->data)]);
	}
}

$app = new \Slim\Slim(['view' => new SlimViewSimple($mustache)]);

$app->get('/', function() use ($app) {
	$app->render('index');
});

});

$app->run();
