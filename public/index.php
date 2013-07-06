<?php

define('ROOTDIR', dirname(__DIR__));
require_once ROOTDIR."/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Database\MySqlConnection as MySqlConnection;
use Illuminate\Events\Dispatcher as EventDispatcher;

O\O::init();


$mustache = new Mustache_Engine([
	'loader' => new Mustache_Loader_FilesystemLoader(
		ROOTDIR.'/views/', 
		['extension' => '.ms']
	),
	'partials_loader' => new Mustache_Loader_FilesystemLoader(
		ROOTDIR.'/views/',
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

$capsule = new Capsule;
$capsule->addConnection(array(
	'driver'    => 'mysql',
	'host'      => $_SERVER['DB1_HOST'],
	'database'  => $_SERVER['DB1_NAME'],
	'username'  => $_SERVER['DB1_USER'],
	'password'  => $_SERVER['DB1_PASS'],
	'charset'   => 'utf8',
	'collation' => 'utf8_general_ci',
	'prefix'    => 'phr_',
));

$capsule->setEventDispatcher(new EventDispatcher());
$capsule->bootEloquent();
$capsule->setAsGlobal();


spl_autoload_register(function ($class) {
	$ns = O\c(O\s($class))->explode('\\');
	if ($ns->count() <= 3) {
		return;
	}
	
	if ($ns->slice(0, 3)->implode('\\') == 'RMAN\Models\ORM') {
		$path = ROOTDIR.'/models/ORM/'.
				$ns->slice(3)->implode('/').
				'.php';
		
		if (file_exists($path)) {
			include $path;
		}
	}
});


$app->get('/', function() use ($app) {
	$app->render('index');
});

$app->get('/artists', function() use ($app) {
	$artists = RMAN\Models\ORM\Artist::get();
	$app->render('artists', ['artists' => $artists]);
});

$app->get('/artists/:id', function($id) use ($app) {
	$artist = RMAN\Models\ORM\Artist::find($id);
	$app->render('artist', ['artist' => $artist]);
});

$app->post('/artists/create', function() use ($app) {
	
});

$app->get('/artists/save', function($request) {
});

$app->run();
