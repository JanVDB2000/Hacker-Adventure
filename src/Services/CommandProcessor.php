<?php

namespace Services;

use Discord\Discord;
use Discord\Helpers\RegisteredCommand;
use Discord\Parts\Channel\Message;

class CommandProcessor
{
    private array $commands = [];

    private ?Discord $discord = null;
    private ?Message $message = null;

    public function registerCommand($commandString, $command): void
    {
        $this->commands[$commandString] = $command;

        //$guild = $this->discord->guilds->get('id', 399558776803557377);
        //$guild->commands->create([
        //    'name' => $commandString,
        //    'description' => 'Geen info',
        //]);
    }

    public function processCommand(string $input): void
    {
        if($this->isCommand($input)) {
            $data = $this->extractCommandAndArguments($input);
            $commandString = $data['commandString'] ?? null;
            $arguments = $data['arguments'] ?? null;

            $this->executeCommand($commandString, $arguments);
        }
    }

    private function isCommand(string $input): bool
    {
        return str_starts_with($input, '!');
    }

    private function extractCommandAndArguments($input): array
    {
        $parts = explode(' ', $input, 2);
        $commandString = strtolower(trim($parts[0], '!'));
        $arguments = $parts[1] ?? '';

        return [
            'commandString' => $commandString,
            'arguments' => $arguments
        ];
    }

    private function executeCommand($commandString, $arguments): void
    {
        if (isset($this->commands[$commandString])) {
            $command = $this->commands[$commandString];
            $commandClass = new $command($this->discord, $this->message);
            $commandClass->execute($commandString, $arguments);
        } else {
            echo "Unknown command: {$commandString}\n";
        }
    }

    public function setDiscord(Discord $discord): void
    {
        $this->discord = $discord;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }
}