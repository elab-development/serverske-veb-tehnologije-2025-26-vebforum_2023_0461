# Veb Forum

Laravel REST API aplikacija za veb forum, razvijena u okviru predmeta Serverske veb tehnologije.

Aplikacija omogućava korisnicima da kreiraju teme, postove i komentare, kao i da lajkuju sadržaj. Postoje različita prava pristupa za obične korisnike, moderatore i administratore.

## Funkcionalnosti

- registracija, login i logout korisnika
- autentifikacija pomoću Laravel Sanctum tokena
- CRUD operacije nad temama, postovima i komentarima
- lajkovanje postova
- korisničke uloge: user, moderator i admin
- paginacija, pretraga, filtriranje i sortiranje tema
- resetovanje lozinke
- statistika foruma
- integracija sa javnim REST servisima
- JSON odgovori i obrada grešaka

## Tehnologije

- PHP
- Laravel 13
- mysql
- Postman
- Github

## API

Aplikacija koristi REST API sa GET, POST, PUT/PATCH i DELETE zahtevima.

Postoje i ugnježdene rute, na primer:

```text
/api/topics/{topic}/posts
/api/posts/{post}/comments
```

Za zaštićene rute koristi se Bearer token dobijen nakon prijavljivanja.

## Javni servisi

Aplikacija koristi News API i OpenWeatherMap API.

Primer:

```text
GET /api/public/weather?city=Belgrade
```

Za OpenWeatherMap potrebno je dodati API ključ u `.env`:

```env
OPENWEATHER_API_KEY=your_api_key
```

## Pokretanje projekta

Nakon kloniranja projekta potrebno je pokrenuti:

```bash
composer install
```

Kreirati `.env` fajl:

```bash
copy .env.example .env
```

Generisati ključ aplikacije:

```bash
php artisan key:generate
```

Pokrenuti migracije:

```bash
php artisan migrate
```

i zatim server:

```bash
php artisan serve
```

API je tada dostupan na:

```text
http://127.0.0.1:8000/api
```

Sve API rute mogu se pregledati komandom:

```bash
php artisan route:list --path=api
```

## Testiranje

API je testiran kroz Postman, uključujući autentifikaciju, CRUD operacije, autorizaciju, pretragu, paginaciju, ugnježdene rute i javne servise.