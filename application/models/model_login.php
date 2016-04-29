<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use mongodb\mongodb;

class Model_Login extends Model
{
  public function login()
  {
    session_start();
    $error = '';
    if (isset($_POST['submit'])) {
      if (empty($_POST['email']) || empty($_POST['password'])) {
      $error = "Email or password is invalid";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $email = stripslashes($email);
        $password = stripslashes($password);

        $connection = new MongoClient();
        $collection = $connection->correctarium->users;
        $query = array('email' => $email, 'password' => $password);
        $cursor = $collection->find($query);

        if ($cursor->getNext() == true) {
          $_SESSION['user'] = $email;
          header("location: /editor/");
        } else {
          $error = "Email or password is invalid";
        }
      }
    }
  }
}
