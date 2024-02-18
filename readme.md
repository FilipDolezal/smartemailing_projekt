# SmartEmailing projekt

Tento projekt je reakcí na testovací zadání od společnosti SmartEmailing.cz
Jedná se o jednoduchou webovou aplikaci pro demonstraci Nette, PHP, mySQL a API dovednostní

## Setup:

- proveďte instalaci závislostí

```sh
    composer install
```

- SQL soubor s strukturou databáze naleznete v rootu adresáře: `smartemailing_project.sql`

- importujte databázi a v configuračním souboru se k ní připojte

## Navigace:

- ### Default:

  - Na této stránce naleznete výpis dat z databáze a formulář k jejich filtraci. Také zde naleznete odkaz na akci "Get", která vypíše data s aktuálním zvoleným filtrem v JSON formátu.
  - Pokud v databázi ještě nejsou data, uvidíte zprávu s možností naimportovat data přímo z API.

- ### Get:
  - Tato akce vrací JSON formát výpisu dat z databáze.
  - Filtrace funguje s stejnými URL parametry jako v "Default" akci.

## Filtrace:

Pro filtrování výsledků stačí dotazu přidat GET parametr.

> /home/default/?open=\<**otevřeno**>&?date=\<**datum**>

nebo

> /home/get/?open=\<**otevřeno**>&?date=\<**datum**>

- ### Open

  - Zobrazit pouze otevřené prodejny.
  - Boolean hodnota s výchozí hodnotou FALSE.
  - TRUE: Zobrazí se pouze otevřené prodejny
  - FALSE: Zobrazí se všechny prodejny bez ohledu na otevírací dobu
  - Lze kombinovat s parametrem "Date"

- ### Date
  - Zobrazí otevřené podniky v konkrétně uvedeném čase
  - String hodnota s výchozí hodnotou "now"
  - Může být libovolný formát DateTime.
  - Použitelné pouze v kombinaci s Open=TRUE
