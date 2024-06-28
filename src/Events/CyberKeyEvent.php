<?php

namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class CyberKeyEvent extends AbstractEvent implements Event
{
    public string $title = 'Cyber key fond';
    public string $description = 'Je vindt een cyber key over een verborgen bankrekening.';
    public string $impactDescription = 'je vindt €100 op de bankrekening maar het vraagt veel kracht dus je stroom gaan naar benenden.';
    public int $cryptocredits = 100;
    public int $current = 25;

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
        $this->user->increaseCryptocredits($this->cryptocredits);
        $this->user->save();
    }
}