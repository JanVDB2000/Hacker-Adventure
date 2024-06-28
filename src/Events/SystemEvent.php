<?php

namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class SystemEvent extends AbstractEvent implements Event
{
    public string $title = 'System Breach Detected';
    public string $description = 'Your hacking skills have triggered a security alert.';
    public string $impactDescription = 'You successfully infiltrated the system, but the authorities are on high alert.';
    public int $cryptocredits = 0;
    public int $current = 50;

    public function __construct(?Discord $discord, ?Message $message, ?User $user)
    {
        parent::__construct($discord, $message, $user);
    }

    /**
     * @throws SQL
     */
    public function impact(): void
    {
        $this->user->decreaseCurrent($this->current);
        $this->user->save();
    }
}