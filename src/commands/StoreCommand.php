<?php

namespace Commands;

use Classes\Store;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use RedBeanPHP\RedException;
use RedBeanPHP\RedException\SQL;
use src\AbstractClasses\Command as AbstractCommand;
use src\Interfaces\Command;

class StoreCommand extends AbstractCommand implements Command
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
        $this->loadStore();
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

    private function loadStore(): void
    {
        $store = new Store();
        $name = "ByteBazaar: Your Cyber Sanctuary";
        $products = $store->getAllProductsInStore();

        $embed = new Embed($this->discord);
        $embed->setTitle($name);
        $embed->setColor('#58b9ff');

        $description = '';
        $buttons = [];

        foreach ($products as $product) {
            $productName = ucfirst($product['name']);
            $price = $product['price'];
            $productId = $product['id'];
            $quantity = $product['quantity'];
            $currentIndex = $product['current_index'];

            $description .= "**$productName**\nPrijs: $price\n Hack Stroom +{$currentIndex} V\n Stock: $quantity\n \n";

            $button = Button::new(Button::STYLE_SECONDARY)
                ->setLabel("Buy $productName")
                ->setCustomId("buy_button_$productName");

            // Add a listener to the button
            $button->setListener(function (Interaction $interaction) use ($productId, $price, $quantity, $productName) {

                $embedInfo = new Embed($this->discord);
                $embedInfo->setTitle("ByteBazaar : Kassa");
                $embedInfo->setColor('#58b9ff');

                $successfulPurchase = $this->processPurchase($productId, $price, $interaction->user->id);
                $username = ucfirst($interaction->user->username);
                $messageContent = $successfulPurchase
                    ? "**$username** heeft **$productName** gekocht voor **$price Euro** van ByteBazaar."
                    : "Sorry, de aankoop van **$productName** is mislukt.";
                $embedInfo->setDescription($messageContent);
                $interaction->respondWithMessage(
                    MessageBuilder::new()
                        ->addEmbed($embedInfo)
                );
            }, $this->discord);

            $buttons[] = $button;
        }

        $embed->setDescription($description);

        $actionRows = [];

        foreach (array_chunk($buttons, 5) as $buttonSet) {
            $actionRow = ActionRow::new();
            foreach ($buttonSet as $button) {
                $actionRow->addComponent($button);
            }
            $actionRows[] = $actionRow;
        }
        $messageBuilder = MessageBuilder::new()->addEmbed($embed);
        foreach ($actionRows as $actionRow) {
            $messageBuilder->addComponent($actionRow);
        }

        $this->message->reply($messageBuilder);
    }

    /**
     * @throws SQL
     * @throws RedException
     */
    private function processPurchase(int $productId, mixed $price, $userId): bool
    {
        $storeInventory = new Store();
        return $storeInventory->removeFromStoreInventory($productId,$price, 1, $this->discord , $this->message ,$userId);
    }
}