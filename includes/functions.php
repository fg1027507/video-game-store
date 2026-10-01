<?php

// Escape text before displaying it on the page.
function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Read the CSV into an array so games.php can loop through it.
function read_games()
{
    $path = __DIR__ . '/../games/games.csv';
    if (!is_readable($path)) {
        return [];
    }
    $file = fopen($path, 'r');
    if ($file === false) {
        return [];
    }
    $games = [];
    while (($row = fgetcsv($file, 0, ',', '"', '')) !== false) {
        if (count($row) === 5) {
            $games[] = $row;
        }
    }
    fclose($file);
    return $games;
}
