<?php

namespace Commands;

use Classes\User;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use src\AbstractClasses\Command as AbstractCommand;
use src\Interfaces\Command;

class MyIdCardCommand extends AbstractCommand implements Command
{
    public function __construct(?Discord $discord = null, ?Message $message = null)
    {
        parent::__construct($discord, $message);
    }

    public function execute(string $commandString, string $arguments = ''): void
    {
        $user = new User($this->message->user_id, $this->message->author->username);
        $this->GetIDStats($user);
    }

    public function GetIDStats(User $user): void
    {
        /** @var $message Message */
        $discordUserInfo = $this->message->author;
        $embed = new Embed($this->discord);
        $username = ucfirst($discordUserInfo->username);
        $avatarURL = $discordUserInfo->avatar;
        $cryptocreditsTotal = $user->getCryptocredits();
        $ramTotal = $user->getHackCount();
        $current = $user->getCurrent();
        $embed->setTitle("Cyber ID Card - $username");
        $embed->setColor('#ffb900');
        $embed->setThumbnail($avatarURL);
        $description = '';
        foreach ($user->getInventory() as $product) {
            $productName = ucfirst($product['product']['name']);
            $quantity =$product['quantity'];
            $currentIndex = $product['product']['current_index'];
            $description .= "**$productName** -- Aantal : $quantity | +$currentIndex V \n";
        }
        $embed->setDescription("
        Crypto One Bank saldo total : $cryptocreditsTotal
        Aantal keer een hack gepleegd : $ramTotal
        Hack stroom : {$current}/100 Volt
        
        Inventory :
        $description
        ");
        $this->message->reply(MessageBuilder::new()->addEmbed($embed));
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