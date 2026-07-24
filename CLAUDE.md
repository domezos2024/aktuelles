# CLAUDE.md

Dieses Repository betreibt die automatisierte Routine **„Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart"**
(Skill: `tagesnachrichten-lohr-aschaffenburg`, siehe `.claude/commands/`), die täglich `index.html` und
`journal.md` neu erstellt und per Pull Request nach `main` bringt.

## Standing Authorization: Auto-Merge dieser Routine

Für Pull Requests, die von der Tagesnachrichten-Routine erzeugt werden (Branches `claude/*`,
Titel-Muster „Tagesnachrichten YYYY-MM-DD – HH:MM Uhr CEST"), gilt eine dauerhafte Freigabe:

- Claude darf diese PRs **ohne erneute Rückfrage** nach main mergen (Squash-Merge), sobald
  `mergeable_state` "clean" ist und keine offenen Review-Kommentare vorliegen.
- Anschließend darf Claude den Source-Branch schließen/löschen, wie in Schritt 8 des Skills
  (`tagesnachrichten-lohr-aschaffenburg.md`) beschrieben.
- Diese Freigabe gilt **nur** für PRs dieser konkreten Tagesnachrichten-Routine. Sie erstreckt sich
  **nicht** auf andere Repos, andere Branches/PRs, Force-Pushes, `git reset --hard`, Löschen fremder
  Branches oder sonstige destruktive Aktionen – dafür gilt weiterhin die normale Rückfrage-Pflicht.
- Voraussetzung bleibt: Der übliche Qualitäts-/Verifikationscheck aus dem Skill (Bilder erreichbar,
  keine Themen-Duplikate der letzten 10 Tage, valides HTML) muss vor dem Merge durchlaufen sein.

Damit läuft die tägliche Routine vollautomatisch von Recherche über PR-Erstellung bis Merge, ohne
dass eine manuelle Bestätigung des Nutzers nötig ist.

## Problembehandlung - falls ein Problem, fehler oder neue Erkenntnis auftaucht
- frage Dich: Wieso ist das Problem aufgetreten? Warum wurde dieser Weg gewählt, was ist das Ziel? Was ist die bessere, funktionierende Methode um das gewollte Ziel zu erreichen?
- merke Dir in der CLAUDE.md den Workaround bzw. die Problemlösung oder Erkenntnis für zukünftige Runs unter den Punkten `## mache dies` und `## mache dies nicht` mit Erklärung wieso

## mache dies
- nutze den httpListener MCP - https://domezos-ware.de/mcp - so oft Du kannst um Aufgaben zu erledigen falls:
  1. httpListener Konnektor hierfür weniger Tokenverbrauch hat als andere Tools und Mittel
  2. httpListener Konnector schneller ist als andere Tools oder Mittel
- sei sparsam mit dem Tokenúmgang indem Du auch während der Arbeit überlegst wie man etwas anders machen könnte um das Gleiche Ziel zu erreichen mit weniger Tokenverbrauch und/oder weniger Zeit Anspruch
- merke Dir Webfetches, Websearches, Curls und Links die besonders Erfolgreich sind für aktuelle Nachrichten für die Kreise/Städte und nutze diese bevorzugt
- gewisse Domains, Webseiten und Links die nicht zum gewünschten Ergebnis führen, zu viele Token kosten oder zu viel Zeit in Anspruch nehmen in Zukunft nicht mehr nutzen
- verfeinere IMMER die CLAUDE.md und die SKILL-Datei `.claude/commands/tagesnachrichten-lohr-aschaffenburg.md` und merke dir darin was gut läuft und was nicht. Begründe dies und halte Dich an was gut läuft. Suche für was schlecht läuft alternativen oder Workarounds und verbessere den Ablauf und merke dir diese.

## mache dies nicht
- **NIEMALS Agenten jeglicher Art nutzen** – keine Hintergrund-Agenten, keine Explore-Agenten, kein Agent-Tool, kein allgemeiner Agent. NIEMALS!! Nur direkte Tools: WebFetch, WebSearch, Bash, Read, Write, Edit, Grep, Glob, GitHub MCP, httpListener MCP.
- **IMMER zuerst lesen, dann schreiben** – IMMER Read auf eine Datei aufrufen bevor Write oder Edit genutzt wird, sonst schlägt Write mit "File has not been read yet" fehl. Auch wenn nur 1 Zeile gelesen wird, muss Read zuerst kommen.
- CLAUDE.md und SKILL-Datei `.claude/commands/tagesnachrichten-lohr-aschaffenburg.md` niemals so ändern das die Grundfunktion dieses Workflow darunter leidet oder kaputt geht.

## recherchieren
Bewährte Links und Methoden für aktuelle Nachrichten (Stand 2026-07-24):

### Beste Quellen (direkt WebFetch, kein WebSearch nötig):
- `https://www.meine-news.de/landkreis-main-spessart/c-blaulicht` → Blaulicht LK Main-Spessart (liefert Titel + Teaser + URLs direkt)
- `https://www.meine-news.de/landkreis-aschaffenburg/c-blaulicht` → Blaulicht LK Aschaffenburg
- `https://www.meine-news.de/landkreis-miltenberg/c-blaulicht` → Blaulicht LK Miltenberg
- `https://www.meine-news.de/aschaffenburg` → Lokales AB-Stadt
- `https://www.meine-news.de/lohr-am-main` → Lokales Lohr
- `https://aschaffenburg.news/aktuelles.html` → Allgemein AB
- `https://www.infranken.de/lk/aschaffenburg/uebersicht/` → LK Aschaffenburg
- `https://www.infranken.de/lk/main-spessart/uebersicht/` → LK Main-Spessart
- `https://main-spessart.de/aktuelles/pressemitteilungen` → Pressemitteilungen LK

### GESPERRTE Quellen (NIEMALS verwenden):
- **main-echo.de – KOMPLETT GESPERRT** → Seite zeigt Werbung/Paywalls die angeklickt werden müssen bevor man den Artikel sieht. Keine brauchbaren Inhalte per WebFetch extrahierbar. Alle main-echo.de URLs ignorieren, auch wenn sie in Suchergebnissen auftauchen.

### Erfahrungen:
- meine-news.de und infranken.de liefern zuverlässig og:image und Kurzfassungen
- Alle Aufrufe PARALLEL in einem Turn absetzen → spart viel Zeit
