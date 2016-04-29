<?php

class Controller_Payment_Failure extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
  }
  function action_index()
	{
		$this->view->generate(
      'payment_failure_view.php',
      'template_view.php',
      $this->model->getSeoTags('make-order'),
      $this->model->getCurrentMenu()
    );
	}
}

 ?>
