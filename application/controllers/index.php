<?php
class Index extends Controller{
	
	function defaultAction(){

		$db = App::db();
		$sql = 'SELECT * FROM posts';
		$result	= $db->query($sql);

		$items = $result->fetchAll();

		$this->view('index', ['items' => $items]);

	}

	
}