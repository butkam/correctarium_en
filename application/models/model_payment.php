<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/library/liqpay.php';

use Parse\ParseClient;
use Parse\ParseCloud;
use Parse\ParseQuery;
use Parse\ParseObject;
use Mailgun\Mailgun;

$conf = parse_ini_file('config.ini');
ParseClient::initialize($conf['key_1'], $conf['key_2'], $conf['key_3']);

class Model_Payment extends Model
{
  public $readyToSendPaymentData = [];
  public $rawPaymentData = [];

  /**
  * Устанавливает cookie с $order_id при заходе на страницу
  * @param String $order_id generate every time page /make-order/ loaded
  */

  public function setOrderId($order_id)
  {
    return setcookie("corr_user_id", $order_id, time()+3600);
  }

  /**
  * Гинерируем данные платежа для Liqpay
  * @param String $price Price of order separated by comma
  * @param String $order_id AJAX from /make-order/ page
  * @param String $public_key from config.ini
  * @param String $private_key from config.ini
  * @return JSON with payment data ready to send to Liqpay
  */

  public function setPaymentData($price, $order_id)
  {
    $conf = parse_ini_file('config.ini');
    $this->rawPaymentData = parse_ini_file('configs/payment_data.ini');
    $this->rawPaymentData['public_key'] = $conf['public_key'];
    $this->rawPaymentData['amount'] = $price;
    $this->rawPaymentData['order_id'] = $order_id;

    $liqpay = new LiqPay($conf['public_key'], $conf['private_key']);
    $html = $liqpay->cnb_signature($this->rawPaymentData);

    $this->readyToSendPaymentData['data'] = base64_encode(json_encode($this->rawPaymentData));
    $this->readyToSendPaymentData['signature'] = $html;
    $this->readyToSendPaymentData['order_id'] = $order_id;

    return json_encode($this->readyToSendPaymentData);
  }

  /**
  * Liqpay стучится со статусом платежа в /payment/, проеряем статус,
  * берем $order_id, получаем данные из базы, отправляем письмо клиенту
  * и нам
  * @TODO: Взять заказ из Монго, сформировать и отправить письмо
  * @param base64 $data Liqay return payment data
  * @param string $signature Liqpay sign
  */

  public function getPaymentStatusAndSendEmails($data, $signature)
  {
    $conf = parse_ini_file('config.ini');
    $sign = base64_encode( sha1($conf['private_key'] . $data . $conf['private_key'], 1 ));
    $order_data = json_decode(base64_decode($data), true);

    $offer = new ParseObject("Offer");
    $payment = new ParseObject("Payment");
    $bot = new nomorespellingerrors\teledit\Bot();

    if (
        $sign === $signature &&
        $order_data['status'] === 'success' ||
        $order_data['status'] === 'processing' ||
        $order_data['status'] === 'wait_secure' ||
        $order_data['status'] === 'wait_accept' ||
        $order_data['status'] === 'wait_lc' ||
        $order_data['status'] === 'sandbox'
       ) {

        $query = new ParseQuery("Offer");
       	$query->equalTo("order_id", $order_data['order_id']);
       	$results = $query->find();
       	for ($i = 0; $i < count($results); $i++) {
       	  $object = $results[$i];
       	  echo $object->getObjectId() . ' - ' . $object->get('order_id');
       	}

        $bot->processPaymentResult($order_data['order_id'], true);

        $payment->set("payment", $order_data['status']);
        $payment->set("order_id", $order_data['order_id']);
        $payment->set("createdBy", $object);
        $payment->save();

        $order = $this->getOrderData($order_data['order_id']);
        $this->sendToUser(
          $_SERVER['DOCUMENT_ROOT'] . '/html/mailToUser.html',
          $order
        );
        $this->sendToUs(
          $_SERVER['DOCUMENT_ROOT'] . '/html/mailToUs.html',
          $order
        );

      } else {
        $bot->processPaymentResult($order_data['order_id'], false);
      }
  }

  /**
  * Проверка статуса платежа. Когда клиент возвращается на сайт /payment/
  * запрашиваем данные платежа и редиректим на соответствующую страницу.
  * @param String $order_id geting from cookie
  */

  public function checkPaymentStatus($order_id)
  {
    $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
    $conf = parse_ini_file('config.ini');

    $liqpay = new LiqPay($conf['public_key'], $conf['private_key']);
    $res = $liqpay->api("payment/status", array(
      'version'       => '3',
      'order_id'      => $order_id
    ));

    if (
        $res->status === 'success' ||
        $res->status === 'processing' ||
        $res->status === 'wait_secure' ||
        $res->status === 'wait_accept'
    ) {
      	header("Location: ". $host ."success/");
    } elseif ($res->status === 'failure') {
        header("Location: ". $host ."payment-failure/");
    } else {
      	header("Location: " . $host);
    }

    die();
  }

  public function getOrderData($order_id)
  {
    $connection = new MongoClient();
    $collection = $connection->correctarium->orders;

    $query = array('order_id' => $order_id);
    $cursor = $collection->find($query);
    return $order = $cursor->getNext();
  }

  public function sendToUser($emailTemplate, $order)
  {
    $content = file_get_contents($emailTemplate);
    $html = sprintf(
              $content,
              $order['order_id'],
              $order['est_delivery'],
              $order['price'],
              $order['client_text']
            );
    $config = parse_ini_file('config.ini');

    $client = new \Http\Adapter\Guzzle6\Client();
    $mgClient = new Mailgun($config['mailgun_key'], $client);
    $domain = "correctarium.com";
    $result = $mgClient->sendMessage($domain, array(
        'from'        => 'Correctarium <mail@correctarium.com>',
        'to'          => $order['client_email'],
        'subject'     => 'Заказ принят',
        'html'        => $html
    ));
  }

  public function sendToUs($emailTemplate, $order)
  {
    $content = file_get_contents($emailTemplate);
    $html = sprintf(
              $content,
              $order['order_id'],
              $order['client_text'],
              $order['price'],
              $order['est_delivery'],
              $order['client_email'],
              $order['client_comment'],
              $order['order_type']
            );
    $config = parse_ini_file('config.ini');

    $client = new \Http\Adapter\Guzzle6\Client();
    $mgClient = new Mailgun($config['mailgun_key'], $client);
    $domain = "correctarium.com";
    $result = $mgClient->sendMessage($domain, array(
        'from'        => 'Correctarium <mail@correctarium.com>',
        'to'          => 'mail@correctarium.com',
        'bcc'         => 'butkamone@gmail.com',
        'subject'     => 'Новый заказ',
        'html'        => $html
    ));
  }
}

 ?>
