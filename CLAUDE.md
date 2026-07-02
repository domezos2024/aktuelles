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
