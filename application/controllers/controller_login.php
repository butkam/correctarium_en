<?php

class Controller_Login extends Controller
{
  public function __construct()
  {
    $this->model = new Model_Login();
    $this->view = new View();
  }
  function action_index()
	{
    if(isset($_SESSION['user'])){
      return header("location: /editor/");
    }

    $this->model->login();
		$this->view->generate(
      'login_view.php',
      'template_view.php',
      $this->model->getSeoTags('login'),
      $this->model->getCurrentMenu()
    );
  }
}
