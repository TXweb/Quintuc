<?php
const CACHE_FILENAME = __DIR__ . '/../var/cache';
const LEMMA_URL_PREFIX = 'https://incubator.wikimedia.org/wiki/';

$entriesPerLanguage = unserialize(file_get_contents(CACHE_FILENAME));
$creationDate = date('Y-m-d H:i:s', filemtime(CACHE_FILENAME));
echo "<p>Aanmaakdatum: {$creationDate}</p>";

$totals = [];

foreach ($entriesPerLanguage as $language => $pages)
{
echo "<h2>{$language}</h2>";
echo '<ol>';

    foreach ($pages as $page)
    {
    $title = str_replace('Wt/zea/', '', $page);
    echo '<li><a href="' . LEMMA_URL_PREFIX . $page . '">' . $title . '</a></li>';
    }

    echo '</ol>';

$totals[$language] = count($pages);
}

arsort($totals);

echo '<h2>Totalen</h2><table><tbody>';

    $totalTotal = 0;
    foreach ($totals as $code => $count)
    {
    echo "<tr><td>{$code}</td><td>{$count}</td></tr>";
    $totalTotal += $count;
    }

    echo "<tr><td><b>Totaal</td><td>{$totalTotal}</td></tr>";

    echo '</tbody></table>';