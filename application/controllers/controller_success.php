<?php

class Controller_Success extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'success_view.php',
      'template_view.php',
      $this->model->getSeoTags('make-order'),
      $this->model->getCurrentMenu()
    );
	}
}

 ?>
