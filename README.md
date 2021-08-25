# Projekt do predmetu IIS

## Informačný systém pre mimovladne organizácie

### Autori

- Daniel Jacko xjacko04@stud.fit.vutbr.cz
- Martin Frandel xfrand00@stud.fit.vutbr.cz
- Patrik Staron xstaro04@stud.fit.vutbr.cz


### Dokumentácia

Dokumentacia sa nachadza v [`./doc/doc.html`](./doc/doc.html).
Pre zobrazenie 

### Databáza

Databáza beží na servery [`mysql80.websupport.sk`](mysql80.websupport.sk).
Pre zobrazenie dokumentácie bez nutnosti stiahnuť: [`Dokumentácia`](https://htmlpreview.github.io/?https://github.com/Dacko98/MO_REGISTER/blob/main/doc/doc.html)

### Štruktúra projektu

- `./app/` Jadro projektu
- `./app/Models/` Modelove triedy pre systém.
- `./app/Http/Controllers` . Kontrolery pre Moduly
- `./doc/` Dokumentacia (doc.html) a readme (README.md).
- `./config/` Konfiguračné subory pre aplikaciu.
- `./database/` Subory pre vytvorenie databazy, migracie.
- `./public/css/` bootstrap a ine css subory.
- `./public/font/` awesome font.
- `./public/files/` symlink pre storage - obrazky pre modely.
- `./public/js/` javascriptove aplikacie.
- `./resources/` Frontend subory.
- `./resources/views/` .blade.php subory na reprezentáciu dát pre užívateľa.
- `./resources/views/auth/` registrácia, login.
- `./resources/views/inc/` header, footer, chybove hlasky, bary na vyhladavanie.
- `./resources/views/layouts/` základ, ktory sa dedí, pridava sa už iba 'content' do \<body>.
- `./resources/views/temp/` Reprezentácia viacerych projektov, organizacii a postov.
- `./resources/views/templates/` základne zobrazenie pre jednotlive modely (vytvaranie, uprava, zobrazenie jedneho).
- `./routes/web.php` registrovane cesty.
- `./storage/` Súborový sklad - neprístupný cez prehliadač.
- `./vednor/` PHP knižnice nainštalované pomocou Composera.
- `.env` Konfiguračný súbor pre aplikáciu, pripojenie do DB, smtp,...
- `artisan` Obrázky.
- `composer.json` Definicia používaných knižníc (PHP), spôsobu ich načítania a požadovaných verzií nástrojom Composer.
- `./package.json` Definicia používaných JavaScript knižníc pre stahovanie JavaScript a CSS závislostí systému - NPM.

#### Požiadavky

- Webový server, napr. [Apache](http://httpd.apache.org/download.cgi) alebo [Nginx](http://nginx.org/en/download.html) +
  [MySQL](https://www.mysql.com/downloads) alebo [MariaDB](https://mariadb.org/download)
  [PHP](http://php.net/downloads.php) >= 7.3
  [XAMPP](https://www.apachefriends.org/download.html)
  [Composer](https://getcomposer.org/download). PHP zavislosti
- [NodeJS](https://nodejs.org/en/download). JavaScript a CSS balíky

**Po 15 minútach neaktivity sa užívateľ automaticky odhlási**

### Nasadenie

Na hosting sme využili hosting heroku, kde sme si nastavili všetky potrebné premenné a napojili na databázu.
