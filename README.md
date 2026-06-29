# Startseite CI/CD Demo

Dieses Projekt ist eine bereinigte Unterrichtsversion einer kleinen internen Startseite. Die Anwendung zeigt Schnellzugriffe, Gruppen, Icons, Benutzerprofile und eine einfache Datenbankanbindung.

Ziel im Unterricht: Die Teilnehmenden sehen an einem realistisch wirkenden Projekt, wie Konfiguration, Build-/Prüfschritte, Deployment-Vorbereitung und Geheimnisse zusammenhängen.

GitHub-Demo:

- <https://github.com/stoykow/cicd_startseite_demo>

## Was wurde bereinigt?

- keine privaten Domains
- keine privaten IP-Adressen
- keine echten Zugangsdaten
- keine feste persönliche E-Mail-Adresse
- Demo-Links mit `example.local`
- Konfiguration über `.env`

## Schnellstart lokal

1. Beispielkonfiguration kopieren:

```bash
cp .env.example .env
```

Unter Windows kann die Datei auch manuell kopiert werden.

2. Werte in `.env` prüfen.

3. Container starten:

```bash
docker compose up -d
```

4. Anwendung öffnen:

```text
http://localhost:28860
```

5. phpMyAdmin öffnen:

```text
http://localhost:28861
```

## Wichtige Variablen

| Variable | Bedeutung |
|---|---|
| `APP_PUBLIC_BASE_URL` | Öffentliche Basisadresse der Startseite |
| `APP_ALLOW_REGISTRATION` | Registrierung aktivieren oder deaktivieren |
| `APP_ALLOW_DEBUG_IMPERSONATION` | Demo-Login über Debug-Token erlauben |
| `APP_DEFAULT_OWNER_EMAIL` | Standard-Mailadresse für Demo-Benutzer |
| `MARIADB_*` | Datenbankname, Benutzer und Passwörter |
| `SSH_*` | optionaler SSH-Zugang in den App-Container |

Für echte Systeme gilt: `.env` niemals committen. Geheimnisse gehören in GitHub Secrets, GitLab CI/CD Variables oder in eine lokale `.env`.

## CI/CD-Idee

Das Projekt ist bewusst nicht perfekt und nicht „Enterprise“. Es eignet sich aber gut, um typische Pipeline-Fragen zu üben:

- Sind alle PHP-Dateien syntaktisch gültig?
- Ist `docker-compose.yml` gültig?
- Sind Beispielwerte in `.env.example` vollständig?
- Wird die Anwendung als Artefakt oder Container vorbereitet?
- Welche Werte dürfen nicht im Repository stehen?

## GitHub und GitLab

Im Projekt liegen Pipeline-Dateien für beide Plattformen:

- GitHub Actions: `.github/workflows/ci.yml`
- GitLab CI/CD: `.gitlab-ci.yml`

Damit kann das Projekt in GitHub laufen und später in ein lokales GitLab importiert oder gepusht werden.

## Rechtliche Platzhalter

`impressum.php` und `datenschutz.php` sind absichtlich nur Platzhalter. Für echte Veröffentlichungen müssen diese Inhalte fachlich und rechtlich korrekt ergänzt werden.

## Quellen und Hinweise

Verwendete Logos und Icons dienen im Unterricht als lokale Demo-Assets. Marken- und Urheberrechte verbleiben bei den jeweiligen Inhabern.

Dieses Unterrichtsprojekt wurde mit KI-Unterstützung bereinigt, strukturiert und dokumentiert. Die Inhalte sind für den Unterricht fachlich zu prüfen.
