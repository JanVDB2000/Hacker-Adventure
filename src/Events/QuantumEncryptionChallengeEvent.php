<?php



namespace Events;

use Classes\User;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Event as AbstractEvent;
use Interfaces\Event;

class QuantumEncryptionChallengeEvent extends AbstractEvent implements Event
{
    public string $title = 'Quantum Encryption Challenge';
    public string $description = 'You stumble upon a cutting-edge quantum encryption challenge. Solve it, and gain access to highly secured data. Fail, and trigger a security alert.';
    public string $impactDescription = 'Successfully solving the challenge grants you 50 Cryptocredits and increases your hacking XP by 20. However, failure results in a fine of 25 Cryptocredits and a decrease in hacking XP by 15.';
    public int $cryptocredits = 50;
    public int $current = 20;

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
