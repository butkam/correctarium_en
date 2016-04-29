<?php

class Controller_About extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'about_view.php',
      'template_view.php',
      $this->model->getSeoTags('about'),
      $this->model->getCurrentMenu()
    );
  }
}
