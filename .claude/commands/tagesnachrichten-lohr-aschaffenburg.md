# Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart

WORKSPACE: GitHub: domezos2024/aktuelles branch: main

---

## Schritt 0 – Zeitbudget (VERBINDLICH)

Die gesamte Routine (Schritt 1–8) muss **innerhalb von 45 Minuten** abgeschlossen sein –
der CI-Workflow bricht den Claude-Prozess nach 47 Minuten hart ab (Job-Timeout: 50 Minuten
gesamt inkl. Checkout). Arbeite entsprechend diszipliniert:

- **httpListener** für verschiedene Aufgaben nutzen falls dies den Tokenverbrauch senkt oder schneller ist als andere Tools undf Mittel
- **Tool-Aufrufe bündeln:** Mehrere unabhängige WebSearch-/WebFetch-Aufrufe IMMER in einer
  Nachricht parallel absetzen, nie sequenziell einzeln.
- **Nicht mehrfach nachfassen:** Führt ein WebFetch auf eine main-echo.de-Detailseite zu 404
  (unterschiedliche URL-Formate: `.../slug-art-XXXXXXXX` vs. `;artXXX,XXXXXXXX`), NICHT per
  WebSearch die "richtige" URL suchen – Artikel überspringen und nächsten Kandidaten nehmen.
  meine-news.de- und infranken.de-URLs (Format `.../c-kategorie/slug_aXXXXXX`) sind über
  WebFetch zuverlässig extrahierbar und sind bei Zeitdruck vorzuziehen.
- **WebSearch-Rate-Limit:** Trifft WebSearch auf ein Session-/Rate-Limit ("hit your session
  limit"), NICHT auf das Tool warten oder wiederholt erneut versuchen. Stattdessen nahtlos auf
  WebFetch umsteigen und gezielt die Kategorie-Übersichtsseiten der Kernquellen abklappern
  (z. B. `meine-news.de/<ort>/c-<kategorie>`, `aschaffenburg.news/<region>.html`) – das liefert
  zuverlässig echte Titel, URLs und Bilder ganz ohne WebSearch.
- **Keine Doppelrecherche:** Wenn eine Übersichts-/Kategorieseite in Schritt 4 bereits Titel,
  Kurzfassung und ggf. og:image liefert, NICHT zusätzlich jeden einzelnen Artikel separat
  fetchen – nur bei fehlenden Pflichtangaben (z. B. Bild explizit gewünscht, Zusammenfassung
  zu kurz) gezielt nachfetchen.
- **Video-Suche ist optional:** Nur für 1–3 besonders passende Meldungen aktiv nach einem
  YouTube-Video suchen, nicht für alle 20. Kein Video gefunden → Feld einfach weglassen.
- **Zielgröße statt Bereich:** Ziel sind genau 20 Meldungen (nicht 20–25) – bei 20 soliden,
  verifizierten Kandidaten die Recherche beenden, nicht weitersammeln.
- **Mindestanforderung (HARTE UNTERGRENZE):** Die finale Auswahl muss IMMER **mehr als 10,
  mindestens 12** solide, verifizierte Meldungen enthalten – ein Abschluss mit 10 oder weniger
  ist kein akzeptables Ergebnis der Routine.
- **Budget-Notbremse:** Ist etwa die Hälfte des Zeitbudgets (≈22 Minuten) verstrichen und
  liegen noch keine 20 Meldungen vor, mit dem vorhandenen Bestand weiterarbeiten (auch bei
  z. B. 15–18 Meldungen) statt weiter zu recherchieren – **aber nur, wenn bereits mindestens
  12 Meldungen vorliegen**. Liegen zu diesem Zeitpunkt weniger als 12 solide Kandidaten vor,
  Recherche in kompakten, gebündelten WebFetch-Batches (siehe Rate-Limit-Hinweis oben)
  fortsetzen, bis die Untergrenze von 12 erreicht ist – dafür darf das 45-Minuten-Budget maßvoll
  überzogen werden; harte Grenze bleibt der 47-Minuten-CI-Abbruch. Lieber wenige Minuten länger
  brauchen als mit 10 oder weniger Meldungen fertig werden.
- **Bild-/URL-Verifikation:** Ein einzelner HTTP-Check pro eingebundenem Bild reicht (nicht
  zusätzlich noch alle 20 Quell-URLs einzeln per curl gegenprüfen) – WebFetch/WebSearch haben
  die URL bereits durch erfolgreichen Seitenabruf verifiziert.

## Schritt 1 – Repo holen
- `git fetch origin main && git checkout main && git pull origin main`

## Schritt 2 – Journal lesen
- `journal.md` vollständig lesen
- **Design-Abschnitt** : Styles / Farben / Fonts merken → ANDERE Werte heute wählen
- **Themen-Abschnitt** (alle bis zu 10 Tage): Alle gespeicherten Schlagwörter, URLs und Titel der letzten 10 Tage merken → KEINE dieser Meldungen heute wiederholen (weder gleiche URL noch gleiches Thema/Ereignis)

## Schritt 3 – Wetter (BrightSky API, DWD-Daten, kein API-Key)

URL: `https://api.brightsky.dev/weather?lat=50.0&lon=9.57&date=HEUTE&last_date=ÜBERMORGEN`

- HEUTE / ÜBERMORGEN = ISO-Datum YYYY-MM-DD (heute und heute+2 Tage)
- Felder: timestamp, temperature, condition, icon, wind_speed, wind_direction, precipitation, precipitation_probability
- 24h stündlich (Timestamps sind UTC; CEST = UTC+2) + Min/Max je Tag aus stündlichen Werten ableiten

## Schritt 4 – Nachrichten recherchieren (WebSearch + WebFetch + Curl)

1. nutze `CLAUDE.md` in der Sektion `## recherchieren` um die Besten Wege, Mittel, Tools und Links nachzulesen und Schritt 4. durch zu führen

**Erste Runde (ein Nachrichten-Turn, alle Aufrufe parallel absetzen):** direkt die
Kategorie-/Blaulicht-Übersichtsseiten der Kernquellen fetchen statt breiter WebSearch-Anfragen
zu starten – die liefern meist schon Titel + Kurzfassung + funktionierende URL in einem Schritt:
- `https://www.meine-news.de/landkreis-main-spessart/c-blaulicht`
- `https://www.meine-news.de/landkreis-aschaffenburg/c-blaulicht`
- `https://www.meine-news.de/landkreis-miltenberg` (bzw. `/c-blaulicht`)
- `https://www.main-echo.de/ressorts/blaulicht`
- `https://aschaffenburg.news/aktuelles.html`
- `https://www.infranken.de/lk/aschaffenburg/uebersicht/` bzw. `/lk/main-spessart/uebersicht/`

**Zweite Runde (nur falls nötig, ebenfalls parallel):** WebSearch nur gezielt einsetzen, um
Lücken zu füllen (z. B. zu wenige Politik/Wirtschaft/Kultur-Kandidaten) – nicht pauschal alle
5 Themen-Suchbegriffe der Reihe nach absuchen. Presseseiten der Landkreise
(`main-spessart.de/aktuelles/pressemitteilungen`, `landkreis-aschaffenburg.de`) eignen sich gut
für Politik/Wirtschaft-Meldungen.

Bei Bedarf ergänzend: `mainpost.de/main-spessart/lohr`, `main-echo.de/region/mein-ort/97816-lohr/`,
`br.de/nachrichten/bayern`, `tagesschau.de/suche?searchText=[Ort]+[Thema]`.

- merke dir mittels Eintrag in `CLAUDE.md` Sektion `## recherchieren` wie die Websearch, Webfetch und Curl Aufrufe am Besten funktionierten und Ergebnisse auf welchen Links lieferten und das nächste mal weniger überlegen zu müssen, weniger Zeit zu verbrauche oder weniger Token zu nutzen. Trage dir die besten Links ein, besten Suchbegriffe, besten Mittel/Tools um die Beiträge zu erfassen und begründe dies immer
  
### Bilder & Videos zu jeder Meldung extrahieren

- **Bild**: Meist bereits aus der Übersichtsseite verfügbar (og:image). Fehlt es dort, EINMAL
  gezielt die Artikelseite fetchen und `<meta property="og:image">` bzw. erstes `<img>`
  extrahieren. Kein Treffer → Medien-Box für diese Meldung weglassen, nicht weitersuchen.
- **Video**: Nur für 1–3 herausragende Meldungen aktiv suchen (YouTube-Embed auf der Seite oder
  gezielte WebSearch `"[Thema] [Ort] youtube"`). Für die übrigen Meldungen kein Video-Feld.
  - YouTube-Thumbnail-URL: `https://img.youtube.com/vi/{VIDEO_ID}/hqdefault.jpg`
  - Video-Link: `https://www.youtube.com/watch?v={VIDEO_ID}`

**Regeln:**
- Kein Placeholder wenn kein echtes Bild/Video gefunden wurde → Medien-Box einfach weglassen
- Bild-URL muss öffentlich zugänglich sein (kein Login, kein Paywall-Token)

## Schritt 5 – Top 20 Meldungen auswählen (lokal → regional)

Für jede Meldung:
- Titel | Zusammenfassung (min. 4 Sätze) | URL | Kategorie (farbig) | Ort (farbig)
- Prioritäten: Blaulicht/Kriminalität > Politik > Wirtschaft > Gesellschaft/Kultur/Sport
- Ziel: 20 echte, von den letzten 10 Tagen VERSCHIEDENE Meldungen (≤48h alt) – siehe
  Zeitbudget-Notbremse in Schritt 0, falls das nicht ohne Zeitüberschreitung erreichbar ist
- Harte Untergrenze: NIE mit 10 oder weniger Meldungen abschließen – mindestens 12
- Keine URL, kein Thema/Ereignis das bereits in einem der letzten 10 Journal-Einträge steht
- Bei Zweifeln: Schlagwort aus journal.md mit Suchbegriff abgleichen, lieber eine neue Meldung wählen

## Schritt 6 – index.html neu schreiben (komplett ersetzen)

Anforderungen:
- Inline CSS, responsive, valide HTML5, eine Datei
- **Kein JavaScript, keine Animationen**
- Header: Wochentag, Datum, Uhrzeit (Berlin CEST, UTC+2) + "Nachrichten – Lohr am Main & Aschaffenburg"
- Wetter-Box: 24h stündlich scrollbar + 2-Tage-Karten
- ~20 News-Karten: Kategorie-Badge, Regions-Badge, Titel, Zusammenfassung, Quelle-Link
- **Bilder in Karten:** Echtes `<img src="URL">` oben rechts (float:right, max 120×90px), anklickbar zur Vollansicht via CSS `:target`-Lightbox (kein JS). Nur einbauen wenn URL gefunden – KEIN Placeholder.
- **Video-Standbild in Karten:** YouTube-Thumbnail (`https://img.youtube.com/vi/{ID}/hqdefault.jpg`) als `<img>` oben rechts mit Spielsymbol-Overlay (▶ rein via CSS ::after), klickbar als Link zu `https://www.youtube.com/watch?v={ID}`. Nur einbauen wenn konkrete Video-ID gefunden.
- **CSS-Lightbox:** `<a href="#img-N" id="img-N"><img ...></a>` + `<div id="lb-N">` (position:fixed, target-Selektor blendet ein). Pro Bild ein Overlay. Kein JS.
- **Design täglich wechseln** (Font (auf gute Leserlichkeit achten), Farben, Header-Gradient, Hintergrund ≠ journal.md)
- Zeitstempel und "Impressum: Coding der Seite erstellt/überwacht von Michael Bergfeld Email: info@domezos-ware.de" und eine Erklärung das die Angaben ohne Gewähr sind und auf deren Quellen zurückzuführen sind. Quellenliste im Footer
- **Besucherzähler (unsichtbar, kein JS):** Direkt nach `<body>` immer folgendes Pixel-Tag einfügen (unverändert bei jedem Run übernehmen, NICHT löschen oder sichtbar machen):
  `<img src="visit_counter.php" alt="" width="1" height="1" style="display:none" aria-hidden="true">`
  Das Skript `visit_counter.php` (liegt im Repo-Root, wird NICHT von diesem Skill neu geschrieben) zählt bei jedem Seitenaufruf automatisch serverseitig mit und schreibt/aktualisiert `besucher.json` im selben Verzeichnis wie `index.html` – ein Eintrag pro Kalendertag (`{"Datum": "TT.MM.JJJJ", "Besucher": N}`), der sich beim ersten Aufruf eines neuen Tages automatisch neu anlegt. Diese beiden Dateien werden von der täglichen Routine nicht verändert, nur das Pixel-Tag in index.html muss bei jedem Neuschreiben erhalten bleiben.

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
git add index.html journal.md .claude/
git commit -m "Tagesnachrichten YYYY-MM-DD – HH:MM Uhr CEST"
git push -u origin <branch>
```

**Zeitstempel-Hinweis:** Immer die aktuelle Berlin-Zeit (CEST, UTC+2) in allen drei Stellen angeben:
1. index.html Header (Montag, 16. Juni 2026 · HH:MM Uhr CEST)
2. index.html Footer (Aktualisierung: Montag, 16. Juni 2026 · HH:MM Uhr CEST)
3. journal.md Header (## 2026-06-16 – HH:MM Uhr CEST)

Dann:
- Pull Request nach main erstellen (Draft)
- PR reviewen (kein Review-Kommentar nötig wenn alles korrekt)
- PR mergen (squash oder merge commit)
- PR schließen
- after merge und schließen: Trigger "Branches aufräumen" via workflow_dispatch with keep=main, dry_run=false.

## Qualitätskriterien

- Ziel: 20 echte, verifizierte Meldungen (anders als die letzten 10 Tage); bei knappem
  Zeitbudget greift die Notbremse aus Schritt 0 – aber NIE unter 12 Meldungen abschließen
- Kein Dummy-Inhalt, keine Platzhalter
- **Bilder/Videos:** Echte URLs verwenden (og:image, img-Tag, YouTube-Thumbnail). Kein gefundenes Bild → keine Medien-Box (besser leer als Platzhalter)
- **CSS-Lightbox** für Bilder funktioniert ohne JavaScript (`:target`-Selektor)
- HTML valide, 1 Datei, kein externes CSS/JS
- Keine CSS-Animationen
- Design komplett anders als Vortag (Design-Block in journal.md prüfen)

## beachte
- NUTZE NIEMALS BACKGROUND AGENTS!!
- nutze im gleichen Verzeichnis auf dem Server wie "index.html" die Datei "file_000000002f8872469668704c39f1eaf6.jpg" und "file_000000002f8872469668704c39f1eaf6.ico" und "favicon.ico" als Logo, favicon und Markenzeichen der News Seite
- wenn du das heute neue Design von index.html entwirfst, bedenke: nationale deutsche Feiertage (Ostern, Pfingsten, Weihnachten usw.), chrisltiche Feiertage, bayrische Feiertage (Christihimmelfahrt usw.), Vathertag, Muttertag, beginn Sommerferien, beginn Schule, Schulferien, saisonale Events (Winterzeit Start. Sommerzeit Start, längster Tag des Jahres, Frühlings- Sommer- Herbst- Winteranfang usw., Veranstaltungen (z.B. Volksfeste, Festivals, Stadtfeste, Wochenmärkte usw.) usw. die in nächster Zeit stattfinden) und beziehe diese Events in das Design mit ein und verfasse zusätzlich einen kurzen Bericht bzw. Erklärung zum Event
- in jede Meldung in der ein Foto/Bild/Video im Beitrag vorkommt, oder mehrere; Das Aussagekräftigste Bild aus dem Beitrag in der rechten oberen Ecke der Meldungsbox anzeigen (anklickbar zum vergrößern oder abspielen)
- auch nach Videos zu Themen und Berichte suchen und oben rechts einfügen (falls etwas passendes zu GENAU dem Thema zu finden ist auf z.B. https://youtube.com oder https://www.google.com/videohp?hl=de oder https://www.tagesschau.de). Dafür brauchst du dich NIERGENDS einloggen oder ein Konto haben!! z.B. wenn eine der Städte oder Landkreise in der www.tagesschau.de  erwähnt wird oder ähnlich.
- mache es auf der index.html Seite möglich (mit einem adäquaten Style, gut sichtbar) sich mit seiner E-Mail Adresse für den Newsletter anzumelden. Programmiere index.html so das jede Email-Adresse in der Datei `email_list.md` im gleichen Verzeichnis auf dem Host-Server von index.html gespeichert wird (gut aus lesbar und zum schnellen bearbeiten geeignete Form.) 
- merke: der neue Dateiname von "Nachrichten.html" ist "index.html"

- Themen-Block in journal.md: heute oben, max. 10 Tage Archiv, ältere Einträge gestrichen
- Repo auf main aktuell nach Merge
