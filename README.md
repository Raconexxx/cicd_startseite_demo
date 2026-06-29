# Startseite

Eine kleine PHP-/MariaDB-Webanwendung fÃƒÂ¼r eine persÃƒÂ¶nliche Startseite mit Links, Profilen, Gruppen und Icons.

Dieses Repository ist gleichzeitig ein reales Deployment-Projekt und ein gutes CI/CD-Unterrichtsbeispiel: Es ist klein genug, um die Pipeline zu verstehen, aber realistisch genug fÃƒÂ¼r Secrets, Docker, SSH-Deployment und Smoke-Tests.

## ProjektÃƒÂ¼berblick

Die Anwendung besteht aus:

- `index.php` als eigentliche Startseite
- `login.php` fÃƒÂ¼r Anmeldung und Registrierung
- `impressum.php` und `datenschutz.php` als ÃƒÂ¶ffentliche Pflichtseiten
- `config/` fÃƒÂ¼r Konfiguration, Bootstrap, UI- und Aktionslogik
- `assets/icons/` fÃƒÂ¼r lokale Icon-Dateien
- `docker-compose.yml` fÃƒÂ¼r PHP/Apache, MariaDB und phpMyAdmin
- `.gitlab-ci.yml` fÃƒÂ¼r die produktive GitLab-Pipeline
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

`.env` enthÃƒÂ¤lt produktive oder lokale Werte und wird nicht committet.

Wichtige Variablen:

| Variable | Bedeutung |
|---|---|
| `MARIADB_ROOT_PASSWORD` | Root-Passwort der MariaDB |
| `MARIADB_DATABASE` | Datenbankname |
| `MARIADB_USER` | Datenbankbenutzer |
| `MARIADB_PASSWORD` | Passwort des Datenbankbenutzers |
| `SSH_USER` | SSH-Benutzer im App-Container |
| `SSH_PASSWORD` | SSH-Passwort im App-Container |
| `SSH_PUBLIC_KEY` | optionaler Public Key fÃƒÂ¼r SSH-Login |

FÃƒÂ¼r CI/CD gilt: echte Geheimnisse gehÃƒÂ¶ren in GitLab CI/CD Variables oder GitHub Secrets, nicht ins Repository.

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

Der Validate-Job prÃƒÂ¼ft:

- PHP-Syntax aller `.php`-Dateien
- vorhandene `.env.example`
- dass keine `.env` im Repository liegt
- zentrale Pflichtinhalte in Impressum und Datenschutz
- einfache Secret-Leak-Regel fÃƒÂ¼r Datenbankwerte

### Deploy

Der Deploy-Job ÃƒÂ¼bertrÃƒÂ¤gt das Repository per SSH in den laufenden App-Container:

```text
startseite@192.168.112.30:28862 -> /var/www/html
```

DafÃƒÂ¼r wird im GitLab-Projekt aktuell nur diese CI/CD-Variable benÃƒÂ¶tigt:

```text
SSH_PASSWORD
```

Der Benutzer, Host und Port sind bewusst in der Pipeline fest eingetragen, weil sie fÃƒÂ¼r dieses Unterrichts- und Heimlabor-Setup nicht geheim sind.

### Smoke

Nach dem Deployment prÃƒÂ¼ft die Pipeline ÃƒÂ¶ffentlich:

- `https://start.nik0.de/impressum.php`
- `https://start.nik0.de/datenschutz.php`

Der Smoke-Test beweist nicht, dass die ganze Anwendung fehlerfrei ist. Er beantwortet die wichtige Deployment-Frage: Ist die neue Version wirklich ÃƒÂ¶ffentlich angekommen?

## GitHub Actions

Die GitHub-Variante liegt in:

```text
.github/workflows/ci.yml
```

Sie zeigt denselben Grundgedanken mit GitHub-Syntax:

```text
validate -> deploy -> smoke
```

Für GitHub ist das Deployment als Demo für klassisches Webhosting gedacht. Die YAML ist aktuell auf `ftps`, Port `21` und Zielpfad `/` eingestellt. Wenn ein Hoster stattdessen `sftp` oder einen anderen Pfad braucht, wird das bewusst in der Workflow-Datei geändert.

Für das GitHub-Deployment reichen vier Einträge:

| Name | Typ | Bedeutung |
|---|---|---|
| `STARTSEITE_DEPLOY_HOST` | Variable | Zielhost |
| `STARTSEITE_PUBLIC_URL` | Variable | öffentliche URL für den Smoke-Test |
| `STARTSEITE_DEPLOY_USER` | Secret | Zielbenutzer |
| `STARTSEITE_DEPLOY_PASSWORD` | Secret | FTP-/SFTP-Passwort |

Damit lässt sich im Unterricht gut vergleichen:

- GitLab nutzt `.gitlab-ci.yml`
- GitHub nutzt `.github/workflows/ci.yml`
- die Konzepte sind gleich
- Syntax, Variablennamen und UI unterscheiden sich
- GitLab deployt per SSH in den Docker-Container
- GitHub deployt per FTPS auf eine Demo-Webseite
## CI/CD-Variablen in GitLab und GitHub

### GitLab

In GitLab liegen die Variablen unter:

```text
Projekt -> Einstellungen -> CI/CD -> Variables
```

FÃƒÂ¼r dieses private Projekt wird aktuell nur das SSH-Passwort als Variable benÃƒÂ¶tigt, weil Host, Port und Benutzer in der GitLab-Pipeline fest eingetragen sind:

```text
SSH_PASSWORD
```

Der Screenshot zeigt die Stelle in GitLab:

![GitLab CI/CD-Variablen](assets/pictures/gitlab-variables.png)

Empfehlung fÃƒÂ¼r echte Geheimnisse:

- `SSH_PASSWORD` maskieren
- `SSH_PASSWORD` schÃƒÂ¼tzen, wenn `main` ein geschÃƒÂ¼tzter Branch ist
- keine produktiven Werte in `.env.example` schreiben
- keine Screenshots mit sichtbaren PasswÃƒÂ¶rtern verÃƒÂ¶ffentlichen

### GitHub

In GitHub liegen Secrets und Variablen unter:

```text
Repository -> Settings -> Secrets and variables -> Actions
```

Für eine GitHub-Demo mit FTP-/SFTP-Deployment reichen diese Werte:

| Name | Typ | Beispiel |
|---|---|---|
| `STARTSEITE_DEPLOY_HOST` | Variable | `w021c13f.kasserver.com` |
| `STARTSEITE_PUBLIC_URL` | Variable | `http://meine-startseite.org/` |
| `STARTSEITE_DEPLOY_USER` | Secret | FTP-/SFTP-Benutzer |
| `STARTSEITE_DEPLOY_PASSWORD` | Secret | echtes FTP-/SFTP-Passwort |

In der Workflow-Datei stehen fest:

| Wert | Aktuell |
|---|---|
| Deployment-Methode | `ftps` |
| Port | `21` |
| Zielpfad | `/` |

Bei einem anderen Hoster werden diese Werte in `.github/workflows/ci.yml` angepasst.

Empfehlung: `ftps` verwenden, wenn der Hoster klassisches FTP mit TLS anbietet. `sftp` verwenden, wenn der Hoster SSH/SFTP anbietet. `ftp` nur als historisches Negativbeispiel oder wenn der Hoster nichts anderes kann.

GitHub-Secrets:

![GitHub Actions Secrets](assets/pictures/github-secrets.png)

GitHub-Variables:

![GitHub Actions Variables](assets/pictures/github-variables.png)
## Synchronisation mit dem GitHub-Demo

Das ÃƒÂ¶ffentliche Unterrichts-Demo liegt in einem eigenen Repository:

```text
https://github.com/stoykow/cicd_startseite_demo
```

Dieses private Projekt wird bewusst mit dem ÃƒÂ¶ffentlichen Demo synchron gehalten. Dadurch sehen Teilnehmende in GitHub denselben Projektstand, inklusive GitLab- und GitHub-Pipeline-Dateien.

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

Damit werden auch `.gitlab-ci.yml`, `.github/workflows/ci.yml`, README, Impressum, Datenschutz und Bilder ins Demo ÃƒÂ¼bernommen.

Wichtig: Vor dem Push ins ÃƒÂ¶ffentliche GitHub-Demo prÃƒÂ¼fen, ob keine echten Geheimnisse in Dateien stehen. PasswÃƒÂ¶rter und Tokens gehÃƒÂ¶ren nicht in Git, sondern in GitLab Variables oder GitHub Secrets.

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

Vor einem Push ins ÃƒÂ¶ffentliche Demo immer prÃƒÂ¼fen:

```powershell
git -C ..\cicd_startseite_demo diff
git -C ..\cicd_startseite_demo status
```

## Deployment-Arten im Unterricht

Dieses Projekt eignet sich, um mehrere Deployment-Arten praktisch einzuordnen.

| Deployment-Art | Beispiel | Einordnung |
|---|---|---|
| Manuelles Kopieren | Dateien per Explorer, SFTP oder `scp` kopieren | einfach, aber fehleranfÃƒÂ¤llig |
| Git Pull auf Server | Server fÃƒÂ¼hrt `git pull` im Webroot aus | nachvollziehbar, aber Server braucht Git-Zugriff |
| SSH-Tar-Deploy | Pipeline streamt ein Archiv per SSH und entpackt es | gut fÃƒÂ¼r kleine PHP-Projekte |
| Rsync-Deploy | Pipeline synchronisiert gezielt geÃƒÂ¤nderte Dateien | effizient, gut kontrollierbar |
| Artefakt-Deploy | Pipeline baut ein Paket und lÃƒÂ¤dt genau dieses aus | sauberer Release-Gedanke |
| Container-Deploy | Pipeline baut ein Image und startet es neu | moderner Standard fÃƒÂ¼r grÃƒÂ¶ÃƒÅ¸ere Setups |
| Blue-Green | zwei Umgebungen, Umschalten der aktiven Route | geringe Ausfallzeit, braucht mehr Infrastruktur |
| Canary | neue Version zuerst nur fÃƒÂ¼r einen Teil der Nutzer | gut mit Monitoring, komplexer Betrieb |
| Rollback | RÃƒÂ¼ckkehr zur letzten guten Version | muss vor dem Fehler geplant sein |

Die aktuelle Pipeline nutzt bewusst einen einfachen SSH-Tar-Deploy. Das ist fÃƒÂ¼r dieses Projekt passend, weil die Anwendung direkt aus PHP-Dateien besteht und der Webroot als Docker-Volume eingebunden ist.

## Passende Tests fÃƒÂ¼r dieses Projekt

FÃƒÂ¼r diese Anwendung sind folgende Tests sinnvoller als kÃƒÂ¼nstliche Unit-Tests:

| Test | Warum passend? |
|---|---|
| PHP-Syntaxcheck | findet kaputte PHP-Dateien schnell |
| Content-Smoke-Test | prÃƒÂ¼ft, ob zentrale Pflichtseiten korrekt befÃƒÂ¼llt sind |
| Secret-Leak-Check | schÃƒÂ¼tzt vor versehentlich committeten Zugangsdaten |
| Deploy-Smoke-Test | prÃƒÂ¼ft, ob die ÃƒÂ¶ffentliche Seite nach dem Deploy wirklich aktualisiert ist |
| Docker-Compose-Check | sinnvoll, wenn Docker im Runner verfÃƒÂ¼gbar ist |

### Was prÃƒÂ¼ft welcher Test?

#### PHP-Syntaxcheck

Der PHP-Syntaxcheck fÃƒÂ¼hrt kein vollstÃƒÂ¤ndiges Programm aus. Er prÃƒÂ¼ft nur, ob alle PHP-Dateien grundsÃƒÂ¤tzlich gÃƒÂ¼ltige PHP-Syntax enthalten.

Beispiel aus der Pipeline:

```bash
find . -path './.git' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

WofÃƒÂ¼r ist das gut?

- findet vergessene Semikolons
- findet kaputte Klammern
- findet ungÃƒÂ¼ltige PHP-Strukturen
- ist schnell und zuverlÃƒÂ¤ssig

Was findet der Test nicht?

- falsche DatenbankzugÃƒÂ¤nge
- kaputte Logik
- fehlende Tabellen
- Fehler, die erst zur Laufzeit auftreten

Wenn dieser Test fehlschlÃƒÂ¤gt, ist der Code meist syntaktisch kaputt und sollte nicht deployt werden.

#### Konfigurationscheck

Der Konfigurationscheck prÃƒÂ¼ft, ob die Beispielkonfiguration vorhanden ist und ob keine echte `.env` im Repository liegt.

Beispiele:

```bash
test -f .env.example
test ! -f .env
```

WofÃƒÂ¼r ist das gut?

- neue Entwicklerinnen und Entwickler sehen, welche Variablen gebraucht werden
- produktive PasswÃƒÂ¶rter bleiben auÃƒÅ¸erhalb von Git
- lokale Konfiguration wird von Projektcode getrennt

Wenn dieser Test fehlschlÃƒÂ¤gt, fehlt entweder die Vorlage oder eine echte `.env` wurde versehentlich eingecheckt.

#### Content-Smoke-Test

Der Content-Smoke-Test prÃƒÂ¼ft, ob wichtige Seiten zentrale Pflichttexte enthalten.

Beispiele:

```bash
grep -q "Private Projektseite" impressum.php
grep -q "DatenschutzerklÃƒÂ¤rung" datenschutz.php
grep -q "PHP-Session-Cookie" datenschutz.php
```

WofÃƒÂ¼r ist das gut?

- erkennt versehentlich zurÃƒÂ¼ckgesetzte Platzhalter
- prÃƒÂ¼ft einfache fachliche Mindestanforderungen
- ist fÃƒÂ¼r kleine PHP-Seiten pragmatischer als ein groÃƒÅ¸es Testframework

Was bedeutet Ã¢â‚¬Å¾Smoke-TestÃ¢â‚¬Å“?

Ein Smoke-Test ist ein schneller Grundtest. Er beantwortet nicht jede Detailfrage, sondern prÃƒÂ¼ft: Brennt es sofort offensichtlich?

Wenn dieser Test fehlschlÃƒÂ¤gt, fehlen wichtige Inhalte oder eine Datei wurde falsch geÃƒÂ¤ndert.

#### Secret-Leak-Check

Der Secret-Leak-Check sucht nach Mustern, die nicht im Repository auftauchen sollen.

Beispiel:

```bash
grep -R --exclude=".env.example" --exclude=".gitlab-ci.yml" --exclude-dir=".github" --exclude-dir=".git" "MARIADB_ROOT_PASSWORD=" .
```

WofÃƒÂ¼r ist das gut?

- verhindert einfache Geheimnis-Leaks
- macht den Unterschied zwischen `.env.example` und echter `.env` sichtbar
- zeigt, warum Secrets in GitLab Variables oder GitHub Secrets gehÃƒÂ¶ren

Wichtig: Das ist nur ein einfacher Schutz. In produktiveren Projekten wÃƒÂ¤ren Werkzeuge wie `gitleaks` oder Secret-Scanning der Plattform sinnvoller.

Wenn dieser Test fehlschlÃƒÂ¤gt, steht wahrscheinlich ein echter oder verdÃƒÂ¤chtiger Geheimniswert im Repository.

#### Deploy-Test

Der Deploy-Schritt ist streng genommen kein Test, sondern eine Aktion. Trotzdem ist er Teil der Pipeline, weil nur erfolgreiche Validierung zum Deployment fÃƒÂ¼hren soll.

In diesem Projekt passiert:

```text
Repository packen -> per SSH ÃƒÂ¼bertragen -> in /var/www/html entpacken
```

WofÃƒÂ¼r ist das gut?

- zeigt ein echtes automatisiertes Deployment
- macht Secrets praktisch sichtbar
- verbindet Git, Pipeline, SSH und Docker-Volume

Wenn dieser Schritt fehlschlÃƒÂ¤gt, liegt das meist an:

- falschem `SSH_PASSWORD`
- nicht erreichbarem Host oder Port
- falschem Zielpfad
- gestopptem Container
- fehlendem Runner-Netzwerkzugriff

#### Ãƒâ€“ffentlicher Smoke-Test nach dem Deployment

Nach dem Deployment ruft die Pipeline die produktive URL auf und prÃƒÂ¼ft den ausgelieferten Inhalt.

Beispiele:

```bash
curl -fsS https://start.nik0.de/impressum.php | grep -q "Private Projektseite"
curl -fsS https://start.nik0.de/datenschutz.php | grep -q "PHP-Session-Cookie"
```

WofÃƒÂ¼r ist das gut?

- prÃƒÂ¼ft nicht nur Dateien, sondern die echte ÃƒÂ¶ffentliche Auslieferung
- erkennt, wenn der Deploy auf den falschen Server ging
- erkennt, wenn Proxy, Container oder Webserver noch alte Inhalte liefern
- ist ein einfacher Einstieg in Monitoring und Release-Kontrolle

Wenn dieser Test fehlschlÃƒÂ¤gt, kann der Code korrekt sein, aber die VerÃƒÂ¶ffentlichung ist nicht korrekt angekommen.

### Merksatz fÃƒÂ¼r den Unterricht

```text
Validate prÃƒÂ¼ft den Code vor dem Deployment.
Deploy verÃƒÂ¶ffentlicht die geprÃƒÂ¼fte Version.
Smoke prÃƒÂ¼ft nach dem Deployment, ob die Version wirklich sichtbar ist.
```

MÃƒÂ¶gliche spÃƒÂ¤tere Erweiterungen:

- `docker compose config` als StrukturprÃƒÂ¼fung
- einfacher HTTP-Test gegen einen Testcontainer
- LinkprÃƒÂ¼fung fÃƒÂ¼r interne Seiten
- Security-Scan mit `gitleaks` oder vergleichbaren Werkzeugen
- Artefakt- oder Release-Ordner statt direkter DateiÃƒÂ¼bertragung

## Unterrichtsbezug

Dieses Projekt kann in einer CI/CD-Reihe so eingesetzt werden:

| Thema | Konkreter Bezug im Projekt |
|---|---|
| CI-Grundlagen | Validate-Stage mit Syntax- und InhaltsprÃƒÂ¼fungen |
| Git | Ãƒâ€žnderung, Commit, Push, Pipeline-AuslÃƒÂ¶sung |
| Pipeline-Aufbau | Stages, Jobs, Images, Variablen |
| Automatisiertes Testen | Smoke-Tests statt schwerer Testframeworks |
| Build/Artefakte | Diskussion: Warum gibt es hier noch kein echtes Build-Artefakt? |
| Deployment | SSH-Tar-Deploy in Docker-Volume |
| Monitoring/Logging | Job-Logs und ÃƒÂ¶ffentliche Smoke-Tests |
| Sicherheit | `.env`, GitLab Variables, GitHub Secrets, Passwort vs. SSH-Key |

## GitLab vs. GitHub

Beide Plattformen lÃƒÂ¶sen dasselbe Grundproblem:

```text
CodeÃƒÂ¤nderung -> PrÃƒÂ¼fung -> optionales Deployment -> Kontrolle
```

GitLab:

- Pipeline-Datei: `.gitlab-ci.yml`
- Variablen: Settings -> CI/CD -> Variables
- Runner: eigener GitLab Runner im Docker-Setup
- gut fÃƒÂ¼r selbst gehostete Umgebung

GitHub:

- Workflow-Datei: `.github/workflows/ci.yml`
- Secrets/Variables: Settings -> Secrets and variables -> Actions
- Runner: GitHub-hosted oder self-hosted Runner
- gut fÃƒÂ¼r ÃƒÂ¶ffentliche Demos und Vergleich der Plattformen

## Rechtliche Seiten

`impressum.php` und `datenschutz.php` sind fÃƒÂ¼r dieses konkrete private Projekt ausgefÃƒÂ¼llt. FÃƒÂ¼r andere Projekte mÃƒÂ¼ssen die Angaben fachlich und rechtlich neu geprÃƒÂ¼ft werden.

## Quellen und Hinweise

Verwendete Logos und Icons dienen als lokale Assets. Marken- und Urheberrechte verbleiben bei den jeweiligen Inhabern.
