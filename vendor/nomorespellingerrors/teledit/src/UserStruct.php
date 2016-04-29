<?php

namespace nomorespellingerrors\teledit;

class UserStruct {
    /** @var int */
    public $userId;

    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $userName;

    /** @var int */
    public $chatId;

    public function __construct($userId = null, $firstName = null, $lastName = null, $userName = null, $chatId = null) {
        $this->userId    = $userId;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->userName  = $userName;
        $this->chatId    = $chatId;
    }
}
