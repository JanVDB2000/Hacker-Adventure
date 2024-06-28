<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use Monolog\Logger;
use Discord\Discord;
use Discord\WebSockets\Event;
use Services\CommandProcessor;
use Discord\WebSockets\Intents;
use Discord\Parts\Channel\Message;
use Monolog\Handler\StreamHandler;

include __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

R::setup('sqlite:hacker-adventure.sqlite');

$commands = include __DIR__ . '/config/commands.php';

$commandProcessor = new CommandProcessor();

foreach($commands as $key => $value) {
    $commandProcessor->registerCommand($key, $value);
}


try {
    $logger = new Logger('DiscordPHP');
    $date = Carbon::now()->format('d-m-Y');
    $logger->pushHandler(new StreamHandler(__DIR__ . "/var/log/bot-$date.log", \Monolog\Level::Debug));

    $discord = new Discord([
        'token' => $_ENV['SECRET_KEY'],
        'intents' => Intents::getDefaultIntents(),
        'logger' => $logger,
    ]);

    $discord->on('ready', function (Discord $discord) use ($commandProcessor) {
        echo "Hacker Adventure Bot is ready!", PHP_EOL;

        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) use ($commandProcessor) {
            $commandProcessor->setDiscord($discord);
            $commandProcessor->setMessage($message);
            $commandProcessor->processCommand($message->content);
        });
    });


    $discord->run();
} catch (\Discord\Exceptions\IntentException $e) {

}

