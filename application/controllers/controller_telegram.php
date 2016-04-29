<?php

class Controller_Telegram extends Controller
{
  public function __construct()
  {
    $this->model = new Model();
    $this->view = new View();
    $this->paymentTelegram = new Model_Telegram();
  }
  function action_index()
	{
	}

  function action_payment()
  {
    $this->paymentTelegram->setCookie();
    $this->view->generate(
      'telegram_view.php',
      'template_view.php',
      $this->model->getSeoTags('make-order'),
      $this->model->getCurrentMenu(),
      $this->paymentTelegram->getTelegramPaymentPage()
    );
  }

}
