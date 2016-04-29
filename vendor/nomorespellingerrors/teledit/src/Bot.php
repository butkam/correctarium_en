<?php
namespace nomorespellingerrors\teledit;

use \Telegram\Bot\Api;

/**
 * Class Bot
 * @package nomorespellingerrors\teledit
 * Максимальное кол-во символов, что проходит в 1 сообщение - 4085 (два килобайта)
 */
class Bot {

    /** @var \Telegram\Bot\Api */
    protected $api;

    /** @var User */
    protected $user;

    /** @var Analytics */
    protected $analytics;

    /** @var Helper */
    protected $helper;

    /**
     * @param null $api \Telegram\Bot\Api
     * @param null $user User
     * @param null $analytics Analytics
     * @param null $helper Helper
     */
    public function __construct($api = null, $user = null, $analytics = null, $helper = null) {
        setlocale(LC_NUMERIC, 'ru_RU.UTF-8', 'Russian_Russia.1251');
        date_default_timezone_set('Europe/Kiev');

        $this->helper = $helper ? $helper : new Helper();

        $this->user = $user ? $user : new User($this->helper);
        $this->analytics = $analytics ? $analytics : new Analytics();
        $this->api  = $api  ? $api  : new Api($this->helper->getConfigValue('TelegramApiKey'));
    }

    public function loadUpdateFromWebhook() {
        $update = $this->api->getWebhookUpdates();

        if ($update) {
            $this->processUpdate($update);
        }
    }

    /**
     * @param $update \Telegram\Bot\Objects\Update
     */
    public function processUpdate($update) {
        $msg = $update->getMessage();

        $chatId = $msg->getChat()->getId();
        $userId = $msg->getFrom()->getId();
        $text   = $msg->getText();
        $timestamp = $msg->getDate();

        if ($chatId == $this->helper->getConfigValue('managerGroupId')) {
            $this->api->sendChatAction([
              'chat_id' => $chatId,
              'action' => 'typing'
            ]);
            $this->deliverOrderToUser($msg);
            return;
        }

        $user = new UserStruct(
            $userId,
            $msg->getFrom()->getFirstName(),
            $msg->getFrom()->getLastName(),
            $msg->getFrom()->getUsername(),
            $chatId
        );

        if (strpos($text, '/start') === 0) {
            if (preg_match('/.*confirm_.*/', $text)) {
                $words = explode(' ', $text);
                $arr = explode('_', $words[1]);
                $orderId = $this->user->getLastOrderId($user);
                if ($arr[2]==hash('crc32b','confirm_'.$arr[1])) {
                  $this->processPaymentResult($orderId, true);
                } else {
                  $this->processPaymentResult($orderId, false);
                }

            } else {
              $this->actOnStartCmd($user, $timestamp);
            }
            return;
        }

        if (strpos($text, '/help') === 0) {
            $this->actOnHelpCmd($user, $timestamp);
            return;
        }

        if (strpos($text, '/spell') === 0) {
            if (preg_match('/\/spell .*/', $text)) {
              $words = explode(' ', $text);
              $command = array_shift($words);
              $textToCheck = implode($words, ' ');

              $this->sendEditedTextToUser($user, $textToCheck, $timestamp, $msg);
              return;
            }

            $this->actOnSpellerCmd($user, $text, $timestamp);
            return;
        }

        $newChatParticipant = $msg->getNewChatParticipant();
        if (!empty($newChatParticipant) and $newChatParticipant->getUsername() == 'correctbot') {
            $this->actOnStartCmd($user, $timestamp);
            return;
        }

        if (empty($text)) {
            return; // This is service message that bot should ignore
        }

        if (strpos($text, '/proof') === 0) {
            $this->actOnWorkTypeSelectionCmd($user, 'proof', $timestamp);
            return;
        }

        if (strpos($text, '/edit') === 0) {
            $this->actOnWorkTypeSelectionCmd($user, 'edit', $timestamp);
            return;
        }

        // If we get here we probably should send offer to the user
        $this->api->sendChatAction([
          'chat_id' => $user->chatId,
          'action'  => 'typing'
        ]);
        $lastAction = $this->user->getLastAction($user);

        if (in_array($lastAction, ['proof', 'edit'])) {
            $this->sendOfferToUser($user, $lastAction, $text, $timestamp);
            return;
        }

        if ($lastAction == 'offer') {
            $this->replyWithNotUnderstand( $msg, $this->helper->getMessageTemplate('2user_error_long_text') );
            return;
        }

        if ($lastAction == 'checkSpelling') {
            $this->sendEditedTextToUser($user, $text, $timestamp, $msg, $lastAction);
            return;
        }

        $this->replyWithNotUnderstand($msg);
    }

    /**
     * @param $orderId string
     * @param $isPaymentSuccessful bool
     */
    public function processPaymentResult($orderId, $isPaymentSuccessful) {
        $offer = $this->user->getOffer($orderId);

        if (!$offer->isFromTelegram) {
            return;
        }

        if ($isPaymentSuccessful) {
            $msgToManagers = sprintf(
                $this->helper->getMessageTemplate('2manager_notify_about_order'),
                $offer->firstName,
                $offer->lastName,
                $offer->getUsername(),
                $offer->getJobTypeLabel(),
                $offer->id,
                $offer->orderVolume,
                $offer->orderPayAmount,
                $offer->orderDeadline,
                $offer->orderText
            );

            $this->api->sendMessage([
              'chat_id' => $this->helper->getConfigValue('managerGroupId'),
              'text'    => $msgToManagers
            ]);
            $this->api->sendMessage([
              'chat_id' => $offer->chatId,
              'text'    => $this->helper->getMessageTemplate('2user_payment_success')
            ]);

            $this->setLastActionFromOffer($offer, 'pay-ok');
            return;
        }

        $newOrderId = $this->user->changeOrderId($offer);

        $this->api->sendMessage([
          'chat_id' => $offer->chatId,
          'text'    => sprintf(
            $this->helper->getMessageTemplate('2user_payment_fail'),
            $this->getPaymentLink($newOrderId, $offer->orderPayAmount)
            ),
          'parse_mode'               => true,
          'disable_web_page_preview' => true
        ]);

        $this->setLastActionFromOffer($offer, 'pay-fail');
    }

    /**
     * @param $user UserStruct
     * @param $cmdTimestamp int
     */
    protected function actOnStartCmd($user, $cmdTimestamp) {
        $this->api->sendMessage([
          'chat_id' => $user->chatId,
          'text'    => $this->helper->getMessageTemplate('2user_start')
        ]);
        $this->user->insertLead($user, $cmdTimestamp);
    }

    protected function actOnHelpCmd($user, $cmdTimestamp)
    {
        $this->api->sendMessage([
          'chat_id'    => $user->chatId,
          'text'       => $this->helper->getMessageTemplate('2user_help'),
          'parse_mode' => 'HTML'
        ]);
    }

    /**
     * @param $user UserStruct
     * @param $workType string
     * @param $lastActionTs int
     */
    protected function actOnWorkTypeSelectionCmd($user, $workType, $lastActionTs) {
        $msgTemplatePrefix = '2user_';

        if ($user->userId != $user->chatId) {
            $msgTemplatePrefix = '2usergroup_';
        }

        $this->api->sendMessage([
          'chat_id' => $user->chatId,
          'text'    => $this->helper->getMessageTemplate($msgTemplatePrefix . $workType)
        ]);
        $this->user->setLastAction($user, $workType, $lastActionTs);
    }

    protected function actOnSpellerCmd($user, $text, $timestamp)
    {
      $msgTemplatePrefix = '2user_check_spelling';

      $this->api->sendMessage([
          'chat_id'      => $user->chatId,
          'text'         => $this->helper->getMessageTemplate($msgTemplatePrefix)
      ]);

      $this->user->setLastAction($user, 'checkSpelling', $timestamp);
    }

    /**
     * @param $user UserStruct
     * @param $workType string
     * @param $orderText string
     * @param $lastActionTs string
     */
    protected function sendOfferToUser($user, $workType, $orderText, $lastActionTs) {
        $price = $this->helper->getPayAmount($orderText, $workType);
        $deadline = $this->helper->getDeadline($orderText, $workType);

        $orderId = $this->user->insertOffer($user, $workType, $orderText, $price, $deadline, $lastActionTs);

        $offerText = sprintf(
            $this->helper->getMessageTemplate('2user_offer'),
            $deadline,
            round(str_replace(",", ".", $price)),
            $this->getPaymentLink($orderId, $price)
        );

        $this->api->sendMessage([
          'chat_id'    => $user->chatId,
          'text'       => $offerText,
          'parse_mode' => 'HTML',
          'disable_web_page_preview' => true
        ]);
    }

    /**
     * @param $msg \Telegram\Bot\Objects\Message
     */
    protected function deliverOrderToUser($msg) {
        $offerId = $this->getOfferIdFromManagerReply($msg);

        if (empty($offerId)) {
            $this->replyWithNotUnderstand($msg, 'Чтобы сдать заказ, введите текст ответом на сообщение, содержащее идентификатор заказа.');
            return;
        }

        $offer = $this->user->getOffer($offerId);

        if (empty($offer->chatId) or !$offer->isFromTelegram) {
            $this->replyWithNotUnderstand($msg, 'Не получается связать этот заказ с конкретным чатом Телеграма, сдайте заказ вручную. Пользователь: @' . $offer->getUsername());
            return;
        }

        $this->api->sendMessage([
          'chat_id' => $offer->chatId,
          'text'    => $this->helper->getMessageTemplate('2user_order_delivery_prefix')
        ]);
        $this->api->sendMessage([
          'chat_id' => $offer->chatId,
          'text'    => $msg->getText()
        ]);
        $this->api->sendMessage([
          'chat_id' => $offer->chatId,
          'text'    => $this->helper->getMessageTemplate('2user_order_delivery_suffix')
        ]);

        $this->api->sendMessage([
          'chat_id' => $this->helper->getConfigValue('managerGroupId'),
          'text'   => $this->helper->getMessageTemplate('2manager_order_delivery_success')
        ]);

        $this->setLastActionFromOffer($offer, 'order-delivered');
    }

    /**
     * @return \Telegram\Bot\Objects\User Returns bot data
     */
    public function testBot() {
        return $this->api->getMe();
    }

    /**
     * Temporary method to develop things without WebHooks configuring
     */
    public function getUpdatesFromPolling() {
        $updateIdCachePath = __DIR__ . '/last-update-id.txt';

        if (file_exists ($updateIdCachePath)) {
            $lastUpdate = file_get_contents($updateIdCachePath);
            $updates = $this->api->getUpdates((int)$lastUpdate + 1);
        } else {
            $updates = $this->api->getUpdates();
        }

        $maxUpdateId = 0;

        foreach ($updates as $update) {
            $this->processUpdate($update);

            $maxUpdateId = $maxUpdateId > $update->getUpdateId() ? $maxUpdateId : $update->getUpdateId();
        }

        if ($maxUpdateId) {
            file_put_contents($updateIdCachePath, $maxUpdateId);
        }
    }

    /**
     * @param $managerMsg \Telegram\Bot\Objects\Message
     * @return string
     */
    protected function getOfferIdFromManagerReply($managerMsg) {
        $msgFromBot = $managerMsg->getReplyToMessage();

        if (!$msgFromBot) {
            return '';
        }

        preg_match('/#(?P<id>.+)/u', $msgFromBot->getText(), $matches);

        return empty($matches['id']) ? '' : $matches['id'];
    }

    /**
     * @param $msg \Telegram\Bot\Objects\Message
     * @param $customText string
     */
    protected function replyWithNotUnderstand($msg, $customText = '') {
        $textToSend = empty($customText) ? $this->helper->getMessageTemplate('not_understand') : $customText;

        $this->api->sendMessage([
            'chat_id'     => $msg->getChat()->getId(),
            'text'       => $textToSend,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
            'reply_to_message_id'      => $msg->getMessageId()
        ]);
    }

    protected function sendEditedTextToUser($user, $text, $lastActionTs, $msg, $lastAction = null)
    {
      $textToSend = $this->helper->checkTextWithSpeller($text);
      $textToSend = trim($textToSend);
      $textToSend = stripslashes($textToSend);
      $textToSend = strip_tags($textToSend);

      $this->api->sendMessage([
          'chat_id'     => $msg->getChat()->getId(),
          'text'        => $textToSend,
          'parse_mode'  => 'Markdown',
          'disable_web_page_preview' => false
          // 'reply_to_message_id'      => $msg->getMessageId()
      ]);

      $this->api->sendMessage([
          'chat_id'     => $msg->getChat()->getId(),
          'text'        => $this->helper->getMessageTemplate('2user_spell_info')
      ]);

      $this->user->setLastAction($user, 'checked', time());
    }

    /**
     * @param $orderId string
     * @return string
     */
    public function getPaymentLink($orderId, $price) {
        return "<a href=\"" . $this->helper->getConfigValue('paymentLinkPrefix') . round(str_replace(",", ".", $price)) . $this->helper->getConfigValue('paymentLinkSuffix') . "\">Подтвердить и перейти к оплате</a>";
        // . $orderId
    }

    protected function setLastActionFromOffer($offer, $lastAction) {
        $user = new UserStruct();
        $user->userId = $offer->userId;
        $user->chatId = $offer->chatId;

        $this->user->setLastAction($user, $lastAction, time());
    }

    public function setWebhook($pathToCert) {
        return $this->api->setWebhook(
            $this->helper->getConfigValue('TelegramWebhookUrl'),
            $pathToCert
        );
    }
}
