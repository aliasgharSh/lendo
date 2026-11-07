# Docker environment for Laravel (Windows)

This setup provides PHP 8.4-FPM, Nginx, MySQL 8.4, and phpMyAdmin.

## 1. Requirements
Install and start Docker Desktop. Make sure Docker Desktop is using Linux containers.

## 2. Place the files
Copy `compose.yaml`, `.dockerignore`, and the `docker` folder into the root of your Laravel project (for example `E:\\lendo`).
Copy `.env.docker.example` to `.env.docker` if you want to customize the database credentials. Compose automatically reads `.env`; if your Laravel project already has its own `.env`, do not replace it. Instead, either use the default credentials in `compose.yaml`, or pass a separate env file with `docker compose --env-file .env.docker up -d --build`.

## 3. Build and start
Open Command Prompt or PowerShell in the Laravel project folder:

```powershell
docker compose up -d --build
```

## 4. Laravel database settings
Set these values in the Laravel project's `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=lendo
DB_USERNAME=lendo
DB_PASSWORD=lendo_local_password
```

Important: inside Docker, use `DB_HOST=mysql` and port `3306`. From Windows tools such as a desktop MySQL client, use `127.0.0.1` and port `3307`.

If Laravel's `.env` is changed, clear cached configuration:

```powershell
docker compose exec php php artisan config:clear
```

## 5. Open services
- Laravel: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL from Windows: `127.0.0.1:3307`

phpMyAdmin login:
- Server/host: `mysql`
- Username: `lendo`
- Password: `lendo_local_password`

You can also log in as `root` with password `root_local_password` (local development only).

## 6. Common commands

```powershell
docker compose ps
docker compose logs -f
docker compose exec php php -v
docker compose exec php php artisan --version
docker compose exec php composer install
docker compose exec php php artisan migrate
docker compose down
```

To stop containers without deleting the database, run `docker compose down`.
To permanently delete the Docker database volume too, run `docker compose down -v` (this deletes the database data).

## Notes
- SQL is the query language; MySQL is the database server that executes SQL.
- PHP-FPM runs PHP scripts; Nginx serves HTTP and forwards PHP requests to PHP-FPM.
- This is intended for local development. Change passwords and harden configuration before any shared or production deployment.
- If another service already uses Windows ports 8080, 8081, or 3307, change the left-hand port number before the colon in `compose.yaml`.
