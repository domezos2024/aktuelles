# Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart

WORKSPACE: GitHub: domezos2024/aktuelles branch: main

---

## Schritt 1 – Repo holen
- `git fetch origin main && git checkout main && git pull origin main`

## Schritt 2 – Journal lesen
- `journal.md` lesen (nur heutiger Eintrag)
- Styles / Farben / Fonts / Themen-Titel merken → ANDERE Werte und Themen heute wählen

## Schritt 3 – Wetter (BrightSky API, DWD-Daten, kein API-Key)

URL: `https://api.brightsky.dev/weather?lat=50.0&lon=9.57&date=HEUTE&last_date=ÜBERMORGEN`

- HEUTE / ÜBERMORGEN = ISO-Datum YYYY-MM-DD (heute und heute+2 Tage)
- Felder: timestamp, temperature, condition, icon, wind_speed, wind_direction, precipitation, precipitation_probability
- 24h stündlich (Timestamps sind UTC; CEST = UTC+2) + Min/Max je Tag aus stündlichen Werten ableiten

## Schritt 4 – Nachrichten recherchieren (WebSearch + WebFetch)

Suchanfragen:
- "Lohr am Main Nachrichten aktuell"
- "Aschaffenburg Nachrichten aktuell"
- "Landkreis Main-Spessart aktuell"
- "Landkreis Aschaffenburg aktuell"
- "Unterfranken Nachrichten heute"

WebFetch (letzte 48h): main-echo.de, infranken.de, aschaffenburg.news, meine-news.de, br.de/nachrichten/bayern

## Schritt 5 – Top 15 Meldungen auswählen (lokal → regional)

Für jede Meldung:
- Titel | Zusammenfassung (min. 4 Sätze) | URL | Kategorie (farbig) | Ort (farbig)
- Prioritäten: Blaulicht/Kriminalität > Politik > Wirtschaft > Gesellschaft/Kultur/Sport
- Mindestens 10 echte, von gestern VERSCHIEDENE Meldungen (≤48h alt)

## Schritt 6 – Nachrichten.html neu schreiben (komplett ersetzen)

Anforderungen:
- Inline CSS, responsive, valide HTML5, eine Datei
- **Kein JavaScript, keine Animationen**
- Header: Wochentag, Datum, Uhrzeit + "Nachrichten – Lohr am Main & Aschaffenburg"
- Wetter-Box: 24h stündlich scrollbar + 2-Tage-Karten
- 15 News-Karten: Kategorie-Badge, Regions-Badge, Titel, Zusammenfassung, Quelle-Link
- **Design täglich wechseln** (Farben, Header-Gradient, Hintergrund ≠ journal.md)
- Zeitstempel und Quellenliste im Footer

## Schritt 7 – journal.md komplett neu schreiben (NUR heute, keine Historie!)

Format:
```
# Tagesnachrichten-Journal – domezos2024/aktuelles

## $DATUM (YYYY-MM-DD) – $UHRZEIT Uhr

### Design
- Schema: [Name]
- Header-Gradient: [HEX von] → [HEX bis]
- Hintergrund: [HEX]
- Karten: [HEX]
- Akzentfarben: [HEX Liste]
- Layout: [grid-3col / grid-2col / card-stream]

### Themen
- [Schlagwort kurz] | [URL] | [Kategorie] | [Region]
(alle 15 Themen)
```

WICHTIG: Datei enthält **exakt einen Eintrag** (heute). Keine früheren Tage speichern.

## Schritt 8 – Push, PR, Merge, Schließen

```bash
git add Nachrichten.html journal.md .claude/
git commit -m "Tagesnachrichten YYYY-MM-DD – HH:MM Uhr"
git push -u origin <branch>
```

Dann:
- Pull Request nach main erstellen (Draft)
- PR reviewen (kein Review-Kommentar nötig wenn alles korrekt)
- PR mergen (squash oder merge commit)
- PR schließen / Branch löschen

## Qualitätskriterien

- Min. 10 echte, verifizierte Meldungen (anders als gestern)
- Kein Dummy-Inhalt, keine Platzhalter
- HTML valide, 1 Datei, kein externes CSS/JS
- Keine CSS-Animationen
- Design komplett anders als Vortag (journal.md prüfen)
- Repo auf main aktuell nach Merge
