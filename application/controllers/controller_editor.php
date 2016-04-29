<?php

class Controller_Editor extends Controller
{

  public function __construct()
  {
    $this->model = new Model_Editor();
    $this->view = new View();
  }

  function action_index()
	{
    $this->model->getSession();

    if (isset($_POST["client_email"])) {
      $data = [
        'file_name'           => $_FILES["file"]["name"],
        'order_intro'         => $_POST["order_intro"],
        'deadline_date_time'  => $_POST["deadline_date_time"],
        'client_name'         => $_POST["client_name"],
        'price'               => $_POST["price"],
        'volume'              => $_POST["volume"],
        'editor_comment'      => $_POST["editor_comment"],
        'order_type'          => $_POST["order_type"],
        'editors_group'       => $_POST["editors_group"],
        'client_email'        => $_POST["client_email"]
      ];
      foreach ($data as $key => $value) {
        if ($value != null) {
          $value = trim($value);
          $value = stripslashes($value);
          $value = htmlspecialchars($value);
          $data[$key] = $value;
        } else {
          return false;
        }
      }

      if (
          $_FILES["file"]["type"] ==
          "text/rtf" ||
          "text/doc" ||
          "text/docx" ||
          "text/pdf" ||
          "text/xls" ||
          "text/xlsx" ||
          "text/ppt" ||
          "text/pptx"
        ) {
          $this->model->uploadFile(
            $_FILES["file"]["name"],
            $_FILES["file"]["tmp_name"]
          );
          $this->model->composeEmailAndSend(
            $_SERVER['DOCUMENT_ROOT'] . '/html/mailToEditor.html',
            $data
          );
          $this->model->saveOrder($data);
          $this->model->refreshPage('/editor/');
      }
    }

    if (isset($_POST["mail_group"])) {
      $groupId = $_POST["mail_group"];
      $this->model->getMailList($groupId);
      return;
    }

    $data = [
      'groups' => $this->model->getGroups(),
      'orders' => $this->model->getOrders()
    ];

    $this->view->generate(
      'editor_view.php',
      'template_view.php',
      $this->model->getSeoTags('editor'),
      $this->model->getCurrentMenu(),
      $data,
      $this->model->getName($_SESSION['user'])
    );
	}
}
