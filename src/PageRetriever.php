<?php

declare(strict_types=1);

namespace TXweb\Quintuc;

use function file_exists;
use function file_put_contents;
use function json_decode;
use function sha1;

final class PageRetriever
{
    private const MAX_REQUESTS_PER_HOUR = 350;
    private const PAGE_DATA_URL_PREFIX = 'https://incubator.wikimedia.org/w/rest.php/v1/page/';

    private RestRetriever $restRetriever;

    /**
     * @var array<string, \DateTimeImmutable>
     */
    private array $wiktionaryUpdateDates = [];

    public function __construct()
    {
        $this->restRetriever = new RestRetriever();
    }

    /**
     * @param string[] $pages
     */
    public function retrievePages(array $pages): void
    {
        $numRetrieved = 0;
        foreach ($pages as $page)
        {
            if ($numRetrieved >= self::MAX_REQUESTS_PER_HOUR)
            {
                return;
            }

            $this->retrieve($page);
            $numRetrieved++;
        }
    }

    private function retrieve(string $page): void
    {
        if ($this->pageMustBeRetrieved($page))
        {
            $url = self::PAGE_DATA_URL_PREFIX . RestRetriever::urlencodeForRest($page);
            $pageDataRaw = $this->restRetriever->retrieve($url);
            $pageData = json_decode($pageDataRaw, true);
            file_put_contents($this->getFilename($page), $pageData['source']);
        }
        else
        {
            echo "Reading {$page} from cache...\n";
        }
    }

    private function pageMustBeRetrieved(string $page): bool
    {
        if ($page === '')
        {
            return false;
        }

        $filename = $this->getFilename($page);
        if (!file_exists($filename))
        {
            return true;
        }

        // check date!
        return false;
    }

    private function getFilename(string $name): string
    {
        return __DIR__ . '/../var/pages/' . sha1($name);
    }
}