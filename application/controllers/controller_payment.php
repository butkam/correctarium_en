<?php

class Controller_Payment extends Controller
{
  function __construct()
  {
    $this->model = new Model_Payment();
  }

  function action_index()
	{
    $host = $_SERVER['HTTP_HOST'];
    $conf = parse_ini_file('config.ini');
    if (isset($_POST["price"]) && isset($_POST["order_id"]))
    {
      $price = $_POST["price"];
      $order_id = $_POST["order_id"];
      $this->model->setOrderId($order_id);
      echo $this->model->setPaymentData(
        $price,
        $order_id,
        $conf['public_key'],
        $conf['private_key']
      );
    } elseif (isset($_COOKIE['corr_user_id'])) {
      $order_id = $_COOKIE['corr_user_id'];
      $this->model->checkPaymentStatus($order_id);
    } else {
      header("Location: http://" . $host . "/");
    }

    if (isset($_POST["data"]) && isset($_POST["signature"]))
    {
      $data = $_POST["data"];
      $signature = $_POST["signature"];
      $this->model->getPaymentStatusAndSendEmails($data, $signature);
    }
	}
}

 ?>
