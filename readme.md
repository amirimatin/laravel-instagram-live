# Laravel Instagram Live

Laravel-based APIs for Instagram Live

## Installation

```bash
git clone https://github.com/miladrahimi/laravel-instagram-live.git
cd laravel-instagram-live
docker-compose up -d
docker exec -it instagram-live_php_1 /bin/sh
chmod 0777 storage
cp .env.example .env # You might edit APP_PORT
php artisan key:generate
composer install
```

## Documentation

### Start a live

```http request
POST {URL}/api/live/start
```

Parameters:
* `username`
* `password`


### Stop a live

```http request
POST {URL}/api/live/stop
```

Parameters:
* `username`
* `password`
* `broadcast_id`

# The End