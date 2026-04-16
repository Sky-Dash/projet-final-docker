# Front — Apex Arena Frontend

## Overview

This container serves the HTML interface of the Apex Arena application. It does not connect to the database directly — instead it calls the api container via internal HTTP using cURL, then renders the responses as HTML pages.

---

## Stack

- PHP 8.2 with Apache
- cURL for internal API calls (built into the base image)

---

## Pages

| Page | URL              | Access |
|------|------------------|-------|
| Home | /index.php       | Public |
| Login | /login.php       | Public |
| Register | /register.php    | Public |
| Profile | /profile.php     | Logged in |
| Game detail | /game.php?id=N   | Public |
| Admin dashboard | /admin.php       | Admin |
| Manage games | /games.php       | Admin |
| Edit game | /admin_game.php?id=N | Admin |
| Manage users | /users.php       | Admin |
|Healthcheck| /healthcheck.php | Public|

---


## Environment Variables

| Variable | Description                                              |
|----------|----------------------------------------------------------|
| API_URL | Internal URL of the api container (set in Docker Compose) |

---

## Dockerfile

```dockerfile
FROM php:8.2-apache

COPY . /var/www/

RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

EXPOSE 80
```

The document root is changed to /var/www/public so only the public/ folder is served. The includes/ folder with the API client and credentials is never accessible via HTTP.

---

## Uploads

Game images are stored in public/uploads/. This folder is bind-mounted from the host at ./front/public/uploads, shared with the api container:

- api writes uploaded files into this folder
- front serves them directly as static files

To set correct permissions on the host:

bash
sudo chown -R www-data:www-data front/public/uploads
