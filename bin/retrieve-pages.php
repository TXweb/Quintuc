#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use TXweb\Quintuc\PageRetriever;

$raw = file_get_contents(__DIR__ . '/../var/index');
$pages = explode(PHP_EOL, $raw);

$retriever = new PageRetriever();
$retriever->retrievePages($pages);