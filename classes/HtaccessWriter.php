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

        self::write($htaccessPath, $newContent);

        return $addedIds;
    }

    /**
     * Quita del .htaccess las líneas dadas (los redirects "Obsoletos": su
     * producto de origen vuelve a estar activo y con stock). Solo borra líneas
     * enteras idénticas con el formato exacto de redirect de producto; nunca
     * toca otro texto aunque se lo pasen.
     *
     * @return int[] ids de producto cuya línea se quitó realmente
     */
    public static function removeLines(string $htaccessPath, array $lines): array
    {
        $content = (string) @file_get_contents($htaccessPath);
        if ($content === '') {
            throw new Exception('No se pudo leer el .htaccess (' . $htaccessPath . ')');
        }

        $toRemove = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^Redirect 301 \/[^\/\s]+\/(\d+)-\S+ https?:\/\/\S+$/', $line, $m)) {
                $toRemove[$line] = (int) $m[1];
            }
        }

        $kept = [];
        $removedIds = [];
        foreach (preg_split('/(?<=\n)/', $content) as $fileLine) {
            $key = trim($fileLine);
            if (isset($toRemove[$key])) {
                $removedIds[] = $toRemove[$key];
                continue;
            }
            $kept[] = $fileLine;
        }

        if (empty($removedIds)) {
            return [];
        }

        self::write($htaccessPath, implode('', $kept));

        return array_values(array_unique($removedIds));
    }

    private static function write(string $htaccessPath, string $newContent)
    {
        // El cron puede quitar y añadir en el mismo segundo: sin el sufijo, la
        // segunda copia pisaría a la primera (la del fichero original).
        $backupPath = $htaccessPath . '.bak-productredirect-' . date('Ymd-His');
        for ($n = 2; file_exists($backupPath); $n++) {
            $backupPath = $htaccessPath . '.bak-productredirect-' . date('Ymd-His') . '-' . $n;
        }
        if (!@copy($htaccessPath, $backupPath)) {
            throw new Exception('No se pudo hacer copia de seguridad antes de escribir, abortado por seguridad.');
        }

        if (@file_put_contents($htaccessPath, $newContent) === false) {
            throw new Exception('No se pudo escribir en el .htaccess (revisa permisos).');
        }
    }
}
