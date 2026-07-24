# CLAUDE.md
Routine: **Tagesnachrichten Lohr · Aschaffenburg · Main-Spessart** – täglich `index.html` + `journal.md` → PR nach `main`. Skill: `.claude/commands/tagesnachrichten-lohr-aschaffenburg.md`

## Auto-Merge (dauerhafte Freigabe)
Branch `claude/*`, Titel „Tagesnachrichten YYYY-MM-DD – HH:MM Uhr CEST":
- Squash-Merge ohne Rückfrage wenn `mergeable_state` "clean" + kein offener Review-Kommentar
- Branch danach löschen (S8 des Skills)
- Nur für diese Routine; keine anderen Repos, PRs, destructive Actions
- Voraussetzung: Bilder erreichbar, keine Duplikate letzte 10 Tage, valides HTML

## Regeln
- **Kein Agent** – kein Agent-Tool, Explore, Background-Agent. Nur: WebFetch, WebSearch, Bash, Read, Write, Edit, Grep, Glob, GitHub MCP, httpListener MCP
- **Read vor Write/Edit** – immer erst Read aufrufen, sonst schlägt Write fehl
- **Parallelität** – alle unabhängigen Tool-Calls in EINEM Turn bündeln
- **httpListener** nutzen wenn Token-/Zeitersparnis gegenüber anderen Tools
- **Fehler/Erkenntnisse** – unter `## lernts` festhalten mit Begründung; CLAUDE.md + Skill nie so ändern, dass Kernfunktion bricht

## lernts
*(Lessons learned, Workarounds – bei Bedarf aktualisieren)*

## recherchieren
**1 MCP-Call** statt 9 WebFetch-Calls:
`mcp__httpListener__run_command (`https://domezos-ware.de/mcp`) → fetch_nachrichten (max_alter_stunden: 48)`

Liefert alle Meldungen aus Blaulicht + Allgemein/Lokal in einem Schritt zurück.

**GESPERRT:** `main-echo.de` → Paywall/Werbung, kein nutzbarer Inhalt. Alle URLs ignorieren.

**Erfahrungen:**
- meine-news.de + infranken.de: og:image + Kurzfassung direkt aus Übersicht → kein Nachfetchen nötig
- fetch_nachrichten-Ergebnis enthält Blaulicht (Prio 1) + Allgemein/Lokal in einem Aufruf
