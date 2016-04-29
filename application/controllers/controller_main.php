<?php

class Controller_Main extends Controller
{
	public function __construct()
	{
		$this->model = new Model();
		$this->view = new View();
	}
	function action_index()
	{
		$this->view->generate(
			'main_view.php',
			'template_view.php',
			$this->model->getSeoTags('main'),
			$this->model->getCurrentMenu()
		);
	}
}
