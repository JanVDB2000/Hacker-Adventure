<?php

namespace Commands;

use Classes\User;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use RedBeanPHP\RedException\SQL;
use src\AbstractClasses\Command as AbstractCommand;
use src\Interfaces\Command;

class UseItemCommand extends AbstractCommand implements Command
{

    /** !useitem {item} */

    private $selectedProduct = null;
    public function __construct(?Discord $discord, ?Message $message)
    {
        parent::__construct($discord, $message);
    }

    /**
     * @throws SQL
     */
    public function execute(string $commandString, string $arguments = '')
    {
        $currentUser = new User($this->message->user_id, $this->message->author->username);

        $embed = new Embed($this->discord);
        $embed->setTitle("Inventory");
        $embed->setColor('#4f4f4f');

        $description = '';
        $buttons = [];
        $idCurrentUser = $this->message->user_id;

        foreach ($currentUser->getInventory() as $product) {
            $productName = ucfirst($product['product']['name']);
            $quantity =$product['quantity'];
            $currentIndex = $product['product']['current_index'];
            $description .= "**$productName** -- Aantal : $quantity |  +$currentIndex V \n";
        }

        foreach ($currentUser->getInventory() as $product) {
            $productName = ucfirst($product['product']['name']);
            $productLowercase = $product['product']['name'];

            $button = Button::new(Button::STYLE_SECONDARY)
                ->setLabel("Use $productName")
                ->setCustomId("use_button_$productLowercase");
            $button->setListener(function (Interaction $interaction) use ($productLowercase, $currentUser,$idCurrentUser){
                if ($interaction->user->id === $idCurrentUser){
                    $embedInfo = new Embed($this->discord);
                    $embedInfo->setColor('#58b9ff');
                    $successfulPurchase = $this->checkItemAndUse($productLowercase,$currentUser);
                    if ($successfulPurchase === true){
                        $name =  ucfirst($this->selectedProduct['name']);
                        $currentIndex = $this->selectedProduct['current_index'];
                        $messageContent = "U heeft **'$name'** gebruikt u Hack stroom is **+$currentIndex V**.";
                        $embedInfo->setDescription($messageContent);
                        $interaction->respondWithMessage(
                            MessageBuilder::new()
                                ->addEmbed($embedInfo)
                        );
                    }

                }

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
    private function checkItemAndUse(string $string, User $user): bool
    {
        foreach ($user->getInventory()as $item){
            $productIdInInventory = $item['product']['id'];
            $productNameInInventory = $item['product']['name'];
            $currentIndex = $item['product']['current_index'];
            $quantity = intval($item['quantity']);
            $price = $item['product']['price'];
            if (strcasecmp($string, $productNameInInventory) === 0) {
                if ($quantity > 0){
                    $this->selectedProduct = $item['product'];
                    $user->increaseCurrent($currentIndex);
                    $user->decreaseCryptocredits($price);
                    $user->removeProductFromInventory($productIdInInventory);
                    $user->save();
                    return true;
                }
                return false ;
            }
        }
        return false;
    }
}