## License Manager

A SaaS Safe Project that allows you to give access to your private apps to people via licenses/keys that have expire dates and devices limit, Its designed for android apps but it can be used on iOS, macOS or Windows PC's if you can get their serial numbers so that the devices limit would work without bugs.

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
