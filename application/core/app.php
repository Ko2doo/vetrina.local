<?php
require 'controller.php';
require 'helpers.php';

class App {

	static $controllers = [
		'index' => '',
		'user' => '',
		'advert' => '',
		'admin' => '',
		'table' => '',
		'cat_settings' => '',
	];

	static $defaultController = 'index';
	static $route = [];
	static private $db = null;

	// дериктории файлов
	static $controllerDir = __DIR__.'/../controllers/';
	static $viewDir 			= __DIR__.'/../views/';

	// входная точка
	static function main() {

		if ( isset($_COOKIE['PHPSESSID']) ) {
			session_start();
		}

		$db = self::db();
		self::route($_SERVER['REQUEST_URI']);

		$controller = self::$defaultController;

		if ( isset(self::$controllers[self::$route[0]]) ) {
			$controller = self::$route[0];
		}

		$file = self::$controllerDir.$controller.'.php';

		if ( file_exists($file) ) {
			require $file;
			$controllerClass = ucfirst($controller);
			new $controllerClass();
		} else{
			header("HTTP/1.0 404 Not Found");
		}
		
	}

	static function db():PDO {

		if ( self::$db === null ) {
			$db_options = require (__DIR__ . '/../config/db.php');

			try {
				self::$db = new PDO(
							$db_options['db_dsn'],
							$db_options['db_user'],
							$db_options['db_pass']);

			} catch (Exception $e) {
		    printf("Соединение не удалось: %s\n", $e->getMessage());
	    		exit();
			}

		} 

		return self::$db;
	}

	static function route($url){
		$path = parse_url($url, PHP_URL_PATH);
		self::$route = array_slice(explode('/', $path), 1);
	}

}