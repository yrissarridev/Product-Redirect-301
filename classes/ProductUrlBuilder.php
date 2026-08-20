<?php
class WineRedirectsProductUrlBuilder
{
    public static function buildPath(array $product): string
    {
        $slug = $product['link_rewrite'];
        if (!empty($product['ean13'])) {
            $slug .= '-' . $product['ean13'];
        }
        $category = $product['cat_rewrite'] ?? '';
        return '/' . $category . '/' . $product['id'] . '-' . $slug . '.html';
    }

    public static function build(array $product, string $baseUrl): string
    {
        return rtrim($baseUrl, '/') . self::buildPath($product);
    }
}
