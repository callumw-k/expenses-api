<?php

namespace App\AuthModule\DTOs;
class Token {
    public string $authToken;

    function __construct($authToken) {
        $this->authToken = $authToken;
    }

}
