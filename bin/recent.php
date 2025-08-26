<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$url = 'https://incubator.wikimedia.org/wiki/Special:RecentChanges?hidebots=1&translations=filter&hidecategorization=1&hideWikibase=1&limit=500&days=365&urlversion=2&rc-testwiki-project=t&rc-testwiki-code=zea';

ini_set( 'user_agent', 'Quintuc');
$ret = file_get_contents($url);

preg_match_all('/title="Wt\/zea\/.+?"/', $ret, $matches);
$filtered = array_unique($matches);
$trimmed = array_map(function (string $input)
{
    return substr($input, 7, -1);
}, $filtered);

