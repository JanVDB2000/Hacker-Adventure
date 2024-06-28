<?php


namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class BlueprintEvent extends AbstractEvent implements Event
{
    public string $title = '';
    public string $description = '';
    public string $impactDescription = '';
    public int $cryptocredits = 0;
    public int $current = 0;
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
        $this->user->increaseCurrent($this->current);
        $this->user->increaseCryptocredits($this->cryptocredits);
        $this->user->decreaseCryptocredits($this->cryptocredits);
        $this->user->save();
    }
}