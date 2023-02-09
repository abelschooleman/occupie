## Hello

This application satisfies the Backend developer test assignment of Bookinglayer and is only intended to serve as a demonstration of skills.

### Getting started

Clone the repo in a directory locally, and start up the containers.

Copy .env.example to .env.

Access the occupie container and run:
```
composer install
php artisan migrate
php artisan db:seed
```

### To try endpoints:
- make sure http://occupie.test points to localhost.
- an import of endpoints for Insomnia is included in endpoints.json. The import should also work for Postman.

### Testing
Run `php artisan test` in the occupie container or from a local machine if configured to use the container's interpreter.
