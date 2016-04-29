<?php

class Controller_Price extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'price_view.php',
      'template_view.php',
      $this->model->getSeoTags('price'),
      $this->model->getCurrentMenu()
    );
	}
}

 ?>
