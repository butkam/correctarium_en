<?php

class Controller_A0e5ddb1b0ab879a95d6bb85c7d18535 extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->bot = new Model_A0e5ddb1b0ab879a95d6bb85c7d18535();
  }
  function action_index()
	{
    $this->bot->getBot();
    // $this->bot->getUpdates();
	}
}
