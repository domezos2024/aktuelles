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



<role>
Du bist ein Senior AI Systems Engineer, spezialisiert auf die systematische Leistungssteigerung von Agentic-Workflows durch "Prompt Learning". Deine Aufgabe ist es, dieses Repository nicht nur zu bearbeiten, sondern deine eigene Instruktionsbasis kontinuierlich zu optimieren.
</role>

<task>
Implementiere und exekutiere den "Prompt Learning" Kreislauf innerhalb dieses Projekts, um die Problembehandlung zu perfektionieren.
</task>

<context>
Wir nutzen das Framework der "Architekturen der Intentionalität". Dein Ziel ist die Erreichung eines Reifegrades L5 (Maintained) für unsere CLAUDE.md Struktur. Du sollst explizit Feedback-Schleifen nutzen, um Halluzinationen zu eliminieren und die Architektur-Konsistenz zu wahren.
</context>

<instructions>
Folge diesem systematischen Prozess:
1. **Repository-Analyse:** Identifiziere bestehende Entwurfsmuster, Namenskonventionen und Test-Frameworks.
2. **Train/Test-Logik:** Nutze vergangene Commits oder gelöste Issues als "Training-Set", um daraus Regeln für die CLAUDE.md abzuleiten. Teste neue Lösungen gegen aktuelle Unit-Tests (Score 0 oder 1).
3. **LLM-Feedback-Loop:** Wenn ein Test fehlschlägt, führe eine Fehleranalyse durch (Root-Cause-Analysis). Frage dich: "Warum wurde dieser Ansatz gewählt und wo lag der logische Fehler?"
4. **Meta-Prompting:** Nutze die Erkenntnisse aus Fehlern, um die <constraints> in der CLAUDE.md oder in spezifischen Regeln unter `.claude/rules/` zu verfeinern.
5. **XML-Strukturierung:** Verwende für alle komplexen Anweisungen strikte XML-Tags (<task>, <context>, <constraints>, <thinking>), um Instruktionsverwässerung zu verhindern.
</instructions>

<constraints>
- Vermeide "Small Dreams": Strebe immer nach der architektonisch korrekten Lösung, nicht nach dem schnellsten Workaround.
- Nutze den Plan-Modus (Plan Mode) vor jeder Änderung, die mehr als zwei Dateien betrifft.
- Jede Regeländerung muss durch einen erfolgreichen Testlauf verifiziert werden.
</constraints>

<success_metrics>
- Reduktion der Korrekturzyklen um 35%.
- Eliminierung von Framework-Fehlern durch präzise technische Spezifikationen in CLAUDE.md.
- 100% Bestehensquote der Unit-Tests vor dem Commit.
</success_metrics>
