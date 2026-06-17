# Code Uitleg (Nederlands) - Stage Management Systeem

Dit document laat per onderdeel zien waar technieken en programmeerconcepten in de code terugkomen.

## 1. OOP (Object Oriented Programming)

Waar:
- app/Models/BaseModel.php
- app/Models/User.php
- app/Models/Student.php
- app/Models/Company.php
- app/Models/Internship.php
- app/Models/Review.php
- app/Models/Role.php
- app/Services/AuthService.php
- app/Services/StudentIdentityService.php

Wat je ziet:
- classes en objecten
- methods en properties
- encapsulatie (bijvoorbeeld hasRole)
- overerving (BaseModel)
- polymorfisme (FilterContract met meerdere implementaties)

## 2. CRUD

Waar:
- app/Http/Controllers/StudentController.php
- app/Http/Controllers/CompanyController.php
- app/Http/Controllers/InternshipController.php
- app/Http/Controllers/ReviewController.php

Wat je ziet:
- Create: store
- Read: index
- Update: update
- Delete: destroy

## 3. Database met SQL

Waar:
- database/creatscript.sql
- database/insert.sql
- database/storedproducer.sql
- database/migrations/*.php

Wat je ziet:
- tabellen en relaties
- foreign keys
- idempotente scripts (IF NOT EXISTS)
- stored procedures

## 4. PHP backend

Waar:
- app/Http/Controllers/*
- app/Models/*
- app/Services/*
- routes/web.php

Wat je ziet:
- request handling
- businessregels
- validatie
- foutafhandeling
- data-opslag via Eloquent

## 5. HTML voor structuur

Waar:
- resources/views/**/*.blade.php

Wat je ziet:
- formulieren
- tabellen
- layoutcomponenten
- pagina-opbouw

## 6. CSS voor styling

Waar:
- public/assets/css/stage.css

Wat je ziet:
- knoppen, formulieren, tabellen
- responsive gedrag
- nette en eenvoudige visuele stijl

## 7. JavaScript voor interactie

Waar:
- public/assets/js/stage.js

Wat je ziet:
- menu toggle
- delete-confirmaties
- auto-dismiss alerts
- switch en for-loop voorbeelden

## 8. Validatie van invoer

Waar:
- controllers in app/Http/Controllers/*

Wat je ziet:
- required regels
- regex regels
- e-mail, URL, datum, min/max
- status-afhankelijke businessvalidatie voor stages

## 9. Foutafhandeling

Waar:
- controllers met try/catch

Wat je ziet:
- try/catch rond create/update/delete
- report(e) voor logging
- nette gebruikersmeldingen met withErrors

## 10. Testen

Waar:
- tests/Unit/AuthServiceTest.php
- tests/Feature/StudentCrudTest.php
- tests/Feature/Acceptance/StageManagementFlowTest.php

Wat je ziet:
- unit tests
- feature/integratie tests
- acceptatieflow tests

## 11. PDO gebruik

Waar:
- indirect via Laravel Eloquent/Query Builder in controllers en models

Uitleg:
- Deze applicatie gebruikt niet handmatig new PDO(...).
- Laravel gebruikt intern PDO met prepared statements.
- Daardoor heb je veilige databasequeries met minder SQL-injection risico.

## 12. Belangrijke programmeerconcepten in code

Variabelen:
- in alle controllers/services (bijv. $filters, $data, $status)

Datatypes:
- string/int/bool/array in type hints en returns

Operators:
- vergelijkingen en logische checks in validatie (===, >, <, &&, ||)

Conditionele statements:
- if/else in controllers en services
- switch in public/assets/js/stage.js

Loops:
- foreach in views en services
- for in stage.js

Functies en methoden:
- methods per controller/model/service

Arrays/lijsten:
- validatieregels, options, filterarrays

Classes en objecten:
- models, controllers, services, middleware

Encapsulatie:
- methods op model/service die logica afschermen

Overerving:
- BaseModel -> domeinmodellen

Polymorfisme:
- FilterContract + StudentFilter/CompanyFilter

Algoritmes:
- StudentIdentityService zoekt eerste ontbrekende studentnummer

Datastructuren:
- collections en arrays in filters/queries

Try/catch:
- foutafhandeling bij database-acties

Debuggen:
- report(e), logs, tests en validatiemeldingen

Performance:
- queryfilters, paginatie, indexes in SQL/migraties

Security:
- auth + role middleware
- password hashing
- input validatie
- prepared statements via Laravel/PDO

## 13. GitHub-geschikte structuur

Waar zichtbaar:
- duidelijke mappenstructuur
- README.md
- SQL scripts in database/
- tests/
- routes/controllers/models/views gescheiden

## 14. Waarom dit goed is voor stage

- realistische casus
- volledige stack (frontend + backend + database)
- aantoonbare softwarekwaliteit (tests, validatie, documentatie)
- professioneel overdraagbaar project
