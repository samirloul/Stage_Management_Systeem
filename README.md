# Stage Management Systeem

Een realistische stage-webapplicatie voor school of bedrijf, gebouwd met Laravel (PHP), SQL, HTML, CSS en JavaScript.

## Projectdoel

Deze applicatie lost een echt probleem op: stagebeheer staat vaak verspreid over Excel, mail en losse documenten. Met dit systeem beheer je alles op 1 plek:
- studenten
- bedrijven
- stagekoppelingen
- beoordelingen
- rollen en rechten

Dit is een sterk stageproject omdat je tegelijk backend, frontend, database, security, testing en documentatie laat zien.

## Doelgroep

- Stagecoordinatoren
- Scholen en opleidingen
- Bedrijven met stagiairs
- Studenten

## Kernfunctionaliteiten

- Studenten CRUD
- Bedrijven CRUD
- Stages koppelen aan student + bedrijf
- Beoordelingen beheren
- Zoeken en filteren
- Inloggen en uitloggen
- Rollen en rechten (admin, student, company)

## Mappenstructuur en uitleg

- app/Models: OOP-domeinmodellen (Role, User, Student, Company, Internship, Review)
- app/Http/Controllers: businesslogica voor CRUD, validatie en foutafhandeling
- app/Http/Middleware: role-based access control
- app/Contracts: interfaces voor polymorfisme
- app/Filters: filteralgoritmes (zoek + filter)
- app/Services: extra servicelogica (zoals autorisatiecontrole)
- resources/views: Blade HTML-templates en formulieren
- public/assets/css: custom styling
- public/assets/js: UI-interacties
- routes: endpoint-definities
- database/migrations: schema op Laravel-manier
- database/creatscript.sql: handmatige SQL schema-import
- database/insert.sql: demo-data insert script
- database/storedproducer.sql: stored procedures
- tests: unit, feature en acceptatietest

Waarom belangrijk: deze structuur maakt je project professioneel, onderhoudbaar en GitHub-geschikt.

## Database-ontwerp

Tabellen:
- roles
- users
- students
- companies
- internships
- reviews

Relaties:
- users.role_id -> roles.id
- students.user_id -> users.id
- companies.user_id -> users.id
- internships.student_id -> students.id
- internships.company_id -> companies.id
- reviews.internship_id -> internships.id
- reviews.reviewer_user_id -> users.id

Belangrijk:
- Tabellen in creatscript.sql gebruiken CREATE TABLE IF NOT EXISTS
- Database gebruikt CREATE DATABASE IF NOT EXISTS
- Charset/collation-regel is verwijderd op verzoek

## Waar wordt PDO gebruikt?

In Laravel gebruik je meestal geen losse PDO-code in controllers, omdat Laravel dat voor je regelt.

PDO zit onder de motorkap van:
- Eloquent ORM (Model::create, update, delete, where)
- Query Builder
- DB-connectie uit configuratie

Kort gezegd:
- Jij schrijft veilige model/query code
- Laravel gebruikt intern PDO prepared statements
- Dat helpt SQL-injection voorkomen

## Waar zit OOP in dit project?

Voorbeelden:
- Models als classes: Student, Company, Internship, Review
- Encapsulatie: User::hasRole
- Overerving: BaseModel als basisklasse
- Polymorfisme: FilterContract + StudentFilter/CompanyFilter
- Methods per class voor duidelijke verantwoordelijkheden

Waarom belangrijk:
- code blijft leesbaar
- makkelijker uitbreiden
- minder bugs bij groei van project

## Waar zit validatie en waarom is dat belangrijk?

Validatie zit in controllers via request->validate:
- verplicht veld
- geldig e-mailformaat
- URL check
- min/max regels
- unieke velden
- datumlogica (end_date >= start_date)

Waarom belangrijk:
- voorkomt foute of lege data in je database
- beschermt tegen slechte input
- geeft gebruikers duidelijke feedback

## Foutafhandeling

In CRUD-acties wordt try/catch gebruikt:
- databasefouten worden opgevangen
- report(e) logt technische details
- gebruiker krijgt nette foutmelding

Waarom belangrijk:
- app crasht minder snel
- betere gebruikerservaring
- makkelijker debuggen

## SQL scripts draaien zonder warnings of errors

De SQL scripts zijn zo gemaakt dat ze veilig opnieuw uitvoerbaar zijn:
- creatscript.sql: IF NOT EXISTS op database en tabellen
- insert.sql: INSERT ... SELECT ... WHERE NOT EXISTS om duplicates te voorkomen
- storedproducer.sql: DROP PROCEDURE IF EXISTS voor recreate zonder fout

Je kunt scripts draaien in:
- phpMyAdmin
- SQL branch / SQL client tooling
- MySQL Workbench
- command line mysql

Aanbevolen importvolgorde:
1. database/creatscript.sql
2. database/insert.sql
3. database/storedproducer.sql

## Installatie (Herd)

1. Clone project
2. composer install
3. copy .env.example .env
4. php artisan key:generate
5. Zet databasegegevens in .env
6. Start via 1 van 2 manieren:

Methode A (Laravel):
- php artisan migrate:fresh --seed

Methode B (losse SQL):
- Import database/creatscript.sql
- Import database/insert.sql
- Import database/storedproducer.sql

7. php artisan serve

## Clone en draaien op een andere laptop (zonder problemen)

Gebruik deze stappen precies in volgorde als iemand het project cloned op een eigen computer.

1. Installeer eerst software
- Git
- PHP 8.2 of hoger
- Composer
- MySQL (of Herd met MySQL)
- Node.js LTS (optioneel, voor frontend tooling)

2. Clone de repository
- git clone <jouw-repository-url>
- cd uitleggen_oop_crud_variabelen_testen

3. Installeer backend dependencies
- composer install

4. Maak configuratiebestand
- copy .env.example .env
- php artisan key:generate

5. Zet database-instellingen goed in .env

Gebruik precies dit formaat (zonder streepjes ervoor):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stage_management
DB_USERNAME=root
DB_PASSWORD=
```

6. Kies 1 database-methode

Belangrijk: kies echt 1 methode. Gebruik niet tegelijk SQL-import en daarna direct migrate:fresh, want dat kan "table already exists" geven.

Methode A (aanbevolen voor Laravel):
- php artisan migrate:fresh --seed

Methode B (handmatige SQL import):
- import database/creatscript.sql
- import database/insert.sql
- import database/storedproducer.sql

Als je per ongeluk beide methodes hebt gemixt, run eerst:
- php artisan db:wipe

Daarna kies je opnieuw 1 methode.

7. Start de applicatie
- php artisan serve

8. Test direct of alles werkt
- php artisan test

Als deze test groen is, is de setup op die laptop correct.

## Veelvoorkomende fouten en snelle oplossingen

1. Error: could not find driver
- Oorzaak: pdo_mysql extensie staat uit
- Oplossing: zet pdo_mysql aan in php.ini en herstart terminal/webserver

2. Error: Access denied for user
- Oorzaak: verkeerde DB_USERNAME of DB_PASSWORD
- Oplossing: controleer .env en test in MySQL client

3. Error: Unknown database stage_management
- Oorzaak: database bestaat nog niet
- Oplossing: run creatscript.sql of maak database handmatig aan

4. Error: table already exists of duplicate entry
- Oorzaak: script al eerder uitgevoerd
- Oplossing: scripts zijn idempotent gemaakt met IF NOT EXISTS en WHERE NOT EXISTS; run opnieuw in juiste volgorde

5. Error: APP_KEY missing
- Oorzaak: key nog niet gezet
- Oplossing: php artisan key:generate

6. Error na wijziging in .env
- Oplossing:
- php artisan config:clear
- php artisan cache:clear

7. Poortconflict op php artisan serve
- Oplossing: php artisan serve --port=8001

## Korte setup-checklist voor docenten of beoordelaars

- Project cloned
- Composer packages geinstalleerd
- .env aangemaakt
- APP_KEY gegenereerd
- Database werkt
- Migratie/seed of SQL-import uitgevoerd
- php artisan serve start zonder fout
- php artisan test is groen

## Demo accounts

- admin@stagems.local / Password123!
- student@stagems.local / Password123!
- bedrijf@stagems.local / Password123!

## Technieken uitgelegd

- HTML: structuur van pagina's en formulieren
- CSS: nette, responsive styling
- JavaScript: interactie zoals menu en delete-confirmatie
- PHP: backend logica, OOP, validatie, rechten
- SQL: tabellen, relaties, data-opslag, procedures

Samenwerking:
- HTML + CSS + JS vormen de interface
- PHP verwerkt verzoeken en regels
- SQL bewaart de data betrouwbaar

## Volledige code-uitleg per onderdeel

Voor een complete Nederlandse uitleg waar elk onderdeel precies in de code zit (inclusief PDO, OOP, CRUD, validatie, algoritmes, datastructuren, security en testing):

- docs/CODE_UITLEG_NL.md

Deze extra documentatie is gemaakt voor stagebeoordeling en laat per techniek zien in welke bestanden de implementatie staat.

## Checklist stage-eisen (afgedekt)

- OOP (classes, objecten, overerving, encapsulatie, polymorfisme)
- CRUD (studenten, bedrijven, stages, beoordelingen)
- Database met SQL (migraties + handmatige SQL scripts)
- PHP backend (controllers, services, middleware)
- HTML structuur (Blade views)
- CSS styling (custom stylesheet)
- JavaScript interactie
- Validatie van invoer
- Foutafhandeling met try/catch
- Testen (unit, feature, acceptatie)
- GitHub-geschikte structuur
- Duidelijke documentatie
- Nette mappenstructuur
- Beveiliging van gebruikersgegevens
- Eenvoudige en professionele UI
- Variabelen, datatypes, operators, if/else/switch, loops
- Functies/methoden, arrays/lijsten
- Algoritmes en datastructuren
- Debuggen en logging
- Databasekennis en performance basis
- Schone, gebruiksvriendelijke code

## Testen

- Unit test: tests/Unit/AuthServiceTest.php
- Feature test: tests/Feature/StudentCrudTest.php
- Acceptatie-flow: tests/Feature/Acceptance/StageManagementFlowTest.php

Doel van testen:
- regressies voorkomen
- betrouwbaarheid verhogen
- veilig refactoren

## Beveiliging in het project

- wachtwoord hashing
- sessie-authenticatie
- role middleware
- validatie op server
- Laravel/PDO prepared statements onder water

## GitHub presentatie

Aanbevolen repository-opbouw:
- duidelijke README
- database scripts apart
- tests zichtbaar
- screenshots in docs/screenshots
- logische commits

Voorbeeld commits:
- initial project setup
- added login system with role-based access
- created CRUD for students
- added internship relation with companies
- improved validation and error handling
- added README and SQL scripts with idempotent setup

## Screenshots

Voeg screenshots toe in docs/screenshots:
- dashboard
- studentenbeheer
- bedrijvenbeheer
- stagekoppelingen
- beoordelingen

## Toekomstige uitbreidingen

- export naar PDF/Excel
- e-mailnotificaties
- documentupload per stage
- audit log
- REST API
