<?php

namespace Commands;

use Carbon\Carbon;
use Classes\User;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Classes\RandomEvent;
use RedBeanPHP\RedException\SQL;
use AbstractClasses\Command as AbstractCommand;
use Interfaces\Command;

class HackCommand extends AbstractCommand implements Command
{
    public function __construct(?Discord $discord = null, ?Message $message = null)
    {
        parent::__construct($discord, $message);
    }

    /**
     * @throws SQL
     */
    public function execute(string $commandString, string $arguments = ''): void
    {
        $user = new User($this->message->user_id, $this->message->author->username);
        $cryptocredits = $this->generateRandomCryptocredits($user);
        $medelijdeIndex = rand(1, 25);

        if ($user->getCryptocredits() > 0) {
            if (!$this->userCanHack()) {
               $this->waitForHack($user);
                return;
            }
            $this->updateUserData($user, $cryptocredits, $medelijdeIndex);
            $this->sendCryptocreditsReceivedMessage($user, $cryptocredits, $medelijdeIndex);
            $RandomEvent = new RandomEvent($this->message, $this->discord);
            $RandomEvent->getEventWithTrigger($user ,25);
            //$RandomEvent->getEventWithOutTrigger($user);
        } else {
            $this->initializeUserCryptocredits($user, 2500);
            $this->sendWelcomeMessage($user);
        }

    }

    private function generateRandomCryptocredits(User $user): int
    {
        $current = $user->getCurrent();
        $baseCryptocredits = ($current / 100) * rand(0, 2500);
        $roundedCryptocredits = round($baseCryptocredits);
        return (int)$roundedCryptocredits;

    }

    /**
     * @throws SQL
     */
    private function updateUserData(User $user, int $cryptocredits, int $currentIndex): void
    {
        $newCryptocredits = $user->getCryptocredits() + $cryptocredits;
        $user->setCryptocredits($newCryptocredits);
        $user->decreaseCurrent($currentIndex);
        $newCount = $user->getHackCount() + 1;
        $user->setHackCount($newCount);
        $user->setLastHack(Carbon::now()->timestamp);
        $user->save();
    }

    private function sendCryptocreditsReceivedMessage(User $user, int $cryptocredits, $currentIndex): void
    {

        $embedHA = new Embed($this->discord);
        $embedHA->setTitle("Terminal : Linux");
        $embedHA->setColor('#58b9ff');
        $embedHA->setDescription("U ontving " . $cryptocredits . " euro van Hacker Adventure van u hack \n **-$currentIndex Volt **");


        $embed = new Embed($this->discord);
        $embed->setTitle("Crypto One Bank : Update Saldo");
        $embed->setColor('#00ff16');
        $embed->setDescription('U totaal saldo bestaat uit ' . $user->getCryptocredits() . ' euro ');

        $this->message->reply(
            MessageBuilder::new()
                ->addEmbed($embedHA)
                ->addEmbed($embed)
        );
    }

    /**
     * @throws SQL
     */
    private function initializeUserCryptocredits(User $user, int $cryptocredits): void
    {
        $user->setCryptocredits($cryptocredits);
        $user->setName($this->message->author->username);
        $user->setHackCount(1);
        $user->save();
    }

    private function sendWelcomeMessage(User $user): void
    {
        $embedHA = new Embed($this->discord);
        $embedHA->setTitle("Bevestiging Inschrijving Hacker Adventure");
        $embedHA->setColor('#58b9ff');
        $embedHA->setDescription('
        Beste,
        
        Gefeliciteerd! Je hebt met succes ingeschreven voor de Hacker Adventure. Jouw inschrijvingsgegevens zijn volledig verwerkt, en je bent nu klaar om informatie en ondersteuning te ontvangen met betrekking tot jouw reis in de wereld van hacking en cybersecurity.
        
        We kijken ernaar uit om je te voorzien van de tools en begeleiding die nodig zijn om jouw vaardigheden naar nieuwe hoogten te brengen. De Hacker Adventure-gemeenschap staat klaar om je te ondersteunen bij het bereiken van je professionele doelen binnen dit spannende domein.
        
        Indien er aanvullende informatie of documentatie vereist is, aarzel dan niet om dit met ons te delen. We streven ernaar om je alle middelen te bieden die je nodig hebt voor een succesvolle Hacker Adventure.
        
        Dank je wel voor je toewijding en enthousiasme. We kijken ernaar uit om samen met jou te hacken en nieuwe horizonten te verkennen!
        
        
        Met vriendelijke groet,
        ');

        $embedSaldo = new Embed($this->discord);
        $embedSaldo->setTitle("Crypto One Bank : Update Saldo");
        $embedSaldo->setColor('#00ff16');
        $embedSaldo->setDescription('U heeft u eerste hack gedaan en u new totale saldo is nu ' . $user->getCryptocredits() . ' euro.');

        $this->message->reply(
            MessageBuilder::new()
                ->addEmbed($embedHA)
                ->addEmbed($embedSaldo)
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

    /**
     * @throws SQL
     */
    private function userCanHack(): bool
    {
        $user = new User($this->message->user_id, $this->message->author->username);
        return $user->canHack();
    }

    private function waitForHack(User $user): void
    {
        $embed = new Embed($this->discord);
        $embed->setTitle("Hacker Adventure : Error 500 \n");
        $embed->setColor('#58b9ff');
        $embed->setDescription("Helaas moeten we je informeren dat ons RAM (Random Access Memory) momenteel volledig benut is, en dit heeft invloed op de verwerking van de hack. Onze technische teams werken hard om dit probleem op te lossen en de benodigde ruimte vrij te maken.");

        $this->message->reply(
            MessageBuilder::new()
                ->addEmbed($embed)
        );
        $remainingTime = $user->getRemainingTimeForHack();
        $timeH =  $remainingTime['hours'];
        $timeM =  $remainingTime['minutes'];
        $timeS =  $remainingTime['seconds'];
        $this->message->reply("U moet nog ".$timeM."m:".$timeS."s wachten");
    }
}