<?php
class ProductRedirectHtaccessParser
{
    public static function extractRedirectEntries(string $htaccessContent): array
    {
        if (!preg_match_all('/^(Redirect 301 \/[^\/\s]+\/(\d+)-[^\r\n]*)$/m', $htaccessContent, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $entries = [];
        foreach ($matches as $match) {
            $entries[] = [
                'id' => (int) $match[2],
                'line' => trim($match[1]),
            ];
        }

        return $entries;
    }

    public static function extractRedirectedProductIds(string $htaccessContent): array
    {
        return array_column(self::extractRedirectEntries($htaccessContent), 'id');
    }
}
