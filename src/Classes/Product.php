<?php

namespace Classes;

use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class Product
{
    private string $name;
    private int $price;
    private int $currentIndex;

    public function __construct() {
        $this->name = '';
        $this->price = 0;
        $this->currentIndex = 0;
    }

    // Getter methods
    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCurrentIndex(): int
    {
        return $this->currentIndex;
    }

    /**
     * @throws SQL
     */
    public static function createProduct($name, $price,$currentIndex): array|\RedBeanPHP\OODBBean
    {
        $product = R::dispense('product');
        $product->name = $name;
        $product->price = $price;
        $product->currentIndex = $currentIndex;
        R::store($product);
        return $product;
    }
}