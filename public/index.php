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
	'host'      => '127.0.0.1',
	'database'  => 'phrenetic',
	'username'  => 'root',
	'password'  => '',
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

$app->get('/artists/', function() use ($app) {
	$artists = RMAN\Models\ORM\Artist::get();
	$app->render('artists/index', ['artists' => $artists]);
});

$app->get('/artists/:id', function($id) use ($app) {
	$artist = RMAN\Models\ORM\Artist::with('picture')->find($id);
	$app->render('artists/view', ['artist' => $artist]);
})->conditions(['id' => '\d+']);

$app->get('/artists/create/', function() use ($app) {
	$artist = new RMAN\Models\ORM\Artist;
	$app->render('artists/create', ['artist' => $artist]);
});

$app->post('/artists/save/', function() use ($app) {
	$artist = new RMAN\Models\ORM\Artist($app->request()->post());
	$artist->save();
	$app->response()->redirect('/artists/' . $artist->id);
});

$app->post('/pictures/upload/', function() use ($app) {
	require_once ROOTDIR.'/lib/filestore.php';
	
	$store = new FileStore(ROOTDIR.'/store/picture-store.zip');
	$pictures = [];
	
	foreach($_FILES as $file) {
		
		$info = (object)$file;
		$img = Intervention\Image\Image::make($info->tmp_name);
		
		unset($info->error);
		$info->storename = $store->add($info->tmp_name);
		unset($info->tmp_name);
		
		$picture = new RMAN\Models\ORM\Picture((array)$info);
		$picture->width = $img->width;
		$picture->height = $img->height;
		$picture->save();
		
		$pictures[] = $picture->toArray();
	}
	
	$response = $app->response();
	
	$response['Content-Type'] = 'application/json';
	$response->body(json_encode($pictures));
});

$app->get('/pictures/display/:storename', function($storename) use ($app) {
	require_once ROOTDIR.'/lib/filestore.php';
	
	$store = new FileStore(ROOTDIR.'/store/picture-store.zip');
	$picture = RMAN\Models\ORM\Picture::where('storename', $storename)->first();
	
	if (empty($picture)) {
		die('FOOBAR');
	}
	
	$response = $app->response();
	$response['Content-Type'] = $picture->type;
	$response->body($store->get($picture->storename));
});

$app->run();
