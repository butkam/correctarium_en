<?php

namespace nomorespellingerrors\teledit;

// TODO Move hardcoded strings to the messages/helper_*.txt
class OfferStruct {
    /** @var string */
    public $id;

    /** @var string */
    public $firstName;

    /** @var int */
    public $userId;

    /** @var string */
    public $lastName;

    /** @var string */
    public $userName;

    /** @var int */
    public $chatId;

    /** @var string */
    public $orderJobType;

    /** @var int */
    public $orderVolume;

    /** @var string */
    public $orderPayAmount;

    /** @var string */
    public $orderDeadline;

    /** @var string */
    public $orderText;

    /** @var bool */
    public $isFromTelegram;

    public function getJobTypeLabel() {
        if ($this->orderJobType == 'proof') {
            return 'корректуру';
        }

        return 'редактуру';
    }

    public function getUsername() {
        return empty($this->userName) ? 'не указывал @username' : $this->userName;
    }
}
