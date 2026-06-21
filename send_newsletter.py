#!/usr/bin/env python3
"""
Tagesnachrichten Newsletter – täglicher Versand um 7 Uhr CEST
Liest email_list.md und versendet die Newsletter-E-Mail via Gmail SMTP.

Benötigte Repository-Secrets:
  GMAIL_USER         = michaelbergfeld1982@gmail.com
  GMAIL_APP_PASSWORD = [Gmail App-Passwort, 16 Zeichen, kein 2FA-Passwort]
"""

import smtplib
import os
import re
import sys
from datetime import datetime
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText


def load_recipients(path="email_list.md"):
    """Parse email addresses from email_list.md (lines starting with '- ')."""
    emails = []
    try:
        with open(path, encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if line.startswith("- ") and "@" in line:
                    addr = line[2:].strip()
                    if re.match(r"[^@\s]+@[^@\s]+\.[^@\s]+", addr):
                        emails.append(addr)
    except FileNotFoundError:
        print(f"[FEHLER] {path} nicht gefunden.", file=sys.stderr)
    return emails


def build_message(sender, recipient):
    """Erstellt die Newsletter-E-Mail."""
    now = datetime.now()
    weekdays_de = ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"]
    months_de = ["Januar", "Februar", "März", "April", "Mai", "Juni",
                 "Juli", "August", "September", "Oktober", "November", "Dezember"]
    date_str = f"{weekdays_de[now.weekday()]}, {now.day}. {months_de[now.month-1]} {now.year}"

    msg = MIMEMultipart("alternative")
    msg["Subject"] = "Aktuelle Themen sind bereit..."
    msg["From"] = sender
    msg["To"] = recipient

    text_body = f"""Guten Tag,

hiermit übersenden wir den neuen Link zu den Aktuellen Themen vom Umkreis:
Aschaffenburg mit Lkr., Main-Spessart, Lohr am Main, Miltenberg

https://snote.fun/news

mfG, Michael Bergfeld

---
Zum Abmelden von dem Newsletter bitte das Wort abmelden als Antwort auf diese E-Mail senden.
"""

    html_body = f"""<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"></head>
<body style="font-family:Georgia,serif;max-width:560px;margin:0 auto;padding:20px;color:#1A1A2E;">
  <div style="background:linear-gradient(135deg,#0D3B6E,#1565C0);padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#fff;margin:0;font-size:1.1rem;">Tagesnachrichten</h2>
    <p style="color:rgba(255,255,255,.8);margin:.3rem 0 0;font-size:.85rem;">Lohr am Main · Aschaffenburg · Main-Spessart · Miltenberg</p>
  </div>
  <p>Guten Tag,</p>
  <p>hiermit übersenden wir den neuen Link zu den Aktuellen Themen vom Umkreis:<br>
     <strong>Aschaffenburg mit Lkr., Main-Spessart, Lohr am Main, Miltenberg</strong></p>
  <p style="margin:24px 0;">
    <a href="https://snote.fun/news"
       style="background:#1565C0;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;">
      &#128240; Jetzt Nachrichten lesen
    </a>
  </p>
  <p>mfG, Michael Bergfeld</p>
  <hr style="border:none;border-top:1px solid #C5D9F2;margin:24px 0;">
  <p style="font-size:.72rem;color:#888;">
    Zum Abmelden von dem Newsletter bitte das Wort <em>abmelden</em> als Antwort auf diese E-Mail senden.
  </p>
</body>
</html>"""

    msg.attach(MIMEText(text_body, "plain", "utf-8"))
    msg.attach(MIMEText(html_body, "html", "utf-8"))
    return msg


def main():
    sender = os.environ.get("GMAIL_USER", "").strip()
    password = os.environ.get("GMAIL_APP_PASSWORD", "").strip()

    if not sender or not password:
        print("[FEHLER] GMAIL_USER oder GMAIL_APP_PASSWORD nicht gesetzt.", file=sys.stderr)
        sys.exit(1)

    recipients = load_recipients()
    if not recipients:
        print("[INFO] Keine Empfänger in email_list.md gefunden. Versand übersprungen.")
        sys.exit(0)

    print(f"[INFO] Versende an {len(recipients)} Empfänger...")
    errors = 0

    try:
        with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
            server.login(sender, password)
            for addr in recipients:
                try:
                    msg = build_message(sender, addr)
                    server.sendmail(sender, addr, msg.as_string())
                    print(f"[OK]  {addr}")
                except Exception as e:
                    print(f"[ERR] {addr}: {e}", file=sys.stderr)
                    errors += 1
    except smtplib.SMTPAuthenticationError:
        print("[FEHLER] Gmail-Authentifizierung fehlgeschlagen. App-Passwort prüfen.", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"[FEHLER] SMTP-Verbindung: {e}", file=sys.stderr)
        sys.exit(1)

    print(f"[INFO] Fertig. {len(recipients)-errors} gesendet, {errors} Fehler.")
    if errors:
        sys.exit(1)


if __name__ == "__main__":
    main()
