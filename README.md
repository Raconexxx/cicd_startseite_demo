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

## Testen unter Windows mit VS Code

Das Projekt kann unter Windows direkt aus VS Code getestet werden. Eine eigene Linux-VM ist dafür nicht nötig.

Voraussetzung:

- VS Code
- Docker Desktop
- Docker Desktop muss gestartet sein

Vorgehen im VS-Code-Terminal:

```powershell
Copy-Item .env.example .env
docker compose up -d
docker compose ps
```

Danach im Browser öffnen:

```text
http://localhost:28860
```

Stoppen:

```powershell
docker compose down
```

Nur statisch ansehen geht auch ohne Docker:

```text
internal_dashboard.html
```

Dann funktionieren aber nur die statischen Karten. Login, Datenbank, Profile und phpMyAdmin brauchen Docker Compose oder eine vergleichbare PHP-/MariaDB-Umgebung.

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

## Release und Upload

Aktuell enthält das Projekt eine Einstiegspipeline zum Prüfen, aber noch kein echtes Produktivdeployment.

Für ein echtes Release wäre die bevorzugte Variante:

1. Pipeline prüft den Code.
2. Pipeline erzeugt ein Release-Artefakt oder ein Container-Image.
3. Ein Deploy-Job überträgt die Version auf den Zielserver.
4. Der Zielserver startet oder aktualisiert die Anwendung.

Empfehlung für den Upload:

- bevorzugt: `ssh`, `scp` oder `rsync` über SSH
- ebenfalls möglich: SFTP, weil es über SSH läuft
- nur wenn der Hoster es zwingend verlangt: FTPS
- vermeiden: unverschlüsseltes FTP

Typische CI/CD-Secrets für ein SSH-Deployment:

| Secret/Variable | Bedeutung |
|---|---|
| `DEPLOY_HOST` | Zielserver |
| `DEPLOY_USER` | Benutzer für den Upload |
| `DEPLOY_PATH` | Zielordner auf dem Server |
| `SSH_PRIVATE_KEY` | privater Schlüssel für den Deploy-Zugriff |

Vereinfachtes Beispiel für einen Deploy-Schritt:

```bash
rsync -av --delete --exclude ".git" --exclude ".env" ./ "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/"
ssh "$DEPLOY_USER@$DEPLOY_HOST" "cd $DEPLOY_PATH && docker compose up -d"
```

Für den Unterricht reicht erstmal die Einordnung:

- Die Pipeline prüft.
- Der Deploy-Job veröffentlicht.
- Zugangsdaten liegen als Secrets in GitHub/GitLab, nicht im Repository.
- SSH/SFTP ist für solche Deployments sauberer als klassisches FTP.

## Rechtliche Platzhalter

`impressum.php` und `datenschutz.php` sind absichtlich nur Platzhalter. Für echte Veröffentlichungen müssen diese Inhalte fachlich und rechtlich korrekt ergänzt werden.

## Quellen und Hinweise

Verwendete Logos und Icons dienen im Unterricht als lokale Demo-Assets. Marken- und Urheberrechte verbleiben bei den jeweiligen Inhabern.

Dieses Unterrichtsprojekt wurde mit KI-Unterstützung bereinigt, strukturiert und dokumentiert. Die Inhalte sind für den Unterricht fachlich zu prüfen.
