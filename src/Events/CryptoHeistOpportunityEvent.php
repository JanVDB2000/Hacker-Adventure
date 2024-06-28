<?php


namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;


class CryptoHeistOpportunityEvent extends AbstractEvent implements Event
{
    public string $title = 'Crypto Heist Opportunity';
    public string $description = 'Intel suggests an opportunity to steal a substantial amount of Cryptocredits from a vulnerable exchange. Proceed with the heist, and reap the rewards. Decline, and maintain a clean reputation.';
    public string $impactDescription = 'Successfully executing the heist brings in 200 Cryptocredits but decreases your reputation significantly. Refusing the opportunity maintains your reputation but leads to the potential ire of other hackers.';
    public int $cryptocredits = 200;
    public int $current = -50; // Reputation hit

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
