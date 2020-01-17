<?php

class Controller{

	public function __construct() {
		
		$action = 'default';
		if ( isset(App::$route[1]) && method_exists($this, App::$route[1].'Action') ) {
			$action = App::$route[1];
		}

		$action.='Action';
		$this->$action();
	}

	function defaultAction() {}

	public function view($name, $data = []) {
		$file = App::$viewDir.$name.'.phtml';

		if ( file_exists($file) ) {
			extract($data);
			include $file;
		} else{
			throw new Exception('View not found: '.$name);
		}
	}

	public function redirect($url) {
		header("Location: $url");
	}
}