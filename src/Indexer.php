<?php
declare(strict_types=1);

namespace TXweb\Quintuc;

use RuntimeException;
use function array_key_exists;
use function fclose;
use function file_get_contents;
use function fopen;
use function fwrite;
use function in_array;
use function json_decode;
use function sort;
use function str_contains;
use function str_starts_with;

final class Indexer
{
    const PAGES_OVERVIEW_URL = 'https://incubator.wikimedia.org/w/api.php?action=query&list=categorymembers&cmtitle=Category:Wt%2Fzea&format=json&cmlimit=500';
    const UPDATE_DATES_URL = 'https://incubator.wikimedia.org/w/api.php?action=query&list=categorymembers&cmtitle=Category:Wt%2Fzea&format=json&cmlimit=500';

    private const PAGES_TO_EXCLUDE = [
        'Wt/zea/Vòblad', // Main page
    ];

    private const NAMESPACES_TO_EXCLUDE = [
        'Wiktionary',
        '\'Ulpe',
    ];
    private RestRetriever $restRetriever;

    public function __construct()
    {
        $this->restRetriever = new RestRetriever();
    }

    public function createIndex(): void
    {
        $pagesToCheck = [];
        $continue = '';

        while (true)
        {
            $url = self::PAGES_OVERVIEW_URL;
            if ($continue)
            {
                $url .= '&cmcontinue=' . RestRetriever::urlencodeForRest($continue);
            }

            $this->restRetriever->retrieve($url);
            $resultRaw = file_get_contents($url);
            if ($resultRaw === false)
            {
                throw new RuntimeException("Could not retrieve from {$url}!");
            }
            $result = json_decode($resultRaw, true, flags: JSON_THROW_ON_ERROR);

            foreach ($result['query']['categorymembers'] as $page)
            {
                $pageId = $page['pageid'];
                $title = $page['title'];
                if (!$this->pageMustBeExcluded($title))
                {
                    $pagesToCheck[$pageId] = $title;
                }
            }

            if (!array_key_exists('continue', $result))
            {
                break;
            }

            $continue = $result['continue']['cmcontinue'];
        }

        sort($pagesToCheck);

        $filename = __DIR__ . '/../var/index';
        $handle = fopen($filename, 'wb');
        if ($handle === false)
        {
            throw new RuntimeException("Could not open {$filename}!");
        }
        foreach ($pagesToCheck as $page)
        {
            fwrite($handle, $page . PHP_EOL);
        }
        fclose($handle);
    }

    private function pageMustBeExcluded(string $title): bool
    {
        if (!str_starts_with($title, 'Wt/zea'))
        {
            return true;
        }

        foreach (self::NAMESPACES_TO_EXCLUDE as $namespace)
        {
            if (str_contains($title, "{$namespace}:"))
            {
                return true;
            }
        }

        if (in_array($title, self::PAGES_TO_EXCLUDE, true))
        {
            return true;
        }

        return false;
    }
}
