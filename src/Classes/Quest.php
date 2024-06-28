<?php

namespace Classes;

use RedBeanPHP\RedException\SQL;
use Traits\ConnectDatabaseTrait;

class Quest
{
    use ConnectDatabaseTrait;

    private ?int $id;
    public string $title = '';
    public string $description = '';
    public string $tablename = 'quest';


    /**
     * @throws SQL
     */
    public function __construct()
    {
       $this->id = $this->resolveFromDatabase();
    }


    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }
}
