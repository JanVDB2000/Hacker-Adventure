<?php


namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;


class FirewallBreachEvent extends AbstractEvent implements Event
{
    public string $title = 'Firewall Breach';
    public string $description = 'You attempt to infiltrate a highly guarded server, but unexpectedly trigger a powerful firewall. Overcome the firewall, and access valuable data. Fail, and face severe system backlash.';
    public string $impactDescription = 'Successfully breaching the firewall rewards you with 75 Cryptocredits and a reputation boost. Failure results in a loss of 30 hacking XP and damages your hacking tools, requiring expensive repairs.';
    public int $cryptocredits = 75;
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
