# FilamentPHP Starter

## Dependencies

-   Laravel Framework: version ^11.31
-   MySQL: version 8.0.30
-   PHP: version 8.3
-   Nodejs: version 18

## Development Installation

1. Clone the repository:

```bash
git clone https://github.com/ajcastro/filamentphp-starter.git
```

2. Install Composer and Npm dependencies:

```bash
composer install
```

```bash
npm install
```

3. Create a copy of the `.env.example` file and rename it to `.env`:

```bash
cp .env.example .env
```

4. Generate the application key:

```bash
php artisan key:generate
```

5. Configure the local environment variables in the `.env` file:

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=filamentphp
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations with seed data:

```bash
php artisan migrate --seed
```

7. Go to `/admin/login` and use credentials:

```
username: admin@example.com
password: pass1234
```

## Features

-   Telescope `/telescope`
-   Users Crud
