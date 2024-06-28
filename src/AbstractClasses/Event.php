<?php

namespace AbstractClasses;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;

abstract class Event
{
    public string $title = 'Default';
    public string $description = '';
    public string $impactDescription = '';
    public int $cryptocredits = 0;
    public int $current = 0;

    protected ?Discord $discord = null;
    protected ?Message $message = null;
    protected ?User $user = null;
    public function __construct(?Discord $discord, ?Message $message, ?User $user) {
        $this->discord = $discord;
        $this->message = $message;
        $this->user = $user;
    }
}