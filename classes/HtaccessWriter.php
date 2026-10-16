<?php
class ProductRedirectHtaccessWriter
{
    const MARKER = '#301 modulo';

    /**
     * Inserta las líneas dadas justo después del marcador "#301 modulo"
     * (lo crea si no existe todavía, justo después de "RewriteEngine on").
     * Ignora cualquier línea que no tenga el formato exacto de redirect de
     * producto, y cualquiera cuyo id de producto ya esté presente en el
     * fichero — nunca duplica ni escribe texto arbitrario.
     *
     * @return int[] ids de producto cuya línea se añadió realmente (el "antiguo"
     *               de cada redirect, el que hay que desactivar en PrestaShop)
     */
    public static function appendLines(string $htaccessPath, array $lines): array
    {
        $content = (string) @file_get_contents($htaccessPath);
        if ($content === '') {
            throw new Exception('No se pudo leer el .htaccess (' . $htaccessPath . ')');
        }

        $existingIds = ProductRedirectHtaccessParser::extractRedirectedProductIds($content);

        $toAdd = [];
        $addedIds = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (!preg_match('/^Redirect 301 \/[^\/\s]+\/(\d+)-\S+ https?:\/\/\S+$/', $line, $m)) {
                continue;
            }
            $id = (int) $m[1];
            if (in_array($id, $existingIds, true)) {
                continue;
            }
            $toAdd[] = $line;
            $addedIds[] = $id;
            $existingIds[] = $id;
        }

        if (empty($toAdd)) {
            return [];
        }

        $block = self::MARKER . "\n" . implode("\n", $toAdd);

        if (strpos($content, self::MARKER) !== false) {
            $newContent = preg_replace('/' . preg_quote(self::MARKER, '/') . '/', $block, $content, 1);
        } elseif (strpos($content, 'RewriteEngine on') !== false) {
            $newContent = preg_replace('/RewriteEngine on/', "RewriteEngine on\n\n" . $block, $content, 1);
        } else {
            $newContent = $block . "\n\n" . $content;
        }

        $backupPath = $htaccessPath . '.bak-productredirect-' . date('Ymd-His');
        if (!@copy($htaccessPath, $backupPath)) {
            throw new Exception('No se pudo hacer copia de seguridad antes de escribir, abortado por seguridad.');
        }

        if (@file_put_contents($htaccessPath, $newContent) === false) {
            throw new Exception('No se pudo escribir en el .htaccess (revisa permisos).');
        }

        return $addedIds;
    }
}
