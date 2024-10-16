<?php
declare(strict_types=1);

namespace TXweb\Quintuc;

use function array_key_exists;
use function explode;
use function file_put_contents;
use function preg_match;
use function serialize;
use function str_contains;
use function strtr;
use function trim;

final class LanguageCounter
{
    private const FILENAME = __DIR__ . '/../var/cache';

    private const LANGUAGE_CODE_TRANSLATIONS = [
        'Wt/zea/af' => 'Afrikaons',
        'Wt/zea/brc' => 'Berbice-Nederlands',
        'Wt/zea/de' => 'Duuts',
        'Wt/zea/dum' => 'Middelnederlands',
        'Wt/zea/en' => 'Iengels',
        'Wt/zea/fr' => 'Frans',
        'Wt/zea/fy' => 'Westerlauwers Vries',
        'Wt/zea/is' => 'Ieslands',
        'Wt/zea/li' => 'Limburgs',
        'Wt/zea/nds' => 'Nedersaksisch/Platduuts',
        'Wt/zea/nl' => 'Nederlands',
        'Wt/zea/nl-BR' => 'Brabants',
        'Wt/zea/nl-KL' => 'Kleverlands',
        'Wt/zea/nl-HL' => '’Ollands',
        'Wt/zea/nl-OV' => 'Oôst-Vlaems',
        'Wt/zea/odt' => 'Oudnederlands',
        'Wt/zea/skw' => 'Skepi',
        'Wt/zea/sv' => 'Zweeds',
        'Wt/zea/vls' => 'West-Vlaems',
        'Wt/zea/xxx' => 'Nie-taelgebonde',
        'Wt/zea/zea' => 'Zeêuws',
    ];

    /** @var array<string, string[]> */
    public array $table = [];

    public function count(string $title, string $pageSource): void
    {
        $lines = explode("\n", $pageSource);
        foreach ($lines as $line)
        {
            $matches = [];
            //echo $line . '<br>';
            $line = trim($line);
            if (str_contains($line, '==='))
            {
                continue;
            }
            elseif (str_contains($line, '=={{'))
            {
                $code = strtr($line, ['=={{' => '', '}}==' => '']);
                $this->addToTable($code, $title);
            }
            elseif (preg_match('/^==([^=]+)==$/', $line, $matches))
            {
                $code = $matches[1];
                $this->addToTable($code, $title);
            }
        }
    }

    private function addToTable(string $code, string $title): void
    {
        if (str_contains($code, '[['))
        {
            $code = strtr($code, ['[[' => '', ']]' => '']);
            if (str_contains($code, '|'))
            {
                $code = explode('|', $code)[1];
            }
        }

        $translatedCode = self::LANGUAGE_CODE_TRANSLATIONS[$code] ?? $code;

        if (!array_key_exists($translatedCode, $this->table))
        {
            $this->table[$translatedCode] = [];
        }

        $this->table[$translatedCode][] = $title;
    }

    public function saveToCache(): void
    {
        @file_put_contents(self::FILENAME, serialize($this->table));
    }
}
