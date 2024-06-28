<?php


namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class ShadowyContactEvent extends AbstractEvent implements Event
{
    public string $title = 'Shadowy Contact';
    public string $description = 'A mysterious figure contacts you with an offer to buy rare hacking tools on the black market. Accept the offer, and your skills will be greatly enhanced. Decline, and risk being marked as uncooperative.';
    public string $impactDescription = 'Accepting the offer increases your hacking XP by 30 but comes with a hefty price of 150 Cryptocredits. Declining leads to a decrease in reputation and potential consequences from the underground hacker community.';
    public int $cryptocredits = 150;
    public int $current = 30;

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
