<?php

class Controller_404 extends Controller
{
  function __construct()
	{
		$this->model = new Model();
    $this->view = new View();
	}

  function action_index()
	{
    $this->view->generate(
      '404_view.php',
      'empty_template_view.php',
      $this->model->getSeoTags('404')
    );
	}
}

 ?>
