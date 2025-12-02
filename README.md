## KeyAuthManagement
A tool made for making authentication keys that has status, expire dates and specifc apps to run on,
Made for hand made subscriptions or closed source apps that should be opened without a key,

## Requirements
```
PHP 8.5
Composer
```

## Setup
```
copy .env.example to .env
setup your db credentials in .env
run the following commands
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate:fresh --seed
and you're ready to host on nginx
```