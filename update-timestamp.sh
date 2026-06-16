#!/bin/bash
# Konvertiert UTC-Zeit zu Berlin-Zeit (CEST, UTC+2) und aktualisiert alle Dateien

# Aktuelle Zeit in UTC
UTC_TIME=$(date -u +"%Y-%m-%d %H:%M:%S")

# Konvertiere zu Berlin-Zeit (UTC+2 für CEST)
BERLIN_TIME=$(date -d "$UTC_TIME UTC+2 hours" +"%A, %d. %B %Y · %H:%M Uhr CEST" 2>/dev/null || \
              date -v+2H -u -j -f "%Y-%m-%d %H:%M:%S" "$UTC_TIME" +"%A, %d. %B %Y · %H:%M Uhr CEST")

BERLIN_TIME_ISO=$(date -d "$UTC_TIME UTC+2 hours" +"%Y-%m-%d – %H:%M Uhr CEST" 2>/dev/null || \
                  date -v+2H -u -j -f "%Y-%m-%d %H:%M:%S" "$UTC_TIME" +"%Y-%m-%d – %H:%M Uhr CEST")

DATE_ISO=$(date +"%Y-%m-%d")
TIME_HHMM=$(date -d "now + 2 hours" +"%H:%M" 2>/dev/null || date -v+2H +"%H:%M")

echo "📍 Berlin-Zeit (CEST): $BERLIN_TIME"
echo "📝 journal.md Format: ## $BERLIN_TIME_ISO"
echo ""
echo "Aktualisiere Dateien mit Zeitstempel: $TIME_HHMM Uhr CEST"

# Aktualisiere Nachrichten.html und journal.md mit aktueller Berlin-Zeit
# (Dies müsste bei der Erstellung manuell durchgeführt werden)
