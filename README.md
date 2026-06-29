# Startseite

Eine kleine PHP-/MariaDB-Webanwendung für eine persönliche Startseite mit Links, Profilen, Gruppen und Icons.

Dieses Repository ist gleichzeitig ein reales Deployment-Projekt und ein gutes CI/CD-Unterrichtsbeispiel: Es ist klein genug, um die Pipeline zu verstehen, aber realistisch genug für Secrets, Docker, SSH-Deployment und Smoke-Tests.

## Projektüberblick

Die Anwendung besteht aus:

- `index.php` als eigentliche Startseite
- `login.php` für Anmeldung und Registrierung
- `impressum.php` und `datenschutz.php` als öffentliche Pflichtseiten
- `config/` für Konfiguration, Bootstrap, UI- und Aktionslogik
- `assets/icons/` für lokale Icon-Dateien
- `docker-compose.yml` für PHP/Apache, MariaDB und phpMyAdmin
- `.gitlab-ci.yml` für die produktive GitLab-Pipeline
- `.github/workflows/ci.yml` als vergleichbare GitHub-Actions-Variante

## Lokaler Start

Beispielkonfiguration kopieren:

```bash
cp .env.example .env
```

Container starten:

```bash
docker compose up -d
```

Anwendung:

```text
http://localhost:28860
```

phpMyAdmin:

```text
http://localhost:28861
```

Stoppen:

```bash
docker compose down
```

## Wichtige Konfiguration

`.env` enthält produktive oder lokale Werte und wird nicht committet.

Wichtige Variablen:

| Variable | Bedeutung |
|---|---|
| `MARIADB_ROOT_PASSWORD` | Root-Passwort der MariaDB |
| `MARIADB_DATABASE` | Datenbankname |
| `MARIADB_USER` | Datenbankbenutzer |
| `MARIADB_PASSWORD` | Passwort des Datenbankbenutzers |
| `SSH_USER` | SSH-Benutzer im App-Container |
| `SSH_PASSWORD` | SSH-Passwort im App-Container |
| `SSH_PUBLIC_KEY` | optionaler Public Key für SSH-Login |

Für CI/CD gilt: echte Geheimnisse gehören in GitLab CI/CD Variables oder GitHub Secrets, nicht ins Repository.

## GitLab CI/CD

Die produktive Pipeline liegt in:

```text
.gitlab-ci.yml
```

Aktueller Ablauf:

```text
validate -> deploy -> smoke
```

### Validate

Der Validate-Job prüft:

- PHP-Syntax aller `.php`-Dateien
- vorhandene `.env.example`
- dass keine `.env` im Repository liegt
- zentrale Pflichtinhalte in Impressum und Datenschutz
- einfache Secret-Leak-Regel für Datenbankwerte

### Deploy

Der Deploy-Job überträgt das Repository per SSH in den laufenden App-Container:

```text
startseite@192.168.112.30:28862 -> /var/www/html
```

Dafür wird im GitLab-Projekt aktuell nur diese CI/CD-Variable benötigt:

```text
SSH_PASSWORD
```

Der Benutzer, Host und Port sind bewusst in der Pipeline fest eingetragen, weil sie für dieses Unterrichts- und Heimlabor-Setup nicht geheim sind.

### Smoke

Nach dem Deployment prüft die Pipeline öffentlich:

- `https://start.nik0.de/impressum.php`
- `https://start.nik0.de/datenschutz.php`

Der Smoke-Test beweist nicht, dass die ganze Anwendung fehlerfrei ist. Er beantwortet die wichtige Deployment-Frage: Ist die neue Version wirklich öffentlich angekommen?

## GitHub Actions

Die GitHub-Variante liegt in:

```text
.github/workflows/ci.yml
```

Sie zeigt denselben Grundgedanken mit GitHub-Syntax:

```text
validate -> optional deploy -> optional smoke
```

Für GitHub ist das Deployment absichtlich optional. Es ist als Demo für klassisches Webhosting gedacht und unterstützt `ftp`, `ftps` und `sftp`.

Es wird nur ausgeführt, wenn diese Repository Variables/Secrets gesetzt sind:

| Name | Typ | Bedeutung |
|---|---|---|
| `STARTSEITE_DEPLOY_ENABLED` | Variable | muss `true` sein |
| `STARTSEITE_DEPLOY_METHOD` | Variable | `ftp`, `ftps` oder `sftp` |
| `STARTSEITE_DEPLOY_HOST` | Variable | Zielhost |
| `STARTSEITE_DEPLOY_PORT` | Variable | z. B. `21`, `22` oder hosterspezifisch |
| `STARTSEITE_DEPLOY_USER` | Variable | Zielbenutzer |
| `STARTSEITE_DEPLOY_PATH` | Variable | Zielordner auf dem Webspace |
| `STARTSEITE_PUBLIC_URL` | Variable | öffentliche URL für den Smoke-Test |
| `STARTSEITE_DEPLOY_PASSWORD` | Secret | FTP-/SFTP-Passwort |

Damit lässt sich im Unterricht gut vergleichen:

- GitLab nutzt `.gitlab-ci.yml`
- GitHub nutzt `.github/workflows/ci.yml`
- die Konzepte sind gleich
- Syntax, Variablennamen und UI unterscheiden sich
- GitLab deployt per SSH in den Docker-Container
- GitHub deployt per FTP/FTPS/SFTP auf eine Demo-Webseite

## CI/CD-Variablen in GitLab und GitHub

### GitLab

In GitLab liegen die Variablen unter:

```text
Projekt -> Einstellungen -> CI/CD -> Variables
```

Für dieses private Projekt wird aktuell nur das SSH-Passwort als Variable benötigt, weil Host, Port und Benutzer in der GitLab-Pipeline fest eingetragen sind:

```text
SSH_PASSWORD
```

Der Screenshot zeigt die Stelle in GitLab:

![GitLab CI/CD-Variablen](assets/pictures/github-variables.png)

Empfehlung für echte Geheimnisse:

- `SSH_PASSWORD` maskieren
- `SSH_PASSWORD` schützen, wenn `main` ein geschützter Branch ist
- keine produktiven Werte in `.env.example` schreiben
- keine Screenshots mit sichtbaren Passwörtern veröffentlichen

### GitHub

In GitHub liegen Secrets und Variablen unter:

```text
Repository -> Settings -> Secrets and variables -> Actions
```

Für eine GitHub-Demo mit optionalem FTP-/SFTP-Deployment wären diese Werte sinnvoll:

| Name | Typ | Beispiel |
|---|---|---|
| `STARTSEITE_DEPLOY_ENABLED` | Variable | `true` |
| `STARTSEITE_DEPLOY_METHOD` | Variable | `ftps` |
| `STARTSEITE_DEPLOY_HOST` | Variable | `demo.example.org` |
| `STARTSEITE_DEPLOY_PORT` | Variable | `21`, `22` oder hosterspezifisch |
| `STARTSEITE_DEPLOY_USER` | Variable | `startseite` |
| `STARTSEITE_DEPLOY_PATH` | Variable | `/`, `/htdocs` oder `/public_html` |
| `STARTSEITE_PUBLIC_URL` | Variable | `https://demo.example.org` |
| `STARTSEITE_DEPLOY_PASSWORD` | Secret | echtes FTP-/SFTP-Passwort |

Empfehlung:

- `ftps` verwenden, wenn der Hoster klassisches FTP mit TLS anbietet
- `sftp` verwenden, wenn der Hoster SSH/SFTP anbietet
- `ftp` nur als historisches Negativbeispiel oder wenn der Hoster nichts anderes kann

Das GitHub-Bild kann später hier ergänzt werden:

```text
assets/pictures/github-variables.png
```

Wenn das Bild vorhanden ist, kann diese Markdown-Zeile ergänzt werden:

```markdown
![GitHub Actions Secrets und Variablen](assets/pictures/github-variables.png)
```

## Synchronisation mit dem GitHub-Demo

Das öffentliche Unterrichts-Demo liegt in einem eigenen Repository:

```text
https://github.com/stoykow/cicd_startseite_demo
```

Dieses private Projekt wird bewusst mit dem öffentlichen Demo synchron gehalten. Dadurch sehen Teilnehmende in GitHub denselben Projektstand, inklusive GitLab- und GitHub-Pipeline-Dateien.

Das Sync-Skript spiegelt das Projekt in das lokale Demo-Verzeichnis:

```powershell
.\scripts\sync-demo.ps1
```

```text
..\cicd_startseite_demo
```

Ausgeschlossen werden nur:

- `.env`
- `.git`

Damit werden auch `.gitlab-ci.yml`, `.github/workflows/ci.yml`, README, Impressum, Datenschutz und Bilder ins Demo übernommen.

Wichtig: Vor dem Push ins öffentliche GitHub-Demo prüfen, ob keine echten Geheimnisse in Dateien stehen. Passwörter und Tokens gehören nicht in Git, sondern in GitLab Variables oder GitHub Secrets.

Der normale Ablauf ist:

```powershell
.\scripts\sync-demo.ps1
cd ..\cicd_startseite_demo
git diff
git status
git add .
git commit -m "Sync demo from source project"
git push origin main
```

Wenn direkt committet und gepusht werden soll:

```powershell
.\scripts\sync-demo.ps1 -Push
```

Vor einem Push ins öffentliche Demo immer prüfen:

```powershell
git -C ..\cicd_startseite_demo diff
git -C ..\cicd_startseite_demo status
```

## Deployment-Arten im Unterricht

Dieses Projekt eignet sich, um mehrere Deployment-Arten praktisch einzuordnen.

| Deployment-Art | Beispiel | Einordnung |
|---|---|---|
| Manuelles Kopieren | Dateien per Explorer, SFTP oder `scp` kopieren | einfach, aber fehleranfällig |
| Git Pull auf Server | Server führt `git pull` im Webroot aus | nachvollziehbar, aber Server braucht Git-Zugriff |
| SSH-Tar-Deploy | Pipeline streamt ein Archiv per SSH und entpackt es | gut für kleine PHP-Projekte |
| Rsync-Deploy | Pipeline synchronisiert gezielt geänderte Dateien | effizient, gut kontrollierbar |
| Artefakt-Deploy | Pipeline baut ein Paket und lädt genau dieses aus | sauberer Release-Gedanke |
| Container-Deploy | Pipeline baut ein Image und startet es neu | moderner Standard für größere Setups |
| Blue-Green | zwei Umgebungen, Umschalten der aktiven Route | geringe Ausfallzeit, braucht mehr Infrastruktur |
| Canary | neue Version zuerst nur für einen Teil der Nutzer | gut mit Monitoring, komplexer Betrieb |
| Rollback | Rückkehr zur letzten guten Version | muss vor dem Fehler geplant sein |

Die aktuelle Pipeline nutzt bewusst einen einfachen SSH-Tar-Deploy. Das ist für dieses Projekt passend, weil die Anwendung direkt aus PHP-Dateien besteht und der Webroot als Docker-Volume eingebunden ist.

## Passende Tests für dieses Projekt

Für diese Anwendung sind folgende Tests sinnvoller als künstliche Unit-Tests:

| Test | Warum passend? |
|---|---|
| PHP-Syntaxcheck | findet kaputte PHP-Dateien schnell |
| Content-Smoke-Test | prüft, ob zentrale Pflichtseiten korrekt befüllt sind |
| Secret-Leak-Check | schützt vor versehentlich committeten Zugangsdaten |
| Deploy-Smoke-Test | prüft, ob die öffentliche Seite nach dem Deploy wirklich aktualisiert ist |
| Docker-Compose-Check | sinnvoll, wenn Docker im Runner verfügbar ist |

### Was prüft welcher Test?

#### PHP-Syntaxcheck

Der PHP-Syntaxcheck führt kein vollständiges Programm aus. Er prüft nur, ob alle PHP-Dateien grundsätzlich gültige PHP-Syntax enthalten.

Beispiel aus der Pipeline:

```bash
find . -path './.git' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

Wofür ist das gut?

- findet vergessene Semikolons
- findet kaputte Klammern
- findet ungültige PHP-Strukturen
- ist schnell und zuverlässig

Was findet der Test nicht?

- falsche Datenbankzugänge
- kaputte Logik
- fehlende Tabellen
- Fehler, die erst zur Laufzeit auftreten

Wenn dieser Test fehlschlägt, ist der Code meist syntaktisch kaputt und sollte nicht deployt werden.

#### Konfigurationscheck

Der Konfigurationscheck prüft, ob die Beispielkonfiguration vorhanden ist und ob keine echte `.env` im Repository liegt.

Beispiele:

```bash
test -f .env.example
test ! -f .env
```

Wofür ist das gut?

- neue Entwicklerinnen und Entwickler sehen, welche Variablen gebraucht werden
- produktive Passwörter bleiben außerhalb von Git
- lokale Konfiguration wird von Projektcode getrennt

Wenn dieser Test fehlschlägt, fehlt entweder die Vorlage oder eine echte `.env` wurde versehentlich eingecheckt.

#### Content-Smoke-Test

Der Content-Smoke-Test prüft, ob wichtige Seiten zentrale Pflichttexte enthalten.

Beispiele:

```bash
grep -q "Private Projektseite" impressum.php
grep -q "Datenschutzerklärung" datenschutz.php
grep -q "PHP-Session-Cookie" datenschutz.php
```

Wofür ist das gut?

- erkennt versehentlich zurückgesetzte Platzhalter
- prüft einfache fachliche Mindestanforderungen
- ist für kleine PHP-Seiten pragmatischer als ein großes Testframework

Was bedeutet „Smoke-Test“?

Ein Smoke-Test ist ein schneller Grundtest. Er beantwortet nicht jede Detailfrage, sondern prüft: Brennt es sofort offensichtlich?

Wenn dieser Test fehlschlägt, fehlen wichtige Inhalte oder eine Datei wurde falsch geändert.

#### Secret-Leak-Check

Der Secret-Leak-Check sucht nach Mustern, die nicht im Repository auftauchen sollen.

Beispiel:

```bash
grep -R --exclude=".env.example" --exclude=".gitlab-ci.yml" --exclude-dir=".github" --exclude-dir=".git" "MARIADB_ROOT_PASSWORD=" .
```

Wofür ist das gut?

- verhindert einfache Geheimnis-Leaks
- macht den Unterschied zwischen `.env.example` und echter `.env` sichtbar
- zeigt, warum Secrets in GitLab Variables oder GitHub Secrets gehören

Wichtig: Das ist nur ein einfacher Schutz. In produktiveren Projekten wären Werkzeuge wie `gitleaks` oder Secret-Scanning der Plattform sinnvoller.

Wenn dieser Test fehlschlägt, steht wahrscheinlich ein echter oder verdächtiger Geheimniswert im Repository.

#### Deploy-Test

Der Deploy-Schritt ist streng genommen kein Test, sondern eine Aktion. Trotzdem ist er Teil der Pipeline, weil nur erfolgreiche Validierung zum Deployment führen soll.

In diesem Projekt passiert:

```text
Repository packen -> per SSH übertragen -> in /var/www/html entpacken
```

Wofür ist das gut?

- zeigt ein echtes automatisiertes Deployment
- macht Secrets praktisch sichtbar
- verbindet Git, Pipeline, SSH und Docker-Volume

Wenn dieser Schritt fehlschlägt, liegt das meist an:

- falschem `SSH_PASSWORD`
- nicht erreichbarem Host oder Port
- falschem Zielpfad
- gestopptem Container
- fehlendem Runner-Netzwerkzugriff

#### Öffentlicher Smoke-Test nach dem Deployment

Nach dem Deployment ruft die Pipeline die produktive URL auf und prüft den ausgelieferten Inhalt.

Beispiele:

```bash
curl -fsS https://start.nik0.de/impressum.php | grep -q "Private Projektseite"
curl -fsS https://start.nik0.de/datenschutz.php | grep -q "PHP-Session-Cookie"
```

Wofür ist das gut?

- prüft nicht nur Dateien, sondern die echte öffentliche Auslieferung
- erkennt, wenn der Deploy auf den falschen Server ging
- erkennt, wenn Proxy, Container oder Webserver noch alte Inhalte liefern
- ist ein einfacher Einstieg in Monitoring und Release-Kontrolle

Wenn dieser Test fehlschlägt, kann der Code korrekt sein, aber die Veröffentlichung ist nicht korrekt angekommen.

### Merksatz für den Unterricht

```text
Validate prüft den Code vor dem Deployment.
Deploy veröffentlicht die geprüfte Version.
Smoke prüft nach dem Deployment, ob die Version wirklich sichtbar ist.
```

Mögliche spätere Erweiterungen:

- `docker compose config` als Strukturprüfung
- einfacher HTTP-Test gegen einen Testcontainer
- Linkprüfung für interne Seiten
- Security-Scan mit `gitleaks` oder vergleichbaren Werkzeugen
- Artefakt- oder Release-Ordner statt direkter Dateiübertragung

## Unterrichtsbezug

Dieses Projekt kann in einer CI/CD-Reihe so eingesetzt werden:

| Thema | Konkreter Bezug im Projekt |
|---|---|
| CI-Grundlagen | Validate-Stage mit Syntax- und Inhaltsprüfungen |
| Git | Änderung, Commit, Push, Pipeline-Auslösung |
| Pipeline-Aufbau | Stages, Jobs, Images, Variablen |
| Automatisiertes Testen | Smoke-Tests statt schwerer Testframeworks |
| Build/Artefakte | Diskussion: Warum gibt es hier noch kein echtes Build-Artefakt? |
| Deployment | SSH-Tar-Deploy in Docker-Volume |
| Monitoring/Logging | Job-Logs und öffentliche Smoke-Tests |
| Sicherheit | `.env`, GitLab Variables, GitHub Secrets, Passwort vs. SSH-Key |

## GitLab vs. GitHub

Beide Plattformen lösen dasselbe Grundproblem:

```text
Codeänderung -> Prüfung -> optionales Deployment -> Kontrolle
```

GitLab:

- Pipeline-Datei: `.gitlab-ci.yml`
- Variablen: Settings -> CI/CD -> Variables
- Runner: eigener GitLab Runner im Docker-Setup
- gut für selbst gehostete Umgebung

GitHub:

- Workflow-Datei: `.github/workflows/ci.yml`
- Secrets/Variables: Settings -> Secrets and variables -> Actions
- Runner: GitHub-hosted oder self-hosted Runner
- gut für öffentliche Demos und Vergleich der Plattformen

## Rechtliche Seiten

`impressum.php` und `datenschutz.php` sind für dieses konkrete private Projekt ausgefüllt. Für andere Projekte müssen die Angaben fachlich und rechtlich neu geprüft werden.

## Quellen und Hinweise

Verwendete Logos und Icons dienen als lokale Assets. Marken- und Urheberrechte verbleiben bei den jeweiligen Inhabern.
