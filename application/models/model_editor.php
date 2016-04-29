<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Mailgun\Mailgun;
use mongodb\mongodb;

class Model_Editor extends Model
{
  public function uploadFile($file_name, $file_tmp_name)
  {
    if (isset($file_name)) {

      if (!empty($file_name)) {
        $location = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
        move_uploaded_file($file_tmp_name, $location.$file_name);
      }

    }
  }

  public function composeEmailAndSend($emailTemplate, $order)
  {
    $config   = parse_ini_file('config.ini');
    $datetime = strtotime($order['deadline_date_time']);
    $datetime = date('d.m.Y H:i', $datetime);

    $content = file_get_contents($emailTemplate);
    $html = sprintf(
              $content,
              $order['order_intro'],
              $this->humaniseOrderType($order['order_type']),
              $order['deadline_date_time'],
              $order['volume'],
              $order['editor_comment']
            );

    $client = new \Http\Adapter\Guzzle6\Client();
    $mgClient = new Mailgun($config['mailgun_key'], $client);
    $domain = "correctarium.com";

    $result = $mgClient->sendMessage($domain, array(
        'from'        => 'Correctarium <mail@correctarium.com>',
        'to'          => 'mail@correctarium.com',
        'bcc'         => $order['editors_group'],
        'subject'     => 'Предложение заказа',
        'html'        => $html
    ), array(
        'attachment'  => array($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$order['file_name'].'')
    ));
  }

  public function saveOrder($data)
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->orders;

    $doc = [
      "client_name"         => $data['client_name'],
      "price"               => $data['price'],
      "volume"              => $data['volume'],
      "deadline_date_time"  => strtotime($data['deadline_date_time']),
      "editor_comment"      => $data['editor_comment'],
      "order_type"          => $this->humaniseOrderType($data['order_type']),
      "order_intro"         => $data['order_intro'],
      "client_email"        => $data['client_email']
    ];

    return $collection->insert($doc);
  }

  public function getOrders()
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->orders;

    $cursor = $collection->find();
    foreach ( $cursor as $id => $value )
    {
        $date = date('d.m.Y H:i', isset($value['deadline_date_time']));
        $order[] = <<<EOT
        <tr>
          <td>{$id}</td>
          <td>{$value['client_name']}</td>
          <td>{$value['order_type']}</td>
          <td>{$value['price']}</td>
          <td>{$value['volume']}</td>
          <td>{$date}</td>
        </tr>\n
EOT;
    }
    return array_reverse($order);
  }

  public function refreshPage($location)
  {
    echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$location.'">';
    exit;
  }

  public function humaniseOrderType($type)
  {
    switch ($type) {
      case 'en':
        $result = "Английский (редактирование)";
        break;
      case 'proof':
        $result = "Корректура";
        break;
      case 'edit':
        $result = "Литературное редактирование";
        break;
      case 'tr':
        $result = "Перевод (русский ←→ украинский)";
        break;
      default:
        $result = "Не определен";
        break;
    }

    return $result;
  }

  public function getSession()
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->users;

    session_start();

    $user = $_SESSION['user'];
    $query = array('email' => $user);
    $cursor = $collection->find($query);

    if ($cursor->getNext() != true) {
      header("location: /login/");
    }
  }

  public function getName($session)
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->users;
    $query = array('email' => $session);
    $cursor = $collection->find($query);

    return $username = $cursor->getNext()['name'];
  }

  public function getMailList($groupId)
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->editors_groups;
    $query = array('group_id' => $groupId);
    $cursor = $collection->find($query);

    return print "{\"group\": " . "\"" . $username = $cursor->getNext()['mail_list'] . "\"" . "}";
  }

  public function getGroups()
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->editors_groups;
    $cursor = $collection->find();

    foreach ($cursor as $id => $value) {
      $groups[] = <<<EOT
      <option value="{$value['group_id']}">{$value['group_name']}</option>
EOT;
    }
    return $groups;
  }
}
