<?php

namespace Classes;

use Classes\User;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;

class RandomEvent
{
    private array $events;
    private Message $message;
    private Discord $discord;

    public function __construct($message,$discord)
    {
        $this->events = include __DIR__ . '/../../config/events.php';
        $this->message = $message;
        $this->discord = $discord;
    }


    public function getEventWithTrigger(User $user, $trigger = 15): void
    {
        if ($this->shouldTriggerEvent($trigger)){

            $this->generateEvent($user);
        }
    }

    public function getEventWithOutTrigger(User $user): void
    {
        $this->generateEvent($user); // DEV Or if needed
    }

    private function generateEvent(User $user): void
    {
        $selectedEvent = $this->events[array_rand($this->events)];

        $event = new $selectedEvent($this->discord, $this->message, $user);

        $event->impact();

        $embed = new Embed($this->discord);
        $embed->setTitle("Random Event : $event->title");
        $embed->setColor('#e000ff');


        $description = $event->description;
        $descriptionImpact = $event->impactDescription;

        $embed->setDescription(" 
        
        $description
        
        $descriptionImpact
        ");

        $this->message->reply(
            MessageBuilder::new()->addEmbed($embed)
        );
    }

    private function shouldTriggerEvent($trigger = 10): bool
    {
        $randomNumber = rand(1, 100);
        $triggerProbability = $trigger;
        return $randomNumber <= $triggerProbability;
    }
}

