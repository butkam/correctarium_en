<?php

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/library/liqpay.php';

use Parse\ParseClient;
use Parse\ParseQuery;

$conf = parse_ini_file('config.ini');
ParseClient::initialize($conf['key_1'], $conf['key_2'], $conf['key_3']);

class Model_Telegram extends Model
{
  public function getTelegramPaymentPage()
  {
    // TODO Error page if there is no orderId or Invalid
    // If already paid LiqPay will throw error on payment page

    $orderId = explode('/', filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL))[3];

    $offerQuery = new ParseQuery('Offer');
    $offerQuery->equalTo('order_id', $orderId);
    $offer = $offerQuery->first();
    $price = $offer->get('price');
    $orderType = $offer->get('orderType');
    $orderLabel = '';
    switch ($orderType) {
        case 'edit':
            $orderLabel = 'Литературное редактирование';
            break;
        case 'correction':
        default:
            $orderLabel = 'Корректура';
            break;
    }
    /*  Готовим платеж  */
    $conf = parse_ini_file('config.ini');
    $liqpay = new LiqPay($conf['public_key'], $conf['private_key']);
    $liqpayParams = [
        'version'        => '3',
        'amount'         => floatval(str_replace(',', '.', $price)),
        'currency'       => 'UAH',
        'description'    => $orderLabel,
        'order_id'       => $orderId
        // , 'sandbox'        => 1
    ];
    $liqpaySignature = $liqpay->cnb_signature($liqpayParams);
    $liqpayParams['public_key'] = $conf['public_key'];
    $liqpayData = base64_encode( json_encode($liqpayParams) );

    $data = [];
    $data['orderLabel'] = $orderLabel;
    $data['price'] = $price;
    $data['liqpayData'] = $liqpayData;
    $data['liqpaySignature'] = $liqpaySignature;

    return $data;
  }

  public function setCookie()
  {
    $orderId = explode('/', filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL))[3];
    # Set cookie with order_id (from proccess-payment/index.php)
    setcookie("corr_user_id", $orderId, time()+3600);
  }
}
