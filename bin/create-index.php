#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use TXweb\Quintuc\Indexer;

$indexer = new Indexer();
$indexer->createIndex();