<?php

namespace Interfaces;

use Discord\Discord;
use Discord\Parts\Channel\Message;

interface Command
{
    public function execute(string $commandString, string $arguments);
    public function getDiscord(): ?Discord;
    public function setDiscord(?Discord $discord): void;
    public function getMessage(): ?Message;
    public function setMessage(?Message $message): void;
}