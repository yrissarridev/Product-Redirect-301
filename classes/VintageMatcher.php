<?php
class ProductRedirectVintageMatcher
{
    const YEAR_PATTERN = '/\b(19[5-9]\d|20[0-3]\d)\b/';

    public static function baseKey(string $name): string
    {
        $stripped = preg_replace(self::YEAR_PATTERN, '', $name);
        $stripped = preg_replace('/\s{2,}/', ' ', $stripped);
        $stripped = trim($stripped, " \t\n\r\0\x0B-");
        return mb_strtolower($stripped);
    }

    public static function extractYear(string $name): ?int
    {
        if (preg_match(self::YEAR_PATTERN, $name, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public static function findPairs(array $products): array
    {
        $groups = [];
        foreach ($products as $product) {
            $key = self::baseKey($product['name']);
            $groups[$key][] = $product;
        }

        $bestByOldId = [];
        foreach ($groups as $group) {
            if (count($group) < 2) {
                continue;
            }
            $olds = array_filter($group, function ($p) {
                return !$p['active'] || $p['quantity'] <= 0;
            });
            $news = array_filter($group, function ($p) {
                return $p['active'] && $p['quantity'] > 0;
            });
            if (empty($olds) || empty($news)) {
                continue;
            }
            foreach ($olds as $old) {
                foreach ($news as $new) {
                    if ($old['id'] === $new['id']) {
                        continue;
                    }
                    $oldYear = self::extractYear($old['name']);
                    $newYear = self::extractYear($new['name']);
                    $needsReview = false;
                    if ($oldYear !== null && $newYear !== null) {
                        if ($newYear <= $oldYear) {
                            continue; // wrong direction, discard
                        }
                    } else {
                        $needsReview = true;
                    }

                    $candidate = ['old' => $old, 'new' => $new, 'needsReview' => $needsReview];
                    if (!isset($bestByOldId[$old['id']])) {
                        $bestByOldId[$old['id']] = $candidate;
                        continue;
                    }
                    $currentYear = self::extractYear($bestByOldId[$old['id']]['new']['name']) ?? -1;
                    $candidateYear = $newYear ?? -1;
                    if ($candidateYear > $currentYear) {
                        $bestByOldId[$old['id']] = $candidate;
                    }
                }
            }
        }

        return array_values($bestByOldId);
    }
}
