<?php
declare(strict_types=1);

define('LOG_FILE', __DIR__ . '/download_nachrichten.log');
define('TARGET_FILE', __DIR__ . '/index.html');
define('SOURCE_URL', 'https://raw.githubusercontent.com/domezos2024/aktuelles/main/Nachrichten.html');
define('REQUEST_TIMEOUT', 15);

function log_msg(string $level, string $message): void {
    $line = sprintf('[%s] [%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), strtoupper($level), $message);
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function delete_existing(string $path): void {
    if (!file_exists($path)) {
        log_msg('info', 'Keine bestehende Datei gefunden, Loeschung übersprungen: ' . $path);
        return;
    }
    if (unlink($path)) {
        log_msg('info', 'Bestehende Datei geloescht: ' . $path);
    } else {
        log_msg('error', 'Loeschen fehlgeschlagen: ' . $path);
    }
}

function download_file(string $url, string $destination): bool {
    log_msg('info', 'Starte Download: ' . $url);

    $context = stream_context_create([
        'http' => [
            'method'          => 'GET',
            'timeout'         => REQUEST_TIMEOUT,
            'follow_location' => 1,
            'user_agent'      => 'PHP-GitHub-Downloader/1.0',
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $content = @file_get_contents($url, false, $context);

    if ($content === false) {
        log_msg('error', 'Download fehlgeschlagen. URL nicht erreichbar: ' . $url);
        return false;
    }

    $bytes = strlen($content);
    log_msg('info', sprintf('Download erfolgreich. Bytes empfangen: %d', $bytes));

    $result = file_put_contents($destination, $content, LOCK_EX);

    if ($result === false) {
        log_msg('error', 'Schreiben fehlgeschlagen. Ziel: ' . $destination);
        return false;
    }

    log_msg('info', sprintf('Datei gespeichert: %s (%d Bytes geschrieben)', $destination, $result));
    return true;
}

function main(): void {
    log_msg('info', '=== Script gestartet ===');
    log_msg('info', 'Ziel-Verzeichnis: ' . __DIR__);

    if (!is_writable(__DIR__)) {
        log_msg('error', 'Verzeichnis nicht beschreibbar: ' . __DIR__);
        http_response_code(500);
        echo 'FEHLER: Verzeichnis nicht beschreibbar.' . PHP_EOL;
        return;
    }

    delete_existing(TARGET_FILE);

    $success = download_file(SOURCE_URL, TARGET_FILE);

    if ($success) {
        log_msg('info', 'Vorgang abgeschlossen. Status: OK');
        http_response_code(200);
        echo 'OK: Nachrichten.html erfolgreich heruntergeladen.' . PHP_EOL;
    } else {
        log_msg('error', 'Vorgang fehlgeschlagen. Status: FEHLER');
        http_response_code(500);
        echo 'FEHLER: Download nicht erfolgreich. Siehe Log.' . PHP_EOL;
    }

    log_msg('info', '=== Script beendet ===');
}

main();
