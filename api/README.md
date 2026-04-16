# API — Apex Arena Backend

## Overview

This container is the backend of the Apex Arena application. It exposes a JSON API used by the front container. It has no HTML output, every response is JSON. It is the only service that communicates with the database.

---

## Stack

- PHP 8.2 with Apache
- PDO for database access (MySQL)
- Extensions:  pdo_mysql

---

## Endpoints

### Public

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /login.php | Authenticate a user, starts a session |
| POST | /register.php | Create a new user account |
| POST | /logout.php | Destroy the session |
| GET | /games.php | List all games |
| GET | /games.php?id=N | Get a single game with its achievements |
| GET | /healthcheck.php | Returns DB connection status |

### Authenticated (requires active session)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /user_games.php | Get the current user's game library |
| POST | /user_games.php | Add a game to the user's profile |
| DELETE | /user_games.php | Remove a game from the user's profile |
| GET | /user_achievements.php?list=1&game_id=N | List unlocked achievement IDs for a game |
| POST | /user_achievements.php | Unlock an achievement |
| DELETE | /user_achievements.php | Remove an unlocked achievement |

### Admin only

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /games.php | Add a new game |
| PUT | /games.php?id=N | Update a game |
| DELETE | /games.php?id=N | Delete a game and all its data |
| POST | /achievements.php | Add an achievement to a game |
| DELETE | /achievements.php?id=N | Delete an achievement |
| GET | /users.php | List users (paginated) |
| PUT | /users.php?id=N | Change a user's role |
| DELETE | /users.php?id=N | Delete a user |
| GET | /stats.php | Get total user and game counts |


---

## Environment Variables

These are injected by Docker Compose:

| Variable | Description |
|----------|-------------|
| DB_HOST | Hostname of the MySQL container (db) |
| DB_NAME | Database name |
| DB_USER | Database user |
| DB_PASS | Database password |

---

## Dockerfile

```dockerfile
FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/

RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 80
```

The document root is changed from the Apache default (/var/www/html) to /var/www/public, so only the public/ folder is accessible via HTTP. The includes/ folder containing DB credentials is never reachable from outside.

---

## Security Notes

- The API container has no exposed host port it is only reachable by the front container on the internal Docker frontend network.
- The includes/ directory is outside the Apache document root and is therefore not accessible via HTTP.
- All database queries use **PDO prepared statements** to prevent SQL injection.
- Passwords are hashed with password_hash().