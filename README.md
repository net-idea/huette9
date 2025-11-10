# Hütte9 - Berghütten-Website

Willkommen zur Hütte9-Website! Dies ist eine moderne Symfony 7.2-Webanwendung für eine Airbnb-Berghütte mit einem Kontaktformular und MariaDB-Datenbankintegration.

## Features

- 🏔️ Ansprechende Homepage mit Willkommenstext
- 📧 Kontaktformular mit Validierung und Spam-Schutz (Rate Limiting)
- 🗄️ MariaDB-Datenbank zur Speicherung von Kontaktanfragen
- 🎨 Bootstrap 5 für modernes, responsives Design
- 🐳 Docker & Docker Compose für einfache lokale Entwicklung
- 🇩🇪 Deutschsprachige Benutzeroberfläche

## Technologie-Stack

- **Framework**: Symfony 7.2
- **Sprache**: PHP 8.3
- **Datenbank**: MariaDB 10.11
- **Frontend**: Bootstrap 5, Twig Templates
- **Containerisierung**: Docker & Docker Compose

## Voraussetzungen

### Für macOS

1. **Docker Desktop für Mac** installieren:
   ```bash
   # Homebrew verwenden
   brew install --cask docker
   
   # Oder manuell von https://www.docker.com/products/docker-desktop herunterladen
   ```

2. **Git** (falls noch nicht installiert):
   ```bash
   brew install git
   ```

### Für Ubuntu

1. **Docker & Docker Compose** installieren:
   ```bash
   # Docker installieren
   sudo apt-get update
   sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common
   curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
   echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
   sudo apt-get update
   sudo apt-get install -y docker-ce docker-ce-cli containerd.io
   
   # Docker ohne sudo verwenden
   sudo usermod -aG docker $USER
   newgrp docker
   
   # Docker Compose installieren
   sudo apt-get install -y docker-compose-plugin
   ```

2. **Git** (falls noch nicht installiert):
   ```bash
   sudo apt-get install -y git
   ```

## Installation & Lokale Entwicklung

### 1. Repository klonen

```bash
git clone https://github.com/net-idea/h-tte9.git
cd h-tte9
```

### 2. Mit Docker starten

```bash
# Container bauen und starten
docker compose up -d

# Warten Sie, bis die Container gestartet sind (ca. 30 Sekunden)
docker compose ps
```

### 3. Datenbank initialisieren

```bash
# Datenbank-Migrationen erstellen
docker compose exec web php bin/console make:migration

# Migrationen ausführen
docker compose exec web php bin/console doctrine:migrations:migrate --no-interaction
```

### 4. Anwendung öffnen

Öffnen Sie Ihren Browser und navigieren Sie zu:
```
http://localhost:8000
```

Sie sollten nun die Hütte9-Homepage mit dem Kontaktformular sehen!

## Entwickler-Befehle

### Container-Verwaltung

```bash
# Container starten
docker compose up -d

# Container stoppen
docker compose down

# Logs anzeigen
docker compose logs -f

# In den Web-Container einsteigen
docker compose exec web bash
```

### Symfony-Befehle

```bash
# Cache leeren
docker compose exec web php bin/console cache:clear

# Neue Migration erstellen
docker compose exec web php bin/console make:migration

# Migrationen ausführen
docker compose exec web php bin/console doctrine:migrations:migrate

# Neuen Controller erstellen
docker compose exec web php bin/console make:controller

# Neue Entity erstellen
docker compose exec web php bin/console make:entity
```

### Datenbank-Befehle

```bash
# In die MariaDB-Konsole einsteigen
docker compose exec mariadb mysql -u huette9 -phuette9pass huette9

# Datenbank-Schema validieren
docker compose exec web php bin/console doctrine:schema:validate

# SQL für Migrationen anzeigen
docker compose exec web php bin/console doctrine:migrations:status
```

### Tests ausführen

```bash
# PHPUnit-Tests ausführen
docker compose exec web php bin/phpunit
```

## Projektstruktur

```
h-tte9/
├── config/                 # Symfony-Konfigurationsdateien
│   ├── packages/          # Package-spezifische Konfiguration
│   └── routes.yaml        # Routing-Konfiguration
├── public/                # Öffentlich zugängliche Dateien
│   └── index.php         # Front-Controller
├── src/
│   ├── Controller/       # Controller (z.B. HomeController)
│   ├── Entity/           # Doctrine-Entities (z.B. Contact)
│   └── Repository/       # Doctrine-Repositories
├── templates/            # Twig-Templates
│   ├── base.html.twig   # Basis-Layout
│   └── home/            # Homepage-Templates
├── migrations/           # Datenbank-Migrationen
├── var/                  # Cache und Logs
├── compose.yaml          # Docker Compose-Konfiguration
├── Dockerfile           # Docker-Image-Definition
└── .env                 # Umgebungsvariablen
```

## Umgebungsvariablen

Die wichtigsten Umgebungsvariablen sind in der `.env`-Datei definiert:

```env
APP_ENV=dev
DATABASE_URL=mysql://huette9:huette9pass@mariadb:3306/huette9?serverVersion=10.11.2-MariaDB&charset=utf8mb4
```

Für lokale Anpassungen können Sie eine `.env.local`-Datei erstellen.

## Spam-Schutz

Das Kontaktformular verfügt über einen Rate-Limiter, der verhindert, dass ein Benutzer zu viele Anfragen sendet:
- **Limit**: 3 Anfragen pro IP-Adresse
- **Zeitfenster**: 15 Minuten
- Konfiguration in `config/packages/rate_limiter.yaml`

## Datenbank-Schema

Die `Contact`-Entity speichert folgende Informationen:
- **name**: Name des Absenders (erforderlich)
- **email**: E-Mail-Adresse (erforderlich, validiert)
- **subject**: Betreff (optional)
- **message**: Nachricht (erforderlich, mindestens 10 Zeichen)
- **createdAt**: Zeitstempel der Erstellung

## Troubleshooting

### Port 8000 ist bereits belegt

```bash
# Anderen Dienst auf Port 8000 stoppen oder Port in compose.yaml ändern
# Beispiel: "8080:8000" statt "8000:8000"
```

### Container startet nicht

```bash
# Logs prüfen
docker compose logs web
docker compose logs mariadb

# Container neu bauen
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Datenbank-Verbindungsfehler

```bash
# Sicherstellen, dass MariaDB läuft
docker compose ps

# MariaDB-Logs prüfen
docker compose logs mariadb

# Gesundheitsstatus prüfen
docker compose exec mariadb healthcheck.sh --connect
```

### Permissions-Probleme (Linux)

```bash
# Eigentümer der Dateien anpassen
sudo chown -R $USER:$USER .

# Cache-Verzeichnis berechtigen
chmod -R 777 var/
```

## Produktions-Deployment

Für Produktionsumgebungen:

1. Setzen Sie `APP_ENV=prod` in der `.env`-Datei
2. Generieren Sie ein sicheres `APP_SECRET`
3. Ändern Sie alle Datenbank-Passwörter
4. Verwenden Sie HTTPS
5. Aktivieren Sie zusätzliche Sicherheitsmaßnahmen

```bash
# Produktions-Optimierungen
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Lizenz

Siehe [LICENSE](LICENSE) für Details.

## Kontakt

Bei Fragen oder Problemen öffnen Sie bitte ein Issue im GitHub-Repository.
