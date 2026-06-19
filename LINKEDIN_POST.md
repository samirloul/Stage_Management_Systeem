# LinkedIn post (menselijk en professioneel)

Ik heb mijn Stage Management Systeem gebouwd om bewust te oefenen met echte software engineering, niet alleen om "iets werkends" op te leveren.

In dit project stond voor mij 1 ding centraal: laten zien dat ik begrijp wat ik codeer, waarom ik het zo bouw en welke technische keuzes daarbij horen.

Wat deze webapp doet:
- Studenten beheren (CRUD)
- Bedrijven beheren (CRUD)
- Stages koppelen aan student en bedrijf
- Beoordelingen en feedback registreren
- Rollen en rechten toepassen
- Zoeken, filteren en valideren
- Dashboard met kerncijfers tonen

Waarom ik dit project gemaakt heb:
- Oefenen met professionele backend structuur
- Oefenen met OOP-principes in een echte case
- Begrijpen hoe database, validatie, security en tests samenwerken
- Laten zien dat ik code onderhoudbaar en uitbreidbaar kan maken

Gebruikte programmeertalen en technologieen:
- PHP (Laravel)
- SQL (MySQL)
- HTML (Blade)
- CSS
- JavaScript
- PHPUnit voor testen

Belangrijke methodes en concepten die ik bewust heb toegepast:
- OOP: classes, inheritance, encapsulation, polymorphism
- Service layer: logica verplaatst uit controllers naar services
- Dependency Injection: duidelijke scheiding van verantwoordelijkheden
- CRUD-architectuur: create, read, update, delete per domein
- Validatie op meerdere niveaus: velden, business rules, status-logica
- Role Based Access Control via middleware
- Eager loading om N+1 queries te voorkomen
- Database relaties met foreign keys en cascade gedrag
- Testing met duidelijke Arrange-Act-Assert opzet

PDO en waarom dat belangrijk is:
In Laravel schrijf ik meestal niet handmatig met losse PDO-queries, maar onder de motorkap gebruikt Laravel wel PDO prepared statements.
Dat is belangrijk omdat:
- het SQL-injection helpt voorkomen
- het veilig omgaat met gebruikersinput
- het zorgt voor betrouwbare database interactie

PSR-12 en nette code stijl:
Ik heb gewerkt volgens nette code conventies (PSR-12 stijl), met duidelijke naamgeving, leesbare structuur en commentaar in het Nederlands.
Doel daarvan:
- code die anderen snel begrijpen
- makkelijker reviewen in teamverband
- veiliger refactoren en uitbreiden

Voor mij is de kern van dit project:
Ik wil laten zien dat ik niet alleen een project kan bouwen, maar ook technisch kan uitleggen waarom iets werkt, welke afwegingen ik maak en hoe ik kwaliteit borg met validatie, structuur en tests.

Repository (clone en run zelf):
https://github.com/samirloul/Stage_Management_Systeem.git

Snelle start:
1. git clone https://github.com/samirloul/Stage_Management_Systeem.git
2. cd Stage_Management_Systeem
3. composer install
4. copy .env.example .env
5. php artisan key:generate
6. php artisan migrate:fresh --seed
7. php artisan serve

Feedback is welkom. Ik sta open voor code review en verbeterpunten.

#Laravel #PHP #OOP #CRUD #SQL #MySQL #PDO #PSR12 #Testing #SoftwareDevelopment #WebDevelopment #StageProject

---

## Kortere versie (optioneel)

Ik heb een Stage Management Systeem gebouwd in Laravel om gericht te oefenen met OOP, CRUD, validatie, autorisatie, database ontwerp en testen.

Wat ik belangrijk vond:
- Niet alleen bouwen, maar ook begrijpen en kunnen uitleggen waarom de code zo is opgezet
- Nette code volgens PSR-12 stijl
- Veiligheid via PDO prepared statements (onder Laravel), validatie en role based access
- Onderhoudbaarheid via services, filters, middleware en duidelijke structuur

Tech stack: PHP, Laravel, SQL/MySQL, HTML, CSS, JavaScript, PHPUnit.

Je kunt het project zelf clonen en draaien via:
https://github.com/samirloul/Stage_Management_Systeem.git

Ik hoor graag feedback van developers en bedrijven.
