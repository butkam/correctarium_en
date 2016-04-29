<?php

namespace nomorespellingerrors\teledit;
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;

/**
 * Class User
 * @package nomorespellingerrors\teledit
 */
class User {
    const USER_CLASS = 'TelegramUser';
    const CHAT_CLASS = 'TelegramChat';
    const OFFER_CLASS = 'Offer';

    public function __construct($helper) {
        $helper = $helper ? $helper : new Helper();

        ParseClient::initialize(
            $helper->getConfigValue('appKey', 'Parse.com'),
            $helper->getConfigValue('restKey', 'Parse.com'),
            $helper->getConfigValue('masterKey', 'Parse.com')
        );
    }

    /**
     * @param $userFromTelegram UserStruct
     * @param $lastActionTs int Timestamp
     * @return string
     */
    public function insertLead($userFromTelegram, $lastActionTs) {
        $user = $this->getOrInsertUser($userFromTelegram);
        $this->insertOrUpdateChat($user, $userFromTelegram->chatId, 'start', $lastActionTs);
        return $user->getObjectId();
    }

    /**
     * @param $userFromTelegram UserStruct
     * @param $workType string
     * @param $price string
     * @param $deadline string
     * @param $text string
     * @return string Offer->order_id
     */
    public function insertOffer($userFromTelegram, $workType, $text, $price, $deadline, $lastActionTs) {
        $user = $this->getOrInsertUser($userFromTelegram);
        $chat = $this->insertOrUpdateChat($user, $userFromTelegram->chatId, 'offer', $lastActionTs);

        $offer = new ParseObject($this::OFFER_CLASS);
            $offer->set('name', $user->get('firstName') . ' ' . $user->get('lastName'));
            $offer->set('orderType', ($workType == 'proof') ? 'correction' : $workType);
            $offer->set('order_id', $this->getRandomOrderId());
            $offer->set('price', $price);
            $offer->set('text', $text);
            $offer->set('time', $deadline);
            $offer->set('createdBy', $chat);
        $offer->save();
        $offer->fetch();
        return $offer->get('order_id');
    }

    /**
     * @param $userFromTelegram UserStruct
     * @param $lastAction string
     * @param $lastActionTs int
     * @return bool
     */
    public function setLastAction($userFromTelegram, $lastAction, $lastActionTs) {
        $user = $this->getOrInsertUser($userFromTelegram);
        $this->insertOrUpdateChat($user, $userFromTelegram->chatId, $lastAction, $lastActionTs);
        return !!$user;
    }

    /**
     * @param $userFromTelegram UserStruct
     * @return string
     */
    public function getLastAction($userFromTelegram) {
        $user = $this->getOrInsertUser($userFromTelegram);

        $chatQuery = new ParseQuery($this::CHAT_CLASS);
        $chatQuery->equalTo('createdBy', $user);
        $chatQuery->equalTo('chatId', $userFromTelegram->chatId);
        $chatQuery->descending('lastActionTimestamp');
        $chatQuery->select('lastAction');

        $chat = $chatQuery->first();

        return $chat ? $chat->get('lastAction') : '';
    }

    public function getLastOrderId($userFromTelegram)
    {
      $user = $this->getOrInsertUser($userFromTelegram);

      $chatQuery = new ParseQuery($this::CHAT_CLASS);
      $chatQuery->equalTo('createdBy', $user);

      $chat = $chatQuery->first();

      $offer = new ParseQuery($this::OFFER_CLASS);
      $offer->equalTo('createdBy', $chat);
      $offer->descending('createdAt');

      $lastOffer = $offer->first();
      
      return $lastOffer ? $lastOffer->get('order_id') : '';
    }

    /**
     * @param $orderId string
     * @return OfferStruct
     */
    public function getOffer($orderId) {
        $offerPopulated = new OfferStruct();

        $offerQuery = new ParseQuery($this::OFFER_CLASS);
        $offerQuery->equalTo('order_id', $orderId);

        $offer = $offerQuery->first();

        if (!$offer) {
            $offerPopulated->isFromTelegram = false;
            return $offerPopulated;
        }

        $offerPopulated->id = $offer->get('order_id');

        $chat = $offer->get('createdBy');

        if (!$chat) {
            $offerPopulated->isFromTelegram = false;
            return $offerPopulated;
        }

        $chat->fetch();

        $user = $chat->get('createdBy');
        $user->fetch();

        $offerPopulated->firstName = $user->get('firstName');
        $offerPopulated->lastName  = $user->get('lastName');
        $offerPopulated->userName  = $user->get('userName');
        $offerPopulated->userId = $user->get('userId');
        $offerPopulated->orderJobType = $offer->get('orderType') == 'correction' ? 'proof' : $offer->get('orderType');
        $offerPopulated->orderVolume = Helper::getSymCount($offer->get('text'));
        $offerPopulated->orderPayAmount = $offer->get('price');
        $offerPopulated->orderDeadline = $offer->get('time');
        $offerPopulated->orderText = $offer->get('text');
        $offerPopulated->chatId = $chat->get('chatId');
        $offerPopulated->isFromTelegram = true;

        return $offerPopulated;
    }

    /**
     * @param $offer OfferStruct
     * @return string
     */
    public function changeOrderId($offer) {
        $newOrderId = $this->getRandomOrderId();

        $offerQuery = new ParseQuery($this::OFFER_CLASS);
        $offerQuery->equalTo('order_id', $offer->id);

        $offer = $offerQuery->first();
        $offer->set('order_id', $newOrderId);
        $offer->save();

        return $newOrderId;
    }

    /**
     * @param $userInput UserStruct
     * @return ParseObject
     */
    protected function getOrInsertUser($userInput) {
        $userQuery = new ParseQuery($this::USER_CLASS);
        $userQuery->equalTo('userId', $userInput->userId);
        $userOutput = $userQuery->first();

        if ($userOutput) {
            return $userOutput;
        }

        $userOutput = new ParseObject($this::USER_CLASS);
        $userOutput->set('userId', $userInput->userId);
        $userOutput->set('firstName', $userInput->firstName);
        $userOutput->set('lastName', $userInput->lastName);
        $userOutput->set('userName', $userInput->userName);
        $userOutput->save();

        return $userOutput;
    }

    /**
     * @param $user ParseObject
     * @param $chatId int
     * @param $lastAction string
     * @param $lastActionTs int
     * @return ParseObject
     */
    protected function insertOrUpdateChat($user, $chatId, $lastAction, $lastActionTs) {
        $chatQuery = new ParseQuery($this::CHAT_CLASS);
        $chatQuery->equalTo('chatId', $chatId);
        $chatQuery->equalTo('createdBy', $user);

        $chat = $chatQuery->first();

        if (!$chat) {
            $chat = new ParseObject($this::CHAT_CLASS);
            $chat->set('chatId', $chatId);
            $chat->set('createdBy', $user);
        }

        $chat->set('lastAction', $lastAction);
        $chat->set('lastActionTimestamp', $lastActionTs);

        $chat->save();

        return $chat;
    }

    protected function getRandomOrderId() {
        return md5(uniqid('1e856a8ea4e8f5bb5ce43feb268f937b', true));
    }
}
