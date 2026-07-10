<?php
/**
 * visit_counter.php – zählt Seitenaufrufe von index.html pro Kalendertag.
 * Aufruf über ein unsichtbares <img>-Tag in index.html (kein JavaScript nötig).
 * Speichert die Zählung in besucher.json im selben Verzeichnis; für jeden
 * neuen Tag wird automatisch ein neuer Eintrag bei 0 begonnen.
 */
declare(strict_types=1);

header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$json_file = __DIR__ . '/besucher.json';
$today     = date('d.m.Y');

$fp = @fopen($json_file, 'c+');
if ($fp !== false && flock($fp, LOCK_EX)) {
    $size    = filesize($json_file);
    $content = $size > 0 ? fread($fp, $size) : '[]';
    $data    = json_decode((string)$content, true);
    if (!is_array($data)) {
        $data = [];
    }

    $found = false;
    foreach ($data as &$entry) {
        if (($entry['Datum'] ?? null) === $today) {
            $entry['Besucher'] = (int)($entry['Besucher'] ?? 0) + 1;
            $found = true;
            break;
        }
    }
    unset($entry);

    if (!$found) {
        $data[] = ['Datum' => $today, 'Besucher' => 1];
    }

    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
}

// Transparentes 1x1-Pixel-GIF zurückgeben
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
