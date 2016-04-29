<?php

class Controller_Who extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'who_view.php',
      'template_view.php',
      $this->model->getSeoTags('who'),
      $this->model->getCurrentMenu()
    );
	}
}

 ?>
