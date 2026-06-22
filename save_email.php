<?php
/**
 * Newsletter-Anmeldung – speichert E-Mail-Adressen in email_list.md
 * Aufruf via POST von index.html (https://snote.fun/news/index.html)
 */
declare(strict_types=1);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$email_file = __DIR__ . '/email_list.md';

/* ---------- helpers ---------- */
function html_page(string $title, string $body): void {
    echo '<!DOCTYPE html><html lang="de"><head>'
       . '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>' . htmlspecialchars($title) . '</title>'
       . '<style>body{font-family:sans-serif;background:#F1F8E9;color:#1A1A1A;'
       . 'display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
       . '.box{background:#fff;border:1px solid #C8E6C9;border-radius:12px;padding:2rem 2.5rem;'
       . 'max-width:480px;text-align:center;box-shadow:0 4px 20px rgba(46,125,50,.13)}'
       . 'h2{color:#1B5E20;margin-bottom:.7rem} p{color:#546E7A;line-height:1.6;margin-bottom:1rem}'
       . 'a{display:inline-block;background:#2E7D32;color:#fff;padding:.55rem 1.4rem;'
       . 'border-radius:6px;text-decoration:none;font-weight:bold}'
       . 'a:hover{background:#1B5E20}</style></head>'
       . '<body><div class="box">' . $body . '</div></body></html>';
}

/* ---------- CSRF-Guard: nur POST ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    html_page('Fehler', '<h2>&#10060; Methode nicht erlaubt</h2>'
        . '<p>Dieses Skript akzeptiert nur POST-Anfragen.</p>'
        . '<a href="index.html">&#8592; Zurück</a>');
    exit;
}

/* ---------- E-Mail validieren ---------- */
$raw   = trim((string)($_POST['email'] ?? ''));
$email = filter_var($raw, FILTER_VALIDATE_EMAIL);

if (!$email || strlen($email) > 254) {
    http_response_code(400);
    html_page('Ungültige Eingabe', '<h2>&#10060; Ungültige E-Mail-Adresse</h2>'
        . '<p>Bitte geben Sie eine gültige E-Mail-Adresse ein.</p>'
        . '<a href="index.html">&#8592; Zurück</a>');
    exit;
}

/* ---------- Datei lesen ---------- */
$content = file_exists($email_file) ? file_get_contents($email_file) : '';

/* ---------- Duplikat-Prüfung ---------- */
if (stripos($content, $email) !== false) {
    html_page('Bereits angemeldet',
        '<h2>&#10003; Bereits angemeldet</h2>'
        . '<p><strong>' . htmlspecialchars($email) . '</strong> ist bereits in der Empfängerliste.</p>'
        . '<a href="index.html">&#8592; Zurück zu den Nachrichten</a>');
    exit;
}

/* ---------- E-Mail eintragen ---------- */
$date = date('Y-m-d');

if (strpos($content, '## Aktive Empfänger') !== false) {
    // Neue Zeile direkt nach dem Kommentar einfügen
    $insert_marker = "<!-- Neue Anmeldungen vom Formular auf index.html hier eintragen -->\n";
    $new_line      = $insert_marker . '- ' . $email . "\n";
    $content       = str_replace($insert_marker, $new_line, $content);
} else {
    $content .= "\n- " . $email . "\n";
}

// Datum aktualisieren
$content = preg_replace(
    '/\*Zuletzt aktualisiert: \d{4}-\d{2}-\d{2}\*/',
    '*Zuletzt aktualisiert: ' . $date . '*',
    $content
);

if (file_put_contents($email_file, $content, LOCK_EX) === false) {
    http_response_code(500);
    html_page('Serverfehler', '<h2>&#10060; Speicherfehler</h2>'
        . '<p>Die E-Mail-Adresse konnte nicht gespeichert werden. Bitte versuchen Sie es später erneut.</p>'
        . '<a href="index.html">&#8592; Zurück</a>');
    exit;
}

/* ---------- Erfolg ---------- */
header('Refresh: 4; url=index.html');
html_page('Angemeldet!',
    '<h2>&#10003; Erfolgreich angemeldet!</h2>'
    . '<p><strong>' . htmlspecialchars($email) . '</strong><br>'
    . 'Sie erhalten ab sofort täglich um <strong>7&nbsp;Uhr CEST</strong> den Newsletter '
    . 'mit den aktuellen Nachrichten aus Aschaffenburg, Lohr am Main und der Region.</p>'
    . '<p style="font-size:.82rem;color:#78909C">Abmeldung: Antwort auf die Newsletter-E-Mail mit dem Wort <em>abmelden</em></p>'
    . '<a href="index.html">&#8592; Zurück zu den Nachrichten</a>'
    . '<p style="font-size:.72rem;color:#B0BEC5;margin-top:1rem">Sie werden in 4 Sekunden weitergeleitet …</p>');
