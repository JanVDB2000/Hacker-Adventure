<?php

use Commands\HackCommand;
use Commands\LeaderBoardCommand;
use Commands\MyIdCardCommand;
use Commands\StoreCommand;
use Commands\UseItemCommand;

return [
    'hack' => HackCommand::class,
    'shop' => StoreCommand::class,
    'use' => UseItemCommand::class,
    'id' => MyIdCardCommand::class,
    'leaderboard' => LeaderBoardCommand::class,
];