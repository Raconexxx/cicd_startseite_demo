# Startseite

Eine kleine PHP-/MariaDB-Webanwendung fÃ¼r eine persÃ¶nliche Startseite mit Links, Profilen, Gruppen und Icons.

Dieses Repository ist gleichzeitig ein reales Deployment-Projekt und ein gutes CI/CD-Unterrichtsbeispiel: Es ist klein genug, um die Pipeline zu verstehen, aber realistisch genug fÃ¼r Secrets, Docker, SSH-Deployment und Smoke-Tests.

## ProjektÃ¼berblick

Die Anwendung besteht aus:

- `index.php` als eigentliche Startseite
- `login.php` fÃ¼r Anmeldung und Registrierung
- `impressum.php` und `datenschutz.php` als Ã¶ffentliche Pflichtseiten
- `config/` fÃ¼r Konfiguration, Bootstrap, UI- und Aktionslogik
- `assets/icons/` fÃ¼r lokale Icon-Dateien
- `docker-compose.yml` fÃ¼r PHP/Apache, MariaDB und phpMyAdmin
- `.gitlab-ci.yml` fÃ¼r die produktive GitLab-Pipeline
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

`.env` enthÃ¤lt produktive oder lokale Werte und wird nicht committet.

Wichtige Variablen:

| Variable | Bedeutung |
|---|---|
| `MARIADB_ROOT_PASSWORD` | Root-Passwort der MariaDB |
| `MARIADB_DATABASE` | Datenbankname |
| `MARIADB_USER` | Datenbankbenutzer |
| `MARIADB_PASSWORD` | Passwort des Datenbankbenutzers |
| `SSH_USER` | SSH-Benutzer im App-Container |
| `SSH_PASSWORD` | SSH-Passwort im App-Container |
| `SSH_PUBLIC_KEY` | optionaler Public Key fÃ¼r SSH-Login |

FÃ¼r CI/CD gilt: echte Geheimnisse gehÃ¶ren in GitLab CI/CD Variables oder GitHub Secrets, nicht ins Repository.

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

Der Validate-Job prÃ¼ft:

- PHP-Syntax aller `.php`-Dateien
- vorhandene `.env.example`
- dass keine `.env` im Repository liegt
- zentrale Pflichtinhalte in Impressum und Datenschutz
- einfache Secret-Leak-Regel fÃ¼r Datenbankwerte

### Deploy

Der Deploy-Job Ã¼bertrÃ¤gt das Repository per SSH in den laufenden App-Container:

```text
startseite@192.168.112.30:28862 -> /var/www/html
```

DafÃ¼r wird im GitLab-Projekt aktuell nur diese CI/CD-Variable benÃ¶tigt:

```text
SSH_PASSWORD
```

Der Benutzer, Host und Port sind bewusst in der Pipeline fest eingetragen, weil sie fÃ¼r dieses Unterrichts- und Heimlabor-Setup nicht geheim sind.

### Smoke

Nach dem Deployment prÃ¼ft die Pipeline Ã¶ffentlich:

- `https://start.nik0.de/impressum.php`
- `https://start.nik0.de/datenschutz.php`

Der Smoke-Test beweist nicht, dass die ganze Anwendung fehlerfrei ist. Er beantwortet die wichtige Deployment-Frage: Ist die neue Version wirklich Ã¶ffentlich angekommen?

## GitHub Actions

Die GitHub-Variante liegt in:

```text
.github/workflows/ci.yml
```

Sie zeigt denselben Grundgedanken mit GitHub-Syntax:

```text
validate -> optional deploy -> optional smoke
```

FÃ¼r GitHub ist das Deployment absichtlich optional. Es ist als Demo fÃ¼r klassisches Webhosting gedacht und unterstÃ¼tzt `ftp`, `ftps` und `sftp`.

Es wird nur ausgefÃ¼hrt, wenn diese Repository Variables/Secrets gesetzt sind:

| Name | Typ | Bedeutung |
|---|---|---|
| `STARTSEITE_DEPLOY_ENABLED` | Variable | muss `true` sein |
| `STARTSEITE_DEPLOY_METHOD` | Variable | `ftp`, `ftps` oder `sftp` |
| `STARTSEITE_DEPLOY_HOST` | Variable | Zielhost |
| `STARTSEITE_DEPLOY_PORT` | Variable | z. B. `21`, `22` oder hosterspezifisch |
| `STARTSEITE_DEPLOY_USER` | Variable | Zielbenutzer |
| `STARTSEITE_DEPLOY_PATH` | Variable | Zielordner auf dem Webspace |
| `STARTSEITE_PUBLIC_URL` | Variable | Ã¶ffentliche URL fÃ¼r den Smoke-Test |
| `STARTSEITE_DEPLOY_PASSWORD` | Secret | FTP-/SFTP-Passwort |

Damit lÃ¤sst sich im Unterricht gut vergleichen:

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

FÃ¼r dieses private Projekt wird aktuell nur das SSH-Passwort als Variable benÃ¶tigt, weil Host, Port und Benutzer in der GitLab-Pipeline fest eingetragen sind:

```text
SSH_PASSWORD
```

Der Screenshot zeigt die Stelle in GitLab:

![GitLab CI/CD-Variablen](assets/pictures/gitlab-variables.png)

Empfehlung fÃ¼r echte Geheimnisse:

- `SSH_PASSWORD` maskieren
- `SSH_PASSWORD` schÃ¼tzen, wenn `main` ein geschÃ¼tzter Branch ist
- keine produktiven Werte in `.env.example` schreiben
- keine Screenshots mit sichtbaren PasswÃ¶rtern verÃ¶ffentlichen

### GitHub

In GitHub liegen Secrets und Variablen unter:

```text
Repository -> Settings -> Secrets and variables -> Actions
```

FÃ¼r eine GitHub-Demo mit optionalem FTP-/SFTP-Deployment wÃ¤ren diese Werte sinnvoll:

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

Das GitHub-Bild kann spÃ¤ter hier ergÃ¤nzt werden:

```text
assets/pictures/github-variables.png
```

Wenn das Bild vorhanden ist, kann diese Markdown-Zeile ergÃ¤nzt werden:

```markdown
![GitHub Actions Secrets und Variablen](assets/pictures/github-variables.png)
```

## Synchronisation mit dem GitHub-Demo

Das Ã¶ffentliche Unterrichts-Demo liegt in einem eigenen Repository:

```text
https://github.com/stoykow/cicd_startseite_demo
```

Dieses private Projekt wird bewusst mit dem Ã¶ffentlichen Demo synchron gehalten. Dadurch sehen Teilnehmende in GitHub denselben Projektstand, inklusive GitLab- und GitHub-Pipeline-Dateien.

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

Damit werden auch `.gitlab-ci.yml`, `.github/workflows/ci.yml`, README, Impressum, Datenschutz und Bilder ins Demo Ã¼bernommen.

Wichtig: Vor dem Push ins Ã¶ffentliche GitHub-Demo prÃ¼fen, ob keine echten Geheimnisse in Dateien stehen. PasswÃ¶rter und Tokens gehÃ¶ren nicht in Git, sondern in GitLab Variables oder GitHub Secrets.

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

Vor einem Push ins Ã¶ffentliche Demo immer prÃ¼fen:

```powershell
git -C ..\cicd_startseite_demo diff
git -C ..\cicd_startseite_demo status
```

## Deployment-Arten im Unterricht

Dieses Projekt eignet sich, um mehrere Deployment-Arten praktisch einzuordnen.

| Deployment-Art | Beispiel | Einordnung |
|---|---|---|
| Manuelles Kopieren | Dateien per Explorer, SFTP oder `scp` kopieren | einfach, aber fehleranfÃ¤llig |
| Git Pull auf Server | Server fÃ¼hrt `git pull` im Webroot aus | nachvollziehbar, aber Server braucht Git-Zugriff |
| SSH-Tar-Deploy | Pipeline streamt ein Archiv per SSH und entpackt es | gut fÃ¼r kleine PHP-Projekte |
| Rsync-Deploy | Pipeline synchronisiert gezielt geÃ¤nderte Dateien | effizient, gut kontrollierbar |
| Artefakt-Deploy | Pipeline baut ein Paket und lÃ¤dt genau dieses aus | sauberer Release-Gedanke |
| Container-Deploy | Pipeline baut ein Image und startet es neu | moderner Standard fÃ¼r grÃ¶ÃŸere Setups |
| Blue-Green | zwei Umgebungen, Umschalten der aktiven Route | geringe Ausfallzeit, braucht mehr Infrastruktur |
| Canary | neue Version zuerst nur fÃ¼r einen Teil der Nutzer | gut mit Monitoring, komplexer Betrieb |
| Rollback | RÃ¼ckkehr zur letzten guten Version | muss vor dem Fehler geplant sein |

Die aktuelle Pipeline nutzt bewusst einen einfachen SSH-Tar-Deploy. Das ist fÃ¼r dieses Projekt passend, weil die Anwendung direkt aus PHP-Dateien besteht und der Webroot als Docker-Volume eingebunden ist.

## Passende Tests fÃ¼r dieses Projekt

FÃ¼r diese Anwendung sind folgende Tests sinnvoller als kÃ¼nstliche Unit-Tests:

| Test | Warum passend? |
|---|---|
| PHP-Syntaxcheck | findet kaputte PHP-Dateien schnell |
| Content-Smoke-Test | prÃ¼ft, ob zentrale Pflichtseiten korrekt befÃ¼llt sind |
| Secret-Leak-Check | schÃ¼tzt vor versehentlich committeten Zugangsdaten |
| Deploy-Smoke-Test | prÃ¼ft, ob die Ã¶ffentliche Seite nach dem Deploy wirklich aktualisiert ist |
| Docker-Compose-Check | sinnvoll, wenn Docker im Runner verfÃ¼gbar ist |

### Was prÃ¼ft welcher Test?

#### PHP-Syntaxcheck

Der PHP-Syntaxcheck fÃ¼hrt kein vollstÃ¤ndiges Programm aus. Er prÃ¼ft nur, ob alle PHP-Dateien grundsÃ¤tzlich gÃ¼ltige PHP-Syntax enthalten.

Beispiel aus der Pipeline:

```bash
find . -path './.git' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

WofÃ¼r ist das gut?

- findet vergessene Semikolons
- findet kaputte Klammern
- findet ungÃ¼ltige PHP-Strukturen
- ist schnell und zuverlÃ¤ssig

Was findet der Test nicht?

- falsche DatenbankzugÃ¤nge
- kaputte Logik
- fehlende Tabellen
- Fehler, die erst zur Laufzeit auftreten

Wenn dieser Test fehlschlÃ¤gt, ist der Code meist syntaktisch kaputt und sollte nicht deployt werden.

#### Konfigurationscheck

Der Konfigurationscheck prÃ¼ft, ob die Beispielkonfiguration vorhanden ist und ob keine echte `.env` im Repository liegt.

Beispiele:

```bash
test -f .env.example
test ! -f .env
```

WofÃ¼r ist das gut?

- neue Entwicklerinnen und Entwickler sehen, welche Variablen gebraucht werden
- produktive PasswÃ¶rter bleiben auÃŸerhalb von Git
- lokale Konfiguration wird von Projektcode getrennt

Wenn dieser Test fehlschlÃ¤gt, fehlt entweder die Vorlage oder eine echte `.env` wurde versehentlich eingecheckt.

#### Content-Smoke-Test

Der Content-Smoke-Test prÃ¼ft, ob wichtige Seiten zentrale Pflichttexte enthalten.

Beispiele:

```bash
grep -q "Private Projektseite" impressum.php
grep -q "DatenschutzerklÃ¤rung" datenschutz.php
grep -q "PHP-Session-Cookie" datenschutz.php
```

WofÃ¼r ist das gut?

- erkennt versehentlich zurÃ¼ckgesetzte Platzhalter
- prÃ¼ft einfache fachliche Mindestanforderungen
- ist fÃ¼r kleine PHP-Seiten pragmatischer als ein groÃŸes Testframework

Was bedeutet â€žSmoke-Testâ€œ?

Ein Smoke-Test ist ein schneller Grundtest. Er beantwortet nicht jede Detailfrage, sondern prÃ¼ft: Brennt es sofort offensichtlich?

Wenn dieser Test fehlschlÃ¤gt, fehlen wichtige Inhalte oder eine Datei wurde falsch geÃ¤ndert.

#### Secret-Leak-Check

Der Secret-Leak-Check sucht nach Mustern, die nicht im Repository auftauchen sollen.

Beispiel:

```bash
grep -R --exclude=".env.example" --exclude=".gitlab-ci.yml" --exclude-dir=".github" --exclude-dir=".git" "MARIADB_ROOT_PASSWORD=" .
```

WofÃ¼r ist das gut?

- verhindert einfache Geheimnis-Leaks
- macht den Unterschied zwischen `.env.example` und echter `.env` sichtbar
- zeigt, warum Secrets in GitLab Variables oder GitHub Secrets gehÃ¶ren

Wichtig: Das ist nur ein einfacher Schutz. In produktiveren Projekten wÃ¤ren Werkzeuge wie `gitleaks` oder Secret-Scanning der Plattform sinnvoller.

Wenn dieser Test fehlschlÃ¤gt, steht wahrscheinlich ein echter oder verdÃ¤chtiger Geheimniswert im Repository.

#### Deploy-Test

Der Deploy-Schritt ist streng genommen kein Test, sondern eine Aktion. Trotzdem ist er Teil der Pipeline, weil nur erfolgreiche Validierung zum Deployment fÃ¼hren soll.

In diesem Projekt passiert:

```text
Repository packen -> per SSH Ã¼bertragen -> in /var/www/html entpacken
```

WofÃ¼r ist das gut?

- zeigt ein echtes automatisiertes Deployment
- macht Secrets praktisch sichtbar
- verbindet Git, Pipeline, SSH und Docker-Volume

Wenn dieser Schritt fehlschlÃ¤gt, liegt das meist an:

- falschem `SSH_PASSWORD`
- nicht erreichbarem Host oder Port
- falschem Zielpfad
- gestopptem Container
- fehlendem Runner-Netzwerkzugriff

#### Ã–ffentlicher Smoke-Test nach dem Deployment

Nach dem Deployment ruft die Pipeline die produktive URL auf und prÃ¼ft den ausgelieferten Inhalt.

Beispiele:

```bash
curl -fsS https://start.nik0.de/impressum.php | grep -q "Private Projektseite"
curl -fsS https://start.nik0.de/datenschutz.php | grep -q "PHP-Session-Cookie"
```

WofÃ¼r ist das gut?

- prÃ¼ft nicht nur Dateien, sondern die echte Ã¶ffentliche Auslieferung
- erkennt, wenn der Deploy auf den falschen Server ging
- erkennt, wenn Proxy, Container oder Webserver noch alte Inhalte liefern
- ist ein einfacher Einstieg in Monitoring und Release-Kontrolle

Wenn dieser Test fehlschlÃ¤gt, kann der Code korrekt sein, aber die VerÃ¶ffentlichung ist nicht korrekt angekommen.

### Merksatz fÃ¼r den Unterricht

```text
Validate prÃ¼ft den Code vor dem Deployment.
Deploy verÃ¶ffentlicht die geprÃ¼fte Version.
Smoke prÃ¼ft nach dem Deployment, ob die Version wirklich sichtbar ist.
```

MÃ¶gliche spÃ¤tere Erweiterungen:

- `docker compose config` als StrukturprÃ¼fung
- einfacher HTTP-Test gegen einen Testcontainer
- LinkprÃ¼fung fÃ¼r interne Seiten
- Security-Scan mit `gitleaks` oder vergleichbaren Werkzeugen
- Artefakt- oder Release-Ordner statt direkter DateiÃ¼bertragung

## Unterrichtsbezug

Dieses Projekt kann in einer CI/CD-Reihe so eingesetzt werden:

| Thema | Konkreter Bezug im Projekt |
|---|---|
| CI-Grundlagen | Validate-Stage mit Syntax- und InhaltsprÃ¼fungen |
| Git | Ã„nderung, Commit, Push, Pipeline-AuslÃ¶sung |
| Pipeline-Aufbau | Stages, Jobs, Images, Variablen |
| Automatisiertes Testen | Smoke-Tests statt schwerer Testframeworks |
| Build/Artefakte | Diskussion: Warum gibt es hier noch kein echtes Build-Artefakt? |
| Deployment | SSH-Tar-Deploy in Docker-Volume |
| Monitoring/Logging | Job-Logs und Ã¶ffentliche Smoke-Tests |
| Sicherheit | `.env`, GitLab Variables, GitHub Secrets, Passwort vs. SSH-Key |

## GitLab vs. GitHub

Beide Plattformen lÃ¶sen dasselbe Grundproblem:

```text
CodeÃ¤nderung -> PrÃ¼fung -> optionales Deployment -> Kontrolle
```

GitLab:

- Pipeline-Datei: `.gitlab-ci.yml`
- Variablen: Settings -> CI/CD -> Variables
- Runner: eigener GitLab Runner im Docker-Setup
- gut fÃ¼r selbst gehostete Umgebung

GitHub:

- Workflow-Datei: `.github/workflows/ci.yml`
- Secrets/Variables: Settings -> Secrets and variables -> Actions
- Runner: GitHub-hosted oder self-hosted Runner
- gut fÃ¼r Ã¶ffentliche Demos und Vergleich der Plattformen

## Rechtliche Seiten

`impressum.php` und `datenschutz.php` sind fÃ¼r dieses konkrete private Projekt ausgefÃ¼llt. FÃ¼r andere Projekte mÃ¼ssen die Angaben fachlich und rechtlich neu geprÃ¼ft werden.

## Quellen und Hinweise

Verwendete Logos und Icons dienen als lokale Assets. Marken- und Urheberrechte verbleiben bei den jeweiligen Inhabern.
