<?php


namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class AIEncryptionGuardianEvent extends AbstractEvent implements Event
{
    public string $title = 'AI Encryption Guardian';
    public string $description = 'Encounter an advanced Artificial Intelligence defending a secure database. Choose to engage in a hacking duel with the AI or find an alternative route. The AI promises great rewards if defeated.';
    public string $impactDescription = 'Successfully defeating the AI increases hacking XP by 40 and grants access to a secret vault with 150 Cryptocredits. However, failure results in a temporary ban from the system and a reputation hit among fellow hackers.';
    public int $cryptocredits = 150;
    public int $current = 40;

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
