<?php
class WineRedirectsHtaccessParser
{
    public static function extractRedirectedProductIds(string $htaccessContent): array
    {
        if (!preg_match_all('/^Redirect 301 \/[^\/\s]+\/(\d+)-/m', $htaccessContent, $matches)) {
            return [];
        }
        return array_map('intval', $matches[1]);
    }
}
