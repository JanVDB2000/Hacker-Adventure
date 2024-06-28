<?php

namespace Classes;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Embed\Embed;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class Store
{
    private string $store_name;
    public function __construct()
    {
        $this->store_name = 'ByteBazaar: Your Cyber Sanctuary';
    }

    public function getName(): string
    {
       return  $this->store_name;
    }

    public function getAllProductsInStore(): array
    {
        $shopInventoryItems= R::findAll('shopinventory');
        $resultArray = [];
        foreach ($shopInventoryItems as $item) {
            $product = R::load('product', $item['product_id']);
            $resultArray[] = [
                'id' => $product->id,
                'name' => $product->name,
                'current_index' => $product->current_index,
                'price' => $product->price,
                'quantity' =>  $item['quantity']
            ];
        }

        return $resultArray;
    }

    /**
     * @throws SQL
     */
    public function removeFromStoreInventory(int $productId,$price, int $quantity , $discord , $message, $userId): bool
    {
        $shopInventory = R::findOne('shopinventory', 'product_id = ?', [$productId]);

        if ($shopInventory->quantity === 0) {

            $embed = new Embed($discord);
            $embed->setTitle("Out of Stock");
            $embed->setColor('#FF0000');
            $embed->setDescription("Die graaiers zijn met al de good stuff van door OP = OP");

            $message->reply(MessageBuilder::new()->addEmbed($embed));
            return false;
        }

        $shopInventory->quantity -= $quantity;
        if ($shopInventory->quantity < 0) {
            $shopInventory->quantity = 0;
        }
        R::store($shopInventory);

        $userDb = R::findOne('user', 'discord_id = ?', [$userId]);

        $user = new User($userId, '');
        $user->decreaseCryptocredits($price);
        $user->save();
        $userInventory = new UserInventory();
        $userInventory->addToUserInventory($userDb->id, $productId, $quantity);
        return true;
    }

    /**
     * @throws SQL
     */
    private  function addToShopInventory($productId, $quantity): array|\RedBeanPHP\OODBBean
    {
        $shopInventory = R::dispense('shopinventory');
        $shopInventory->product_id = $productId;
        $shopInventory->quantity = $quantity;
        R::store($shopInventory);
        return $shopInventory;
    }


}