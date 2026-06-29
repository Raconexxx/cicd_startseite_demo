# Projekt - Startseite CI/CD Demo

## Kurzbeschreibung

Dieses Projekt ist eine kleine PHP-/MariaDB-Webanwendung für eine persönliche oder interne Startseite. Für den Unterricht wurde die Vorlage bereinigt:

- echte interne Links wurden durch Demo-Links ersetzt
- private Domains und IP-Adressen wurden entfernt
- persönliche E-Mail-Adressen wurden durch Demo-Werte ersetzt
- Konfiguration wird über `.env` gesetzt
- `.env.example` zeigt nur Beispielwerte

## Warum eignet sich das Projekt für CI/CD?

Das Projekt ist klein genug für den Unterricht, aber realistisch genug für echte CI/CD-Fragen:

- Es gibt Anwendungscode.
- Es gibt Konfiguration.
- Es gibt Datenbankzugriff.
- Es gibt Container über Docker Compose.
- Es gibt sensible Werte, die nicht ins Repository gehören.
- Es gibt Pipeline-Dateien für GitHub und GitLab.

## Lokaler Start

```bash
cp .env.example .env
docker compose up -d
```

Danach öffnen:

```text
http://localhost:28860
```

phpMyAdmin:

```text
http://localhost:28861
```

## Pipeline-Dateien

GitHub Actions:

```text
.github/workflows/ci.yml
```

GitLab CI/CD:

```text
.gitlab-ci.yml
```

Die Einstiegspipeline prüft:

- PHP-Syntax
- Beispielkonfiguration
- Docker-Compose-Konfiguration

## Was ist bewusst noch nicht enthalten?

Für den Unterricht ist das gut, weil man die Pipeline gemeinsam erweitern kann:

- echte Unit-Tests
- Security-Scan
- Secret-Scan
- Container-Build
- Artefaktbereitstellung
- Deployment auf Testumgebung
- manuelle Freigabe für Produktion

## Wichtige Unterrichtsfrage

> Was muss aus einem privaten Projekt herausgelöst werden, bevor es geteilt oder veröffentlicht wird?

Gute Antworten:

- Zugangsdaten
- Tokens
- interne URLs
- private IP-Adressen
- echte Mailadressen
- lokale absolute Pfade
- produktive Datenbankwerte

## Erwartete Erweiterung

Eine sinnvolle produktionsnähere Pipeline könnte so aussehen:

```text
Pull Request
  -> Lint
  -> Tests
  -> Secret-Scan
  -> Security-Scan

Merge in main
  -> Build
  -> Artefakt oder Container erstellen
  -> Deploy auf Testumgebung

Manuelle Freigabe
  -> Deploy auf Produktion
  -> Monitoring prüfen
  -> Rollback bei Fehlern
```
