<?php

require_once 'vendor/autoload.php';

class Model_A0e5ddb1b0ab879a95d6bb85c7d18535 extends Model
{
  public function getUpdates()
  {
    // TODO Add every two seconds polling

    // require_once 'vendor/autoload.php';

    $bot = new nomorespellingerrors\teledit\Bot();

    $start = time();

    for ($i = 1; $i < 60; $i += 2) {
        $current_time = time() - $start;

        if ($current_time > 59) {
            exit();
        }

        $bot->getUpdatesFromPolling();
        time_sleep_until($start + $i);
    }
  }

  public function getBot()
  {
    $bot = new nomorespellingerrors\teledit\Bot();
    $bot->loadUpdateFromWebhook();
  }
}
