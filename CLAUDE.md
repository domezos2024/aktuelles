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
Direkt WebFetch (kein WebSearch nötig) – alle parallel in EINEM Turn absetzen:

**Blaulicht (Prio 1):**
- `https://www.meine-news.de/landkreis-main-spessart/c-blaulicht`
- `https://www.meine-news.de/landkreis-aschaffenburg/c-blaulicht`
- `https://www.meine-news.de/landkreis-miltenberg/c-blaulicht`

**Allgemein/Lokal:**
- `https://www.meine-news.de/aschaffenburg`
- `https://www.meine-news.de/lohr-am-main`
- `https://aschaffenburg.news/aktuelles.html`
- `https://www.infranken.de/lk/aschaffenburg/uebersicht/`
- `https://www.infranken.de/lk/main-spessart/uebersicht/`
- `https://main-spessart.de/aktuelles/pressemitteilungen`

**GESPERRT:** `main-echo.de` → Paywall/Werbung, kein nutzbarer Inhalt. Alle URLs ignorieren.

**Erfahrungen:**
- meine-news.de + infranken.de: og:image + Kurzfassung direkt aus Übersicht → kein Nachfetchen nötig
- Alle Aufrufe parallel → spart erheblich Zeit und Token
