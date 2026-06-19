# Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart

WORKSPACE: GitHub: domezos2024/aktuelles branch: main

---

## Schritt 1 – Repo holen
- `git fetch origin main && git checkout main && git pull origin main`

## Schritt 2 – Journal lesen
- `journal.md` vollständig lesen
- **Design-Abschnitt** (nur heutiger Eintrag): Styles / Farben / Fonts merken → ANDERE Werte heute wählen
- **Themen-Abschnitt** (alle bis zu 10 Tage): Alle gespeicherten Schlagwörter, URLs und Titel der letzten 10 Tage merken → KEINE dieser Meldungen heute wiederholen (weder gleiche URL noch gleiches Thema/Ereignis)

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
- Mindestens 10 echte, von den letzten 10 Tagen VERSCHIEDENE Meldungen (≤48h alt)
- Keine URL, kein Thema/Ereignis das bereits in einem der letzten 10 Journal-Einträge steht
- Bei Zweifeln: Schlagwort aus journal.md mit Suchbegriff abgleichen, lieber eine neue Meldung wählen

## Schritt 6 – Nachrichten.html neu schreiben (komplett ersetzen)

Anforderungen:
- Inline CSS, responsive, valide HTML5, eine Datei
- **Kein JavaScript, keine Animationen**
- Header: Wochentag, Datum, Uhrzeit (Berlin CEST, UTC+2) + "Nachrichten – Lohr am Main & Aschaffenburg"
- Wetter-Box: 24h stündlich scrollbar + 2-Tage-Karten
- 15 News-Karten: Kategorie-Badge, Regions-Badge, Titel, Zusammenfassung, Quelle-Link
- **Design täglich wechseln** (Farben, Header-Gradient, Hintergrund ≠ journal.md)
- Zeitstempel und Quellenliste im Footer

## Schritt 7 – journal.md neu schreiben (rollierende 10-Tage-Historie)

**Struktur:** Die Datei enthält zwei getrennte Bereiche:

1. **Design-Block** – exakt ein Eintrag (nur heute, Vortag wird überschrieben)
2. **Themen-Block** – bis zu 10 Tages-Einträge (heute oben, älteste unten; Eintrag >10 Tage wird gestrichen)

Format:
```
# Tagesnachrichten-Journal – domezos2024/aktuelles

## Design – $DATUM (YYYY-MM-DD) – $UHRZEIT Uhr CEST
- Schema: [Name]
- Header-Gradient: [HEX von] → [HEX bis]
- Hintergrund: [HEX]
- Karten: [HEX]
- Akzentfarben: [HEX Liste]
- Layout: [grid-3col / grid-2col / card-stream]

---

## Themen-Archiv (letzte 10 Tage – NICHT wiederholen!)

### $DATUM (YYYY-MM-DD) ← heute, neu hinzufügen
- [Schlagwort kurz] | [URL] | [Kategorie] | [Region]
(alle 15 Themen des heutigen Tages)

### $DATUM-1 (YYYY-MM-DD) ← gestern, aus Vorversion übernehmen
- ...

... (alle vorhandenen Einträge bis max. 10 Tage rückwirkend behalten)
```

**Regeln:**
- Design-Block: immer nur heute (1 Eintrag, kein Archiv)
- Themen-Block: heute oben anhängen, Einträge älter als 10 Tage entfernen
- Schlagwörter müssen so präzise sein, dass man dasselbe Ereignis in 10 Tagen noch erkennt

**ZEITZONE: Berlin (CEST = UTC+2)** – Immer die aktuelle Berlin-Uhrzeit verwenden!

## Schritt 8 – Push, PR, Merge, Schließen

```bash
git add Nachrichten.html journal.md .claude/
git commit -m "Tagesnachrichten YYYY-MM-DD – HH:MM Uhr CEST"
git push -u origin <branch>
```

**Zeitstempel-Hinweis:** Immer die aktuelle Berlin-Zeit (CEST, UTC+2) in allen drei Stellen angeben:
1. Nachrichten.html Header (Montag, 16. Juni 2026 · HH:MM Uhr CEST)
2. Nachrichten.html Footer (Aktualisierung: Montag, 16. Juni 2026 · HH:MM Uhr CEST)
3. journal.md Header (## 2026-06-16 – HH:MM Uhr CEST)

Dann:
- Pull Request nach main erstellen (Draft)
- PR reviewen (kein Review-Kommentar nötig wenn alles korrekt)
- PR mergen (squash oder merge commit)
- PR schließen / Branch löschen

## Qualitätskriterien

- Min. 10 echte, verifizierte Meldungen (anders als die letzten 10 Tage)
- Kein Dummy-Inhalt, keine Platzhalter
- HTML valide, 1 Datei, kein externes CSS/JS
- Keine CSS-Animationen
- Design komplett anders als Vortag (Design-Block in journal.md prüfen)
- Themen-Block in journal.md: heute oben, max. 10 Tage Archiv, ältere Einträge gestrichen
- Repo auf main aktuell nach Merge
