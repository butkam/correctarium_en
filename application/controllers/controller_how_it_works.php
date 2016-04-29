<?php

class Controller_How_It_Works extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'how_it_works_view.php',
      'template_view.php',
      $this->model->getSeoTags('how-it-works'),
      $this->model->getCurrentMenu()
    );
	}
}

 ?>
