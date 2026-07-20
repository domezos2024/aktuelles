#!/usr/bin/env python3
"""
Löscht alle Branches in einem GitHub-Repository außer den geschützten.
Standard: nur 'main' bleibt erhalten.

Verwendung:
    GITHUB_TOKEN=ghp_xxx python3 cleanup_branches.py [--dry-run] [--keep main,develop]

Umgebungsvariablen:
    GITHUB_TOKEN   GitHub Personal Access Token (Pflicht)
    GITHUB_OWNER   Repository-Besitzer  (Standard: domezos2024)
    GITHUB_REPO    Repository-Name      (Standard: aktuelles)
"""

import argparse
import os
import sys
import urllib.request
import urllib.error
import json


OWNER = os.environ.get("GITHUB_OWNER", "domezos2024")
REPO  = os.environ.get("GITHUB_REPO",  "aktuelles")
API   = "https://api.github.com"


def gh_request(method: str, path: str, token: str) -> dict | list | None:
    url = f"{API}{path}"
    req = urllib.request.Request(url, method=method)
    req.add_header("Authorization", f"Bearer {token}")
    req.add_header("Accept", "application/vnd.github+json")
    req.add_header("X-GitHub-Api-Version", "2022-11-28")
    try:
        with urllib.request.urlopen(req) as resp:
            body = resp.read()
            return json.loads(body) if body else None
    except urllib.error.HTTPError as e:
        body = e.read().decode()
        print(f"  HTTP {e.code} – {body}", file=sys.stderr)
        return None


def list_branches(token: str) -> list[str]:
    branches = []
    page = 1
    while True:
        result = gh_request("GET", f"/repos/{OWNER}/{REPO}/branches?per_page=100&page={page}", token)
        if not result:
            break
        branches.extend(b["name"] for b in result)
        if len(result) < 100:
            break
        page += 1
    return branches


def delete_branch(name: str, token: str) -> bool:
    encoded = urllib.parse.quote(name, safe="")
    result = gh_request("DELETE", f"/repos/{OWNER}/{REPO}/git/refs/heads/{encoded}", token)
    # DELETE liefert 204 No Content → result ist None bei Erfolg
    return result is None


def main() -> None:
    parser = argparse.ArgumentParser(description="Löscht alle Branches außer den angegebenen.")
    parser.add_argument(
        "--keep",
        default="main",
        help="Kommagetrennte Liste der Branches, die behalten werden (Standard: main)",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Zeigt nur, was gelöscht würde, ohne wirklich zu löschen",
    )
    args = parser.parse_args()

    token = os.environ.get("GITHUB_TOKEN")
    if not token:
        print("Fehler: Umgebungsvariable GITHUB_TOKEN nicht gesetzt.", file=sys.stderr)
        sys.exit(1)

    keep = {b.strip() for b in args.keep.split(",") if b.strip()}
    print(f"Repository : {OWNER}/{REPO}")
    print(f"Behalten   : {', '.join(sorted(keep))}")
    print(f"Dry-run    : {'ja' if args.dry_run else 'nein'}")
    print()

    branches = list_branches(token)
    to_delete = [b for b in branches if b not in keep]

    if not to_delete:
        print("Keine Branches zum Löschen gefunden.")
        return

    print(f"Gefunden: {len(branches)} Branch(es) – {len(to_delete)} werden gelöscht:\n")
    for name in to_delete:
        print(f"  – {name}")
    print()

    if args.dry_run:
        print("Dry-run aktiv – nichts wurde gelöscht.")
        return

    import urllib.parse  # noqa: PLC0415  (benötigt erst beim echten Lauf)

    deleted, failed = 0, 0
    for name in to_delete:
        encoded = urllib.parse.quote(name, safe="")
        # DELETE /repos/{owner}/{repo}/git/refs/heads/{branch}
        url = f"{API}/repos/{OWNER}/{REPO}/git/refs/heads/{encoded}"
        req = urllib.request.Request(url, method="DELETE")
        req.add_header("Authorization", f"Bearer {token}")
        req.add_header("Accept", "application/vnd.github+json")
        req.add_header("X-GitHub-Api-Version", "2022-11-28")
        try:
            urllib.request.urlopen(req)
            print(f"  ✓ gelöscht: {name}")
            deleted += 1
        except urllib.error.HTTPError as e:
            body = e.read().decode()
            print(f"  ✗ Fehler bei '{name}': HTTP {e.code} – {body}")
            failed += 1

    print(f"\nErgebnis: {deleted} gelöscht, {failed} fehlgeschlagen.")


if __name__ == "__main__":
    main()
