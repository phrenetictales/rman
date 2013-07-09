<?php

define('ROOTDIR', dirname(__DIR__));
require_once ROOTDIR."/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection as DatabaseConnection;
use Illuminate\Database\MySqlConnection as MySqlConnection;
use Illuminate\Events\Dispatcher as EventDispatcher;

O\O::init();


////////////////////////////////////////////////////////////////////////////////
// Load the Mustache Template engine and configure it                         //
////////////////////////////////////////////////////////////////////////////////
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

////////////////////////////////////////////////////////////////////////////////
// Load Laravel Database and ORM (Eloquent)                                   //
////////////////////////////////////////////////////////////////////////////////
$capsule = new Capsule;
$capsule->addConnection(array(
	'driver'    => 'mysql',
	'host'      => '127.0.0.1',
	'database'  => 'phrenetic',
	'username'  => 'root',
	'password'  => 'shit4b',
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

////////////////////////////////////////////////////////////////////////////////
// Hello World!                                                               //
// TODO: replace with front page: slideshow, news, etc..                      //
////////////////////////////////////////////////////////////////////////////////
$app->get('/', function() use ($app) {
	$app->render('index');
});

////////////////////////////////////////////////////////////////////////////////
// Artists                                                                    //
//                                                                            //
// * List                                                                     //
// * Create                                                                   //
// * Save                                                                     //
// * View                                                                     //
//                                                                            //
// TODO: edit                                                                 //
////////////////////////////////////////////////////////////////////////////////
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

////////////////////////////////////////////////////////////////////////////////
// Releases                                                                   //
//                                                                            //
// * List                                                                     //
// * Create                                                                   //
// * Save                                                                     //
// * View                                                                     //
//                                                                            //
// TODO: edit                                                                 //
////////////////////////////////////////////////////////////////////////////////
$app->get('/releases/', function() use ($app) {
	$releases = RMAN\Models\ORM\Release::get();
	$app->render('releases/index', ['releases' => $releases]);
});

$app->get('/releases/:id', function($id) use ($app) {
	$release = RMAN\Models\ORM\Release::with('picture')->find($id);
	$app->render('releases/view', ['release' => $release]);
})->conditions(['id' => '\d+']);

$app->get('/releases/create/', function() use ($app) {
	$release = new RMAN\Models\ORM\Release;
	$artists = RMAN\Models\ORM\Artist::get();
	
	$tags = array_map(function($artist) {
		
		return array(
			'id'	=> $artist['id'],
			'value'	=> $artist['id'],
			'label'	=> $artist['name']
		);
	}, $artists->toArray());
	
	$app->render('releases/create', [
		'release'	=> $release,
		'tags'	=> json_encode($tags)
	]);
});

$app->post('/releases/save/', function() use ($app) {
	
	$request = $app->request();
	$release = new RMAN\Models\ORM\Release;
	
	
	$release->title = $request->post('title');
	$release->picture_id = $request->post('picture_id');
	
	$release->save();
	
	$order = 0;
	
	foreach($request->post('tracks') as $trk) {
		if (is_integer($trk)) {
			$track = RMAN\Models\ORM\Track::find($track);
			$release->tracks()->save($track);
		}
		else {
			$track = new RMAN\Models\ORM\Track;
			$track->title = $trk['title'];
			$track->order = ++$order;
			$release->tracks()->save($track);
			
			
			foreach($trk['artists'] as $artist_id) {
				$artist = RMAN\Models\ORM\Artist::find($artist_id);
				$track->artists()->attach($artist->id);
			}
			
			$track->push();
		}
		
	}
	
	$release->push();
	$app->response()->redirect('/releases/' . $release->id);
});

////////////////////////////////////////////////////////////////////////////////
// Pictures                                                                   //
//                                                                            //
// * Upload                                                                   //
// * Display                                                                  //
////////////////////////////////////////////////////////////////////////////////
$app->post('/pictures/upload/', function() use ($app) {
	$pictures = [];
	
	
	foreach($_FILES as $file) {
		
		try {
			$img = Intervention\Image\Image::make($file['tmp_name']);
			$storename = Phrenetic\StoreFile::instance('pictures')
						->add($file['tmp_name']);
			
			$picture = new RMAN\Models\ORM\Picture([
				'type'		=> $file['type'],
				'name'		=> $file['name'],
				'storename'	=> $storename
			]);
			$picture->width = $img->width;
			$picture->height = $img->height;
			$picture->save();
			
			$pictures[] = $picture->toArray();
		}
		catch (Exception $e) {
			$pictures[] = ['error' => $e->getMessage()];
		}
	}
	
	$response = $app->response();
	
	$response['Content-Type'] = 'application/json';
	$response->body(json_encode($pictures));
});

$app->get('/pictures/display/:storename', function($storename) use ($app) {
	
	$store = new Phrenetic\StoreFile('pictures');
	$picture = RMAN\Models\ORM\Picture::where('storename', $storename)->first();
	
	if (empty($picture)) {
		die('FOOBAR');
	}
	
	$response = $app->response();
	$response['Content-Type'] = $picture->type;
	$response->body($store->get($picture->storename));
});

$app->run();
