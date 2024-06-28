<?php

namespace Commands;


use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use RedBeanPHP\R;
use src\AbstractClasses\Command as AbstractCommand;
use src\Interfaces\Command;

class LeaderBoardCommand extends AbstractCommand implements Command
{
    public function __construct(?Discord $discord = null, ?Message $message = null)
    {
        parent::__construct($discord, $message);
    }

    public function execute(string $commandString, string $arguments = ''): void
    {
        $topUsers = $this->findTopUsers();
        $formattedDescription = $this->formatTopUsers($topUsers);
        $this->sendLeaderBoardOverview($formattedDescription);
    }

    private function findTopUsers(int $amount = 10): array
    {
        return R::findAll('user', 'WHERE user_name IS NOT NULL ORDER BY cryptocredits DESC LIMIT ' . $amount);
    }

    private function formatTopUsers(array $topUsers): string
    {
        $text = '';
        $count = 0;
        foreach ($topUsers as $user) {
            $count ++;
            $userName = ucfirst($user->user_name);
            $text .= "$count: $userName, Crypto credits : {$user->cryptocredits}\n";
        }
        return $text;
    }

    private function sendLeaderBoardOverview(string $formattedDescription): void
    {
        $embedHA = new Embed($this->discord);
        $embedHA->setTitle("Top Hackers Leaderboard:");
        $embedHA->setColor('#58b9ff');
        $embedHA->setDescription($formattedDescription);

        $this->message->reply(
            MessageBuilder::new()
                ->addEmbed($embedHA)
        );
    }

    public function getDiscord(): ?Discord
    {
        return $this->discord;
    }

    public function setDiscord(?Discord $discord): void
    {
        $this->discord = $discord;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): void
    {
        $this->message = $message;
    }
}