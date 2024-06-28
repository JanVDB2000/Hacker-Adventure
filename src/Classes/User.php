<?php

namespace Classes;

use Carbon\Carbon;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;
use Discord\Parts\Channel\Message;

class User
{
    private $discordId;
    public ?string $userName;
    private int $cryptocredits = 0;
    private int $hackCount = 0;
    private int $current = 0;
    private ?string $lastHack = '';


    /**
     * @throws SQL
     */
    public function __construct($id, $name)
    {
        $this->resolveFromDatabase($id, $name);
    }

    /**
     * @throws SQL
     */
    public function save(): void
    {
        $userDb = R::findOne('user', 'discord_id = ?', [$this->discordId]);

        if (!$userDb) {
            // User doesn't exist, create a new user
            $userDb = R::dispense('user');
            $userDb->discord_id = $this->discordId;
        }

        $userDb->user_name = $this->userName;
        $userDb->current = $this->current;
        $userDb->cryptocredits = $this->cryptocredits;
        $userDb->hackCount = $this->hackCount;
        $userDb->last_hack = $this->lastHack;

        R::store($userDb);
    }

    public function getCurrent(): int
    {
        return $this->current;
    }

    private function setCurrent($current): void
    {
        // Ensure the volt stat is within the valid range (1-100)
        $this->current = max(1, min(100, $current));
    }

    public function increaseCurrent($amount): void
    {
        $this->setCurrent($this->current + $amount);
    }

    public function decreaseCurrent($amount): void
    {
        $this->setCurrent($this->current - $amount);
    }

    public function getCryptocredits(): int
    {
        return $this->cryptocredits;
    }

    public function increaseCryptocredits(int $amount): void
    {
        $this->setCryptocredits($this->cryptocredits + $amount);
    }

    public function decreaseCryptocredits(int $amount): void
    {
        $this->setCryptocredits($this->cryptocredits - $amount);
    }

    public function setCryptocredits($cryptocredits): void
    {
        $this->cryptocredits = $cryptocredits;
    }

    public function setHackCount($hackCount): void
    {
        $this->hackCount = $hackCount;
    }

    public function getHackCount(): int
    {
        return $this->hackCount;
    }

    public function setName(string $name): void
    {
        $this->userName = $name;
    }

    public function getName(): string
    {
        return $this->userName;
    }

    /**
     * @throws SQL
     */
    private function resolveFromDatabase($id, $name): void
    {
        $userDb = R::findOne('user', 'discord_id = ?', [$id]);

        if ($userDb) {
            $this->discordId = $userDb->discord_id;
            $this->userName = $userDb->user_name;
            $this->cryptocredits = $userDb->cryptocredits;
            $this->hackCount = $userDb->hackCount;
            $this->current = $userDb->current ?? rand(65, 100);
            $this->lastHack = $userDb->last_hack;
        } else {
            $this->discordId = $id;
            $this->userName = $name;

            $this->save();
        }
    }

    public function canHack(): bool
    {
        return $this->lastHack < Carbon::now()->subMinutes(5)->timestamp;
    }

    public function setLastHack($string): void
    {
        $this->lastHack = $string;
    }

    public function getRemainingTimeForHack(): array
    {
        $now = Carbon::now();
        $expirationTime = Carbon::createFromTimestamp($this->lastHack)->addMinutes(5);

        $diff = $now->diff($expirationTime);
        $remainingHours = $diff->h;
        $remainingMinutes = $diff->i;
        $remainingSeconds = $diff->s;

        return [
            'hours' => $remainingHours,
            'minutes' => $remainingMinutes,
            'seconds' => $remainingSeconds,
        ];
    }

    public function getInventory(): array
    {
        $userDb = R::findOne('user', 'discord_id = ?', [$this->discordId]);
        if (!$userDb->getID()) {
            return [];
        }
        $inventoryItems = R::findAll('userinventory',' user_id = ?', [$userDb->getID()]);
        $resultArray = [];
        foreach ($inventoryItems as $item) {
            $product = R::load('product', $item["product_id"]);
            $resultArray[] = [
                'quantity' => $item["quantity"],
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'current_index' => $product->current_index,
                ],
            ];
        }
        return $resultArray;
    }

    /**
     * @throws SQL
     */
    public function removeProductFromInventory($productId): bool|int
    {
        $userDb = R::findOne('user', 'discord_id = ?', [$this->discordId]);

        if (!$userDb->id) {
            return false;
        }
        $userInventoryDb = R::findOne('userinventory', 'product_id = ? AND user_id  = ?', [$productId,$userDb->id]);

        if ($userInventoryDb) {
            $newQuantity = $userInventoryDb->quantity - 1;
            $userInventoryDb->quantity = $newQuantity;
            if ($newQuantity === 0) {
                R::trash($userInventoryDb);
            } else {
                R::store($userInventoryDb);
            }
        } else {
            return false;
        }
        return true;
    }
}