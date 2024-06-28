<?php

namespace AbstractClasses;

use Discord\Discord;
use Discord\Parts\Channel\Message;

abstract class Command
{
    protected ?Discord $discord = null;
    protected ?Message $message = null;
    public function __construct(?Discord $discord, ?Message $message) {
        $this->discord = $discord;
        $this->message = $message;
    }
}