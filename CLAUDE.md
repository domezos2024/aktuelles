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

### fetch_wetter / fetch_nachrichten – kein Shell-Befehl!
**Problem:** `fetch_wetter lat=50.0 ...` oder `fetch_wetter(...)` als Shell-Befehl schlägt fehl ("not recognized as command").
**Grund:** `fetch_wetter` und `fetch_nachrichten` sind MCP-Tools des lokalen Servers (localhost:8090), keine PowerShell-Cmdlets.
**Lösung:** `mcp__httpListener__run_command` mit PowerShell `Invoke-RestMethod` als Command-String:
```
$body = '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"fetch_wetter","arguments":{"lat":"50.0","lon":"9.57","tage":3}}}';
$r = Invoke-RestMethod -Uri 'http://localhost:8090/mcp' -Method POST -Body $body -ContentType 'application/json';
Write-Output $r.result.content[0].text
```
Für nachrichten: `"name":"fetch_nachrichten","arguments":{"max_alter_stunden":48}`

### fetch_nachrichten – großes Ergebnis parsen (Python)
**Problem:** Ergebnis ~117 KB → PowerShell ConvertTo-Json erzeugt Unicode-Escapes (`+`) + Wrapper-JSON → direktes `json.loads()` schlägt mit "Extra data" fehl.
**Lösung:** STDERR-Abschnitt abschneiden, dann Outer-JSON direkt laden:
```python
stdout_start = raw.find('--- STDOUT ---\n') + len('--- STDOUT ---\n')
stderr_start = raw.find('\n--- STDERR ---')
outer = json.loads(raw[stdout_start:stderr_start].strip())
data = json.loads(outer['result']['content'][0]['text'])
```

### Read vor Write – immer!
**Problem:** Write auf existierende Datei schlägt fehl: "File has not been read yet."
**Regel:** Immer erst `Read` aufrufen (mind. 1 Zeile reicht), dann `Write` oder `Edit`.

### main-spessart.de – Published = Fetch-Time (Workaround)
**Problem:** `NachrichtenService.vb` setzt bei main-spessart.de `Published = DateTimeOffset.UtcNow` → alle Artikel erscheinen als "aktuell".
**Workaround:** URL-Artikelnummer prüfen: `≥11190` = in den letzten 7 Tagen erschienen. Oder Summary auf Datum `DD.MM.YYYY` prüfen.

### Kontext-Unterbrechung – Tool-Ergebnisse wiederverwenden
Wenn der Kontext abbricht, liegen gecachte Tool-Ergebnisse in:
`/root/.claude/projects/-home-user-aktuelles/<session-id>/tool-results/`
→ Python-Parsing-Skript erneut ausführen statt neue API-Calls zu machen.

## recherchieren
**1 MCP-Call** statt 9 WebFetch-Calls:
`mcp__httpListener__run_command (`https://domezos-ware.de/mcp`) → fetch_nachrichten (max_alter_stunden: 48)`

Liefert alle Meldungen aus Blaulicht + Allgemein/Lokal in einem Schritt zurück.

**GESPERRT:** `main-echo.de` → Paywall/Werbung, kein nutzbarer Inhalt. Alle URLs ignorieren.

**Erfahrungen:**
- meine-news.de + infranken.de: og:image + Kurzfassung direkt aus Übersicht → kein Nachfetchen nötig
- fetch_nachrichten-Ergebnis enthält Blaulicht (Prio 1) + Allgemein/Lokal in einem Aufruf
