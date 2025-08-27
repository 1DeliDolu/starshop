# Symfony, Doctrine Relations & Warp Drive Basics

Hallo und herzlich willkommen! Dieses Repository enthält den Code und das Skript für den
[Symfony, Doctrine Relations & Warp Drive Basics](https://symfonycasts.com/screencast/symfony7-doctrine-relations)
Kurs auf SymfonyCasts.

## Einrichtung

Wenn du den Code gerade heruntergeladen hast: Glückwunsch!

Um das Projekt zum Laufen zu bringen, folge diesen Schritten:

**Composer-Abhängigkeiten herunterladen**

Stelle sicher, dass [Composer installiert](https://getcomposer.org/download/) ist und führe dann aus:

```
composer install
```

Alternativ musst du möglicherweise `php composer.phar install` ausführen, abhängig davon, wie Composer installiert wurde.

**TailwindCSS bauen**

Dieses Projekt verwendet TailwindCSS. Um die CSS-Datei zu bauen, führe aus:

```
php bin/console tailwind:build
```

**Symfony Webserver starten**

Du kannst Nginx oder Apache verwenden, aber der lokale Webserver von Symfony funktioniert meist noch besser.

Um den Symfony-Client zu installieren, folge den Anweisungen unter
https://symfony.com/download – das musst du nur einmal pro System machen.

Danach, um den Webserver zu starten, öffne ein Terminal, gehe in das Projektverzeichnis und führe aus:

```
symfony serve
```

(Falls du diesen Befehl zum ersten Mal verwendest, kann es sein, dass du vorher `symfony server:ca:install` ausführen musst.)

Jetzt kannst du die Seite unter `https://localhost:8000` aufrufen.

Viel Spaß!

## Ideen, Feedback oder Probleme?

Wenn du Vorschläge oder Fragen hast, kannst du gerne ein Issue in diesem Repository eröffnen oder einen Kommentar beim Kurs hinterlassen. Wir beobachten beides :).

## Danke!

Vielen lieben Dank für deine Unterstützung und dafür, dass wir das tun dürfen, was wir lieben!

<3 Deine Freunde von SymfonyCasts

---

## Letzte Aktualisierungen & Hinweise (Ergänzung)

Der folgende Abschnitt dient als allgemeine Anleitung. Bitte passe ihn ggf. an die tatsächlich im Repository vorhandenen Dateien an.

### 1) Umgebungsvariablen
- Das Projekt verwendet Einstellungen aus der `.env` oder `.env.local` Datei. Für die Datenbankverbindung muss die Variable `DATABASE_URL` korrekt gesetzt werden:
  - Beispiel: DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
- Überprüfe weitere notwendige ENV-Variablen für E-Mail, Drittanbieterdienste usw.

### 2) Datenbank & Migrationen
- Um eine neue Entwicklungsumgebung einzurichten, erstelle die Datenbank und führe Migrationen aus:
  ```
  php bin/console doctrine:database:create
  php bin/console doctrine:migrations:migrate
  ```
- Falls vorhanden, können auch Fixtures geladen werden (siehe unten).

### 3) Fixtures (Beispieldaten)
- Um Beispieldaten für die Entwicklung zu laden, verwende ggf. DoctrineFixturesBundle:
  ```
  php bin/console doctrine:fixtures:load
  ```
- Achtung: Dies kann die aktuelle Datenbank überschreiben.

### 4) Tests
- Um Unit- oder Integrationstests mit PHPUnit auszuführen:
  ```
  php ./vendor/bin/phpunit
  ```
- Falls eine CI-Konfiguration existiert (z.B. GitHub Actions), prüfe die entsprechenden Workflow-Dateien im Verzeichnis `.github/workflows`.

### 5) Cache & Assets
- Bei Problemen mit Cache oder Assets:
  ```
  php bin/console cache:clear
  php bin/console assets:install
  ```

### 6) Docker (falls vorhanden)
- Wenn Docker unterstützt wird, findest du eine `docker-compose.yml` im Hauptverzeichnis. Beispiel für den Start:
  ```
  docker compose up --build
  ```

### 7) Tailwind / Frontend-Entwicklung
- Für die Entwicklung mit Tailwind oder zum Überwachen von Änderungen:
  ```
  php bin/console tailwind:build --watch
  ```
  oder ggf. über `npm run dev`/`npm run build` aus der `package.json`.

### 8) Beitrag leisten
- Wenn du zum Projekt beitragen möchtest:
  - Eröffne ein neues Issue oder sende einen Pull Request.
  - Beachte ggf. vorhandene Coding-Guidelines (z.B. PHP CS Fixer, Rector).
  - Bei größeren Änderungen bitte zuerst ein Issue zur Diskussion eröffnen.

### 9) Lizenz & Urheberrecht
- Die Projektlizenz ist in der Datei `LICENSE` oder `LICENSE.md` im Hauptverzeichnis zu finden. Für Informationen zur Nutzung siehe diese Datei.

### 10) Changelog (Änderungsprotokoll)
- Falls ein `CHANGELOG.md` vorhanden ist, findest du dort alle wichtigen Änderungen. Andernfalls empfiehlt es sich, wichtige Release-Notes in diesem Abschnitt zu dokumentieren.

---

Hinweis: Dieser Ergänzungsabschnitt dient als allgemeiner Leitfaden und basiert nicht auf dem tatsächlichen Inhalt der Dateien im Repository. Bitte passe ihn an die Gegebenheiten deines Projekts an.
