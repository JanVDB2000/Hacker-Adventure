<?php


namespace Classes;


use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class UserInventory
{
    /**
     * @throws SQL
     */
    public function addToUserInventory($userId, $productId, $quantity): void
    {
        $existingUserInventory = R::findOne('userinventory', 'user_id = ? AND product_id = ?', [$userId, $productId]);
        if ($existingUserInventory) {
            $existingUserInventory->quantity += $quantity;
            R::store($existingUserInventory);
        } else {
            $userInventory = R::dispense('userinventory');
            $userInventory->user_id = $userId;
            $userInventory->product_id = $productId;
            $userInventory->quantity = $quantity;
            R::store($userInventory);
        }
    }
}