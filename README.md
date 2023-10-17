# Laravel r/place

This is another (and bit updated) variant of the app made in "[YouTube video: I built r/place with PHP and JavaScript in an hour](https://www.youtube.com/watch?v=XSw5fFo0_pA)" from  [aschmelyun](https://twitter.com/aschmelyun).

Used Laravel 10 + Vite + Vue 3 + Tailwind. 

Made for the workshop in [Slovak National Gallery](https://umeleckecrevo.sng.sk). 

Feel free to re/use and re/create. 

## Preview

![usage](https://github.com/igor-kamil/rplace/blob/media/final.gif?raw=true)


## Features

 - real-time updates using broadcasting
 - range component for dynamic scale



https://github.com/igor-kamil/rplace/assets/2682941/e794d708-b00c-4885-a05f-d0e435dde8b0



## Requirements

- PHP >= 8.1
- MySQL 8
- Redis
- [Pusher](https://pusher.com) or [Soketi](https://soketi.app) for setting up [Broadcasting](https://laravel.com/docs/10.x/broadcasting)

## Installation

Clone this repo, and then run the following locally:

```bash
cp .env.example .env
composer install
npm install
php artisan serve
```

For more info follow [Laravel 10 Docs: Installation](https://laravel.com/docs/10.x/installation)

## Usage

Use `php artisan pixels:clear` to set empty canvas.
Or `php artisan db:seed` + `php artisan pixels:seed` to generate random pixels from users generated by faker.

## License

This repo is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
