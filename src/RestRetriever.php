<?php
declare(strict_types=1);

namespace TXweb\Quintuc;

use function file_get_contents;
use function str_replace;
use function urlencode;

final class RestRetriever
{
    public function retrieve(string $url): string
    {
        ini_set( 'user_agent', 'Quintuc');
        $ret = file_get_contents($url);
        if ($ret === false)
        {
            throw new \RuntimeException("Cannot open file {$url}");
        }

        return $ret;
    }

    public static function urlencodeForRest(string $input): string
    {
        return str_replace('%2520', '%20', urlencode(str_replace(' ', '%20', $input)));
    }
}
