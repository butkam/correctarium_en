<?php

class Controller_Make_Order extends Controller
{
  function __construct()
	{
		$this->model = new Model_Make_Order();
		$this->view = new View();
	}

  function action_index()
	{
    if (isset($_POST['email']) && $_POST['text']) {
      $volume = strlen(utf8_decode($_POST['text']));
      $data = [
        'client_name'         => $_POST['name'],
        'client_email'        => $_POST['email'],
        'client_text'         => $_POST['text'],
        'price'               => $_POST['price'],
        'volume'              => $volume,
        'deadline_date_time'  => null,
        'est_delivery'        => $_POST['time'],
        'order_id'            => $_POST['order_id'],
        'client_comment'      => $_POST['comment'],
        'order_type'          => $_POST['humanOrderType']
      ];
      return $this->model->saveOrderData($data);
    }

    $data = $this->model->setOrderId();
		$this->view->generate(
      'make_order_view.php',
      'template_view.php',
      $this->model->getSeoTags('make-order'),
      $this->model->getCurrentMenu(),
      $data
    );

	}
}
