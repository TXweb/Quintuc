#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use TXweb\Quintuc\LanguageCounter;

$raw = file_get_contents(__DIR__ . '/../var/index');
$pages = explode(PHP_EOL, $raw);

$counter = new LanguageCounter();
foreach ($pages as $page)
{
    $filename = __DIR__ . '/../var/pages/' . sha1($page);
    if (!file_exists($filename))
        continue;

    $contents = file_get_contents($filename);
    $counter->count($page, $contents);
}

$counter->saveToCache();
