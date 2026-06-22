<?php
/**
 * download_nachrichten.php
 *
 * Aufgaben:
 *  1. Nachrichten.html von GitHub holen → als index.html lokal speichern
 *  2. E-Mail-Adressen aus email_list.md auslesen
 *  3. Professionellen Newsletter per Gmail SMTP an alle Empfänger versenden
 *
 * Benötigte Server-Umgebungsvariablen:
 *   GMAIL_USER         = michaelbergfeld1982@gmail.com
 *   GMAIL_APP_PASSWORD = [16-stelliges Gmail App-Passwort]
 */
declare(strict_types=1);

/* ═══════════════════════════════════════════════════
   KONFIGURATION
═══════════════════════════════════════════════════ */
define('LOG_FILE',        __DIR__ . '/download_nachrichten.log');
define('TARGET_FILE',     __DIR__ . '/index.html');
define('EMAIL_LIST_FILE', __DIR__ . '/email_list.md');
define('SOURCE_URL',      'https://raw.githubusercontent.com/domezos2024/aktuelles/main/Nachrichten.html');
define('NEWS_URL',        'https://snote.fun/news');
define('SMTP_HOST',       'smtp.gmail.com');
define('SMTP_PORT',       465);
define('REQUEST_TIMEOUT', 20);

/* ═══════════════════════════════════════════════════
   LOGGING
═══════════════════════════════════════════════════ */
function log_msg(string $level, string $message): void {
    $line = sprintf('[%s] [%s] %s' . PHP_EOL,
        date('Y-m-d H:i:s'), strtoupper($level), $message);
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
    echo $line;
}

/* ═══════════════════════════════════════════════════
   SCHRITT 1 – HTML-Download & index.html speichern
═══════════════════════════════════════════════════ */
function download_nachrichten(): bool {
    log_msg('info', 'Starte Download: ' . SOURCE_URL);

    $context = stream_context_create([
        'http' => [
            'method'          => 'GET',
            'timeout'         => REQUEST_TIMEOUT,
            'follow_location' => 1,
            'user_agent'      => 'DoMeZos-Nachrichten-Bot/2.0',
        ],
        'ssl'  => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $content = @file_get_contents(SOURCE_URL, false, $context);
    if ($content === false) {
        log_msg('error', 'Download fehlgeschlagen – URL nicht erreichbar: ' . SOURCE_URL);
        return false;
    }

    if (file_exists(TARGET_FILE)) {
        @unlink(TARGET_FILE);
    }

    $written = file_put_contents(TARGET_FILE, $content, LOCK_EX);
    if ($written === false) {
        log_msg('error', 'Schreiben fehlgeschlagen: ' . TARGET_FILE);
        return false;
    }

    log_msg('info', sprintf('index.html gespeichert (%d Bytes)', $written));
    return true;
}

/* ═══════════════════════════════════════════════════
   SCHRITT 2 – E-Mail-Adressen aus email_list.md lesen
═══════════════════════════════════════════════════ */
function load_recipients(): array {
    if (!file_exists(EMAIL_LIST_FILE)) {
        log_msg('warn', 'email_list.md nicht gefunden: ' . EMAIL_LIST_FILE);
        return [];
    }

    $emails = [];
    foreach (file(EMAIL_LIST_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        // Format in email_list.md: "- adresse@domain.de"
        if (str_starts_with($line, '- ') && str_contains($line, '@')) {
            $addr = trim(substr($line, 2));
            if (filter_var($addr, FILTER_VALIDATE_EMAIL) && strlen($addr) <= 254) {
                $emails[] = $addr;
            }
        }
    }

    log_msg('info', sprintf('%d Empfänger aus email_list.md geladen', count($emails)));
    return array_unique($emails);
}

/* ═══════════════════════════════════════════════════
   SCHRITT 3 – E-Mail-Inhalt erstellen
═══════════════════════════════════════════════════ */
function build_subject(): string {
    $wochentage = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
    $monate     = ['Januar','Februar','März','April','Mai','Juni',
                   'Juli','August','September','Oktober','November','Dezember'];
    $d = (int)date('w');
    $m = (int)date('n') - 1;
    return sprintf(
        'Neue Tagesnachrichten – %s, %d. %s %s',
        $wochentage[$d], (int)date('j'), $monate[$m], date('Y')
    );
}

function build_plain(string $subject): string {
    $wochentage = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
    $monate     = ['Januar','Februar','März','April','Mai','Juni',
                   'Juli','August','September','Oktober','November','Dezember'];
    $d       = (int)date('w');
    $m       = (int)date('n') - 1;
    $datum   = sprintf('%s, %d. %s %s', $wochentage[$d], (int)date('j'), $monate[$m], date('Y'));
    $newsUrl = NEWS_URL;

    return <<<TEXT
────────────────────────────────────────
 TAGESNACHRICHTEN – LOHR · ASCHAFFENBURG
 Lohr am Main · Aschaffenburg · Main-Spessart · Miltenberg
────────────────────────────────────────

Guten Tag,

die aktuellen Tagesnachrichten für {$datum} sind ab sofort verfügbar –
frisch recherchiert aus Ihrer Region.

➜ Jetzt lesen: {$newsUrl}

Was erwartet Sie heute?
Alle wichtigen Meldungen aus Lohr am Main, Aschaffenburg, dem Landkreis
Main-Spessart, dem Landkreis Miltenberg und Unterfranken – übersichtlich
aufbereitet, täglich aktualisiert.

──────────────────────────────

Mit freundlichen Grüßen

Michael Bergfeld
DoMeZos-Ware

──────────────────────────────
Abmeldung: Um sich vom Newsletter abzumelden, antworten Sie auf diese
E-Mail einfach mit dem Wort: abmelden
TEXT;
}

function build_html(string $subject): string {
    $wochentage = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
    $monate     = ['Januar','Februar','März','April','Mai','Juni',
                   'Juli','August','September','Oktober','November','Dezember'];
    $d       = (int)date('w');
    $m       = (int)date('n') - 1;
    $datum   = sprintf('%s, %d. %s %s', $wochentage[$d], (int)date('j'), $monate[$m], date('Y'));
    $uhrzeit = date('H:i') . ' Uhr';

    return <<<HTML
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{$subject}</title>
</head>
<body style="margin:0;padding:0;background:#ECEFF1;font-family:Georgia,Times,'Times New Roman',serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#ECEFF1;padding:24px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0"
               style="max-width:600px;width:100%;background:#fff;border-radius:10px;
                      overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);">

          <!-- HEADER -->
          <tr>
            <td style="background:linear-gradient(135deg,#0D2137 0%,#1565C0 55%,#1E88E5 100%);
                       padding:32px 36px 28px;">
              <p style="margin:0 0 6px;color:rgba(255,255,255,.7);font-size:.78rem;
                         letter-spacing:.12em;text-transform:uppercase;font-family:sans-serif;">
                Ihr täglicher Nachrichten-Dienst
              </p>
              <h1 style="margin:0;color:#fff;font-size:1.55rem;font-weight:bold;
                          letter-spacing:-.01em;line-height:1.25;">
                Tagesnachrichten
              </h1>
              <p style="margin:6px 0 0;color:rgba(255,255,255,.8);font-size:.88rem;
                         font-family:sans-serif;">
                Lohr am Main &nbsp;·&nbsp; Aschaffenburg &nbsp;·&nbsp;
                Main-Spessart &nbsp;·&nbsp; Miltenberg
              </p>
            </td>
          </tr>

          <!-- DATUM-BANNER -->
          <tr>
            <td style="background:#1565C0;padding:8px 36px;">
              <p style="margin:0;color:#BBDEFB;font-size:.8rem;font-family:sans-serif;">
                {$datum} &nbsp;&bull;&nbsp; {$uhrzeit} CEST
              </p>
            </td>
          </tr>

          <!-- BODY -->
          <tr>
            <td style="padding:36px 36px 28px;">
              <p style="margin:0 0 16px;font-size:1rem;color:#1A1A2E;line-height:1.7;">
                Guten Tag,
              </p>
              <p style="margin:0 0 20px;font-size:1rem;color:#37474F;line-height:1.75;">
                die <strong>aktuellen Tagesnachrichten</strong> für den heutigen Tag
                sind ab sofort verfügbar – frisch recherchiert, sorgfältig zusammengefasst,
                direkt aus Ihrer Region.
              </p>

              <!-- REGION-BADGES -->
              <p style="margin:0 0 28px;font-family:sans-serif;">
                <span style="display:inline-block;background:#E3F2FD;color:#0D47A1;
                             border-radius:20px;padding:4px 13px;font-size:.75rem;
                             font-weight:bold;margin:3px 2px;">&#127968; Lohr am Main</span>
                <span style="display:inline-block;background:#E8F5E9;color:#1B5E20;
                             border-radius:20px;padding:4px 13px;font-size:.75rem;
                             font-weight:bold;margin:3px 2px;">&#127961; Aschaffenburg</span>
                <span style="display:inline-block;background:#FFF8E1;color:#E65100;
                             border-radius:20px;padding:4px 13px;font-size:.75rem;
                             font-weight:bold;margin:3px 2px;">&#127795; Main-Spessart</span>
                <span style="display:inline-block;background:#FCE4EC;color:#880E4F;
                             border-radius:20px;padding:4px 13px;font-size:.75rem;
                             font-weight:bold;margin:3px 2px;">&#127749; Miltenberg</span>
              </p>

              <!-- CTA-BUTTON -->
              <table cellpadding="0" cellspacing="0" style="margin:0 0 32px;">
                <tr>
                  <td style="background:linear-gradient(135deg,#1565C0,#0D47A1);
                              border-radius:8px;box-shadow:0 4px 14px rgba(21,101,192,.4);">
                    <a href="https://snote.fun/news"
                       style="display:inline-block;color:#fff;text-decoration:none;
                              font-family:sans-serif;font-weight:bold;font-size:1rem;
                              padding:14px 36px;letter-spacing:.02em;">
                      &#128240;&nbsp; Jetzt Nachrichten lesen
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 8px;font-size:.9rem;color:#546E7A;line-height:1.7;">
                Sollte der Button nicht funktionieren, kopieren Sie bitte diesen Link
                direkt in Ihren Browser:
              </p>
              <p style="margin:0 0 32px;font-size:.85rem;">
                <a href="https://snote.fun/news"
                   style="color:#1565C0;text-decoration:underline;">
                  https://snote.fun/news
                </a>
              </p>

              <!-- TRENNLINIE -->
              <hr style="border:none;border-top:1px solid #E0E0E0;margin:0 0 24px;">

              <!-- SIGNATUR -->
              <p style="margin:0 0 4px;font-size:.95rem;color:#1A1A2E;line-height:1.6;">
                Mit freundlichen Grüßen
              </p>
              <p style="margin:0;font-size:1rem;color:#0D2137;font-weight:bold;line-height:1.5;">
                Michael Bergfeld
              </p>
              <p style="margin:0;font-size:.85rem;color:#546E7A;font-family:sans-serif;
                         letter-spacing:.04em;">
                DoMeZos-Ware
              </p>
            </td>
          </tr>

          <!-- FOOTER -->
          <tr>
            <td style="background:#F5F5F5;border-top:1px solid #E0E0E0;
                       padding:18px 36px;border-radius:0 0 10px 10px;">
              <p style="margin:0;font-size:.72rem;color:#90A4AE;
                         font-family:sans-serif;line-height:1.7;text-align:center;">
                Sie erhalten diese E-Mail, weil Sie sich für den Tagesnachrichten-Newsletter
                von DoMeZos-Ware angemeldet haben.<br>
                <strong>Abmeldung:</strong> Antworten Sie einfach auf diese E-Mail
                mit dem Wort&nbsp;<em>abmelden</em>&nbsp;– Sie werden umgehend
                aus dem Verteiler entfernt.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

/* ═══════════════════════════════════════════════════
   SCHRITT 4 – SMTP-Versand (Gmail SSL, Port 465)
═══════════════════════════════════════════════════ */

/** Liest eine SMTP-Antwortzeile; gibt Code (int) und Text zurück. */
function smtp_read($socket): array {
    $response = '';
    while (($line = fgets($socket, 512)) !== false) {
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') break; // Letzte Zeile hat "NNN Text"
    }
    $code = (int)substr(trim($response), 0, 3);
    return [$code, trim($response)];
}

/** Sendet einen SMTP-Befehl und liest die Antwort. */
function smtp_cmd($socket, string $cmd): array {
    fwrite($socket, $cmd . "\r\n");
    return smtp_read($socket);
}

function send_newsletter_email(
    string $smtp_user,
    string $smtp_pass,
    string $to,
    string $subject,
    string $plain_body,
    string $html_body
): bool {
    $socket = @stream_socket_client(
        'ssl://' . SMTP_HOST . ':' . SMTP_PORT,
        $errno, $errstr, 30
    );
    if (!$socket) {
        log_msg('error', "SMTP-Verbindung fehlgeschlagen ({$errno}): {$errstr}");
        return false;
    }
    stream_set_timeout($socket, 30);

    // Begrüßung lesen
    smtp_read($socket);

    // EHLO
    [$code] = smtp_cmd($socket, 'EHLO localhost');
    if ($code !== 250) {
        // HELO als Fallback
        smtp_cmd($socket, 'HELO localhost');
    }

    // AUTH LOGIN
    [$code] = smtp_cmd($socket, 'AUTH LOGIN');
    if ($code !== 334) {
        log_msg('error', "AUTH LOGIN fehlgeschlagen (Code {$code})");
        fclose($socket);
        return false;
    }

    [$code] = smtp_cmd($socket, base64_encode($smtp_user));
    if ($code !== 334) {
        log_msg('error', "SMTP-Benutzername nicht akzeptiert (Code {$code})");
        fclose($socket);
        return false;
    }

    [$code, $resp] = smtp_cmd($socket, base64_encode($smtp_pass));
    if ($code !== 235) {
        log_msg('error', "SMTP-Authentifizierung fehlgeschlagen (Code {$code})");
        fclose($socket);
        return false;
    }

    // MAIL FROM
    [$code] = smtp_cmd($socket, "MAIL FROM:<{$smtp_user}>");
    if ($code !== 250) {
        log_msg('error', "MAIL FROM abgelehnt (Code {$code})");
        fclose($socket);
        return false;
    }

    // RCPT TO
    [$code] = smtp_cmd($socket, "RCPT TO:<{$to}>");
    if ($code !== 250) {
        log_msg('error', "RCPT TO abgelehnt für {$to} (Code {$code})");
        fclose($socket);
        return false;
    }

    // DATA
    [$code] = smtp_cmd($socket, 'DATA');
    if ($code !== 354) {
        log_msg('error', "DATA-Befehl abgelehnt (Code {$code})");
        fclose($socket);
        return false;
    }

    // MIME-Nachricht aufbauen
    $boundary  = 'ALT_' . bin2hex(random_bytes(12));
    $from_name = '=?UTF-8?B?' . base64_encode('Tagesnachrichten | DoMeZos-Ware') . '?=';
    $subj_enc  = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $mime  = "Date: " . date('r') . "\r\n";
    $mime .= "From: {$from_name} <{$smtp_user}>\r\n";
    $mime .= "To: {$to}\r\n";
    $mime .= "Subject: {$subj_enc}\r\n";
    $mime .= "MIME-Version: 1.0\r\n";
    $mime .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
    $mime .= "X-Mailer: DoMeZos-Ware Newsletter Bot\r\n";
    $mime .= "\r\n";

    // Plaintext-Part
    $mime .= "--{$boundary}\r\n";
    $mime .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $mime .= "Content-Transfer-Encoding: base64\r\n";
    $mime .= "\r\n";
    $mime .= chunk_split(base64_encode($plain_body), 76, "\r\n");

    // HTML-Part
    $mime .= "--{$boundary}\r\n";
    $mime .= "Content-Type: text/html; charset=UTF-8\r\n";
    $mime .= "Content-Transfer-Encoding: base64\r\n";
    $mime .= "\r\n";
    $mime .= chunk_split(base64_encode($html_body), 76, "\r\n");

    $mime .= "--{$boundary}--\r\n";
    $mime .= ".\r\n";

    fwrite($socket, $mime);
    [$code] = smtp_read($socket);
    $ok = ($code === 250);

    smtp_cmd($socket, 'QUIT');
    fclose($socket);

    return $ok;
}

/* ═══════════════════════════════════════════════════
   HAUPTPROGRAMM
═══════════════════════════════════════════════════ */
function main(): void {
    log_msg('info', '════════ Script gestartet ════════');

    // 1 – Download
    if (!is_writable(__DIR__)) {
        log_msg('error', 'Verzeichnis nicht beschreibbar: ' . __DIR__);
        http_response_code(500);
        return;
    }

    $dl_ok = download_nachrichten();
    if (!$dl_ok) {
        log_msg('error', 'Download fehlgeschlagen – Newsletter-Versand wird trotzdem versucht');
    }

    // 2 – Empfänger laden
    $recipients = load_recipients();
    if (empty($recipients)) {
        log_msg('info', 'Keine Empfänger gefunden – Versand übersprungen');
        log_msg('info', '════════ Script beendet ════════');
        echo $dl_ok ? 'OK: Nachrichten.html aktualisiert. Keine Empfänger.' . PHP_EOL
                    : 'WARNUNG: Download fehlgeschlagen, keine Empfänger.' . PHP_EOL;
        return;
    }

    // 3 – SMTP-Credentials aus ENV
    $smtp_user = trim((string)getenv('GMAIL_USER'));
    $smtp_pass = trim((string)getenv('GMAIL_APP_PASSWORD'));

    if ($smtp_user === '' || $smtp_pass === '') {
        log_msg('error', 'GMAIL_USER oder GMAIL_APP_PASSWORD nicht gesetzt – Versand abgebrochen');
        http_response_code(500);
        echo 'FEHLER: SMTP-Credentials fehlen.' . PHP_EOL;
        return;
    }

    // 4 – E-Mail-Inhalte erstellen
    $subject    = build_subject();
    $plain_body = build_plain($subject);
    $html_body  = build_html($subject);

    // 5 – Versand
    $sent   = 0;
    $errors = 0;
    foreach ($recipients as $addr) {
        log_msg('info', "Sende an: {$addr}");
        $ok = send_newsletter_email($smtp_user, $smtp_pass, $addr, $subject, $plain_body, $html_body);
        if ($ok) {
            log_msg('info', "OK: {$addr}");
            $sent++;
        } else {
            log_msg('error', "FEHLER: {$addr}");
            $errors++;
        }
        if (count($recipients) > 1) usleep(300_000); // 0,3 s Pause zwischen Mails
    }

    log_msg('info', sprintf('Versand abgeschlossen: %d gesendet, %d Fehler', $sent, $errors));
    log_msg('info', '════════ Script beendet ════════');

    if ($errors > 0) {
        http_response_code(500);
        echo sprintf('WARNUNG: %d/%d E-Mails gesendet, %d Fehler. Siehe Log.' . PHP_EOL,
            $sent, count($recipients), $errors);
    } else {
        http_response_code(200);
        echo sprintf('OK: index.html aktualisiert, Newsletter an %d Empfänger versendet.' . PHP_EOL, $sent);
    }
}

main();
