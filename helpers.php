<?php

function getIdFromString(string $string): ?string
{
    $regex = '/@(\d+)>/';
    preg_match($regex, $string, $matches);
    return $matches[1] ?? null;
}