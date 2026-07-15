<?php
/**
 * subscribe.php – Newsletter-Anmeldung für die Tagesnachrichten
 * Lohr · Aschaffenburg · Main-Spessart.
 *
 * Nimmt per POST das Feld "email" entgegen, validiert es und hängt die
 * Adresse – eine pro Zeile im Format "email@domain.de | 2026-07-15 06:20 CEST" –
 * an email_list.md im selben Verzeichnis an (nur anhängen, keine Duplikate).
 * Danach Redirect zurück auf index.html mit Erfolgsmeldung.
 */
declare(strict_types=1);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$email_file = __DIR__ . '/email_list.md';

/* ---------- kleine HTML-Ausgabe-Hilfe ---------- */
function html_page(string $title, string $body): void {
    echo '<!DOCTYPE html><html lang="de"><head>'
       . '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>' . htmlspecialchars($title) . '</title>'
       . '<style>body{font-family:Verdana,Geneva,sans-serif;background:#FFF6EF;color:#2A1A12;'
       . 'display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
       . '.box{background:#fff;border:1px solid #F6C9A8;border-radius:16px;padding:2rem 2.5rem;'
       . 'max-width:480px;text-align:center;box-shadow:0 8px 30px rgba(232,69,43,.15)}'
       . 'h2{color:#C63A22;margin-bottom:.7rem} p{color:#5A4034;line-height:1.6;margin-bottom:1rem}'
       . 'a{display:inline-block;background:linear-gradient(100deg,#E8452B,#F2762D);color:#fff;'
       . 'padding:.6rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:bold}'
       . 'a:hover{filter:brightness(1.08)}</style></head>'
       . '<body><div class="box">' . $body . '</div></body></html>';
}

/* ---------- nur POST erlauben ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    html_page('Fehler', '<h2>Methode nicht erlaubt</h2>'
        . '<p>Dieses Skript akzeptiert nur POST-Anfragen.</p>'
        . '<a href="index.html">&#8592; Zur&uuml;ck</a>');
    exit;
}

/* ---------- E-Mail validieren ---------- */
$raw   = trim((string)($_POST['email'] ?? ''));
$email = filter_var($raw, FILTER_VALIDATE_EMAIL);

if (!$email || strlen($email) > 254) {
    http_response_code(400);
    html_page('Ung&uuml;ltige Eingabe', '<h2>Ung&uuml;ltige E-Mail-Adresse</h2>'
        . '<p>Bitte geben Sie eine g&uuml;ltige E-Mail-Adresse ein.</p>'
        . '<a href="index.html">&#8592; Zur&uuml;ck</a>');
    exit;
}

/* ---------- Datei lesen & Duplikatpr&uuml;fung ---------- */
$content = file_exists($email_file) ? (string)file_get_contents($email_file) : '';

if ($content !== '' && stripos($content, $email) !== false) {
    html_page('Bereits angemeldet',
        '<h2>Bereits angemeldet</h2>'
        . '<p><strong>' . htmlspecialchars($email) . '</strong> ist bereits in der Empf&auml;ngerliste.</p>'
        . '<a href="index.html">&#8592; Zur&uuml;ck zu den Nachrichten</a>');
    exit;
}

/* ---------- Adresse anh&auml;ngen ---------- */
$stamp = (new DateTime('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i');
$line  = $email . ' | ' . $stamp . ' CEST' . "\n";

/* Sicherstellen, dass ein Zeilenumbruch vor dem neuen Eintrag steht */
if ($content !== '' && substr($content, -1) !== "\n") {
    $line = "\n" . $line;
}

if (file_put_contents($email_file, $line, FILE_APPEND | LOCK_EX) === false) {
    http_response_code(500);
    html_page('Serverfehler', '<h2>Speicherfehler</h2>'
        . '<p>Die E-Mail-Adresse konnte nicht gespeichert werden. Bitte versuchen Sie es sp&auml;ter erneut.</p>'
        . '<a href="index.html">&#8592; Zur&uuml;ck</a>');
    exit;
}

/* ---------- Erfolg ---------- */
header('Refresh: 4; url=index.html');
html_page('Angemeldet!',
    '<h2>Erfolgreich angemeldet!</h2>'
    . '<p><strong>' . htmlspecialchars($email) . '</strong><br>'
    . 'Sie erhalten ab sofort t&auml;glich die aktuellen Nachrichten aus Lohr am Main, '
    . 'Aschaffenburg und dem Main-Spessart-Kreis.</p>'
    . '<p style="font-size:.82rem;color:#8A6A58">Abmeldung jederzeit per Antwort auf den Newsletter mit dem Wort <em>abmelden</em>.</p>'
    . '<a href="index.html">&#8592; Zur&uuml;ck zu den Nachrichten</a>'
    . '<p style="font-size:.72rem;color:#B79A88;margin-top:1rem">Sie werden in 4 Sekunden weitergeleitet &hellip;</p>');
