# Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart
Workspace: domezos2024/aktuelles, branch main. Regeln + Quellen: → CLAUDE.md

**Problembehandlung - falls ein Problem, fehler oder neue Erkenntnis auftaucht**
- frage Dich: Wieso ist das Problem aufgetreten? Warum wurde dieser Weg gewählt, was ist das Ziel? Was ist die bessere, funktionierende Methode um das gewollte Ziel zu erreichen?
- merke Dir in der CLAUDE.md den Workaround bzw. die Problemlösung oder Erkenntnis für zukünftige Runs unter den Punkten `## mache dies` und `## mache dies nicht` mit Erklärung wieso

## S0 – Zeitregeln (45 Min. / CI-Abbruch bei 47 Min.)
- Alle parallelen Calls in EINEM Turn bündeln, nie sequenziell
- 404 oder kein Inhalt → Artikel überspringen, nicht nachsuchen
- WebSearch-Limit → sofort auf WebFetch-Übersichtsseiten umsteigen
- Übersicht hat Titel+Teaser+Bild → Artikel NICHT einzeln nachfetchen
- Video: nur 1–3 herausragende Meldungen suchen, Rest weglassen
- Ziel: genau 20 Meldungen (≤48h alt); bei 20 Kandidaten Stop
- Untergrenze: min. 12 Meldungen (NIE 10 oder weniger)
- Notbremse: nach ≈22 Min. mit vorhandenem weiterarbeiten wenn ≥12

## S1 – Repo
`git fetch origin main && git checkout main && git pull origin main`

## S2 – Journal lesen
`journal.md` lesen: Design-Block (Farben/Fonts merken → ANDERE wählen); Themen letzte 10 Tage → NICHT wiederholen (URL + Thema/Ereignis).

## S3 – Wetter
`GET https://api.brightsky.dev/weather?lat=50.0&lon=9.57&date=HEUTE&last_date=ÜBERMORGEN`
Felder: timestamp, temperature, condition, icon, wind_speed, precipitation, precipitation_probability. Timestamps UTC, CEST=UTC+2. Min/Max je Tag aus Stundenwerten ableiten.

## S4 – Recherche
Quellen + Erfahrungen: → **CLAUDE.md ## recherchieren**
Erste Runde (parallel): Blaulicht meine-news.de (MS+AB+MIL+MSP) + aschaffenburg.news/aktuelles.html + infranken.de (AB+MS+MSP)
Zweite Runde (nur bei Lücken, parallel): main-spessart.de/pressemitteilungen, landkreis-aschaffenburg.de, mainpost.de/main-spessart/lohr

**Bilder:** og:image aus Übersicht → fertig. Fehlt: Artikel EINMAL fetchen → og:image oder erstes img. Kein Treffer → weglassen.
**Video:** YouTube-Embed auf Seite oder WebSearch `"[Thema] [Ort] youtube"`. Thumb: `https://img.youtube.com/vi/{ID}/hqdefault.jpg`. Link: `https://www.youtube.com/watch?v={ID}`

## S5 – Top 20 auswählen
Priorität: Blaulicht/Kriminalität > Politik > Wirtschaft > Gesellschaft/Kultur/Sport
Keine Duplikate (URL oder Thema) aus letzten 10 Tagen. Pro Meldung: Titel | 4-Satz-Zusammenfassung | URL | Kategorie | Ort

## S6 – index.html schreiben
Anforderungen: Inline CSS, valides HTML5, responsiv, 1 Datei, kein JS, keine Animationen.

**Pflicht-Elemente:**
- Direkt nach `<body>`: `<img src="visit_counter.php" alt="" width="1" height="1" style="display:none" aria-hidden="true">` – IMMER unverändert
- Header: `[Wochentag], [Datum] · aktuelle-Uhrzeit=`[HH:MM:SS] Uhr CEST` · Nachrichten – Lohr am Main & Aschaffenburg mit Lkr. - Main-Spessart`
- Logo: `<img src="file_000000002f8872469668704c39f1eaf6.jpg">` | Favicon: `favicon.ico` (gleiches Verz.)
- Wetter-Box: 24h scrollbar ab jetzige Urhzeit + 2-Tage-Karten
- 20 News-Karten: Kategorie-Badge (farbig) + Regions-Badge (farbig) + Titel + Zusammenfassung + Quelle-Link
- Bilder: `<img src="URL">` float:right max 120×90px. CSS-Lightbox via `:target` (kein JS). Kein Placeholder.
  - Lightbox-Muster: `<a href="#img-N" id="img-N"><img ...></a>` + `<div id="lb-N">` mit `:target`-Selektor
- Video: YouTube-Thumb als img, ▶-Overlay via CSS `::after`, klickbar → youtube.com/watch. Kein Placeholder.
- Newsletter: E-Mail-Formular (gut sichtbar, professionell, ansprechend), POST zu PHP-Handler (save_email.php), speichert in `email_list.md` (eine E-Mail pro Zeile)
- Footer: Zeitstempel + `Impressum: Michael Bergfeld · <a href="mailto:info@domezos-ware.de">info@domezos-ware.de</a>` + Angaben ohne Gewähr + Quellenliste
- **Design täglich wechseln** (Font, Farben, Header-Gradient, Hintergrund ≠ journal.md Design-Block)
- Saisonale Events/Feiertage in Design einbeziehen (Feiertage, Ferienstart/-ende, Jahreszeiten, Volksfeste) + kurzer Bericht im Header-Bereich

## S7 – journal.md schreiben

    # Tagesnachrichten-Journal – domezos2024/aktuelles

    ## Design – YYYY-MM-DD – HH:MM Uhr CEST
    - Schema: [Name] | Gradient: [HEX]→[HEX] | BG: [HEX] | Karten: [HEX] | Akzent: [HEX,...] | Layout: [...]

    ---

    ## Themen-Archiv (letzte 10 Tage – NICHT wiederholen!)

    ### YYYY-MM-DD (heute)
    - [Schlagwort] | [URL] | [Kategorie] | [Region]

    ### YYYY-MM-DD (gestern)
    - ...

Design-Block: nur heute (1 Eintrag, überschreiben). Themen: heute oben, Einträge >10 Tage löschen. Zeitzone: CEST (UTC+2).

## S8 – Push, PR, Merge

```bash
git add index.html journal.md .claude/
git commit -m "Tagesnachrichten YYYY-MM-DD – HH:MM Uhr CEST"
git push -u origin <branch>
```

## S9 - clear context
Chatbefehl `/clear`

Zeitstempel CEST (UTC+2) in: index.html Header, index.html Footer, journal.md Header.

1. PR erstellen nach main (ready for review, KEIN Draft)
2. PR squash-mergen sobald `mergeable_state` "clean"
3. workflow_dispatch „Branches aufräumen": keep=main, dry_run=false
