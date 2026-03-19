# Countries

Small app: Auth0 login, then browse countries (name + flag). Data comes from [REST Countries](https://restcountries.com), cached for an hour, paginated in PHP.

**Stack:** Laravel, Vue 3, Vite, Tailwind, Auth0, Docker (PHP-FPM + nginx).

---

## Requirements

- **Docker** with Compose (v2.20+). Use `docker compose` or `docker-compose`.
- **Auth0 account** — you need it to configure login (see [Auth0 setup](#auth0-setup) below).

You do **not** need PHP, Composer, or Node.js on your host. Everything runs inside Docker.

---

## Run the app (first time)

Follow these steps in order.

### 1. Copy environment file

```bash
cp .env.example .env
```

The app container will not start without a `.env` file.

### 2. Configure Auth0 in `.env`

Before starting Docker, set these three variables in `.env` (see [Auth0 setup](#auth0-setup) for where to get the values):

- `AUTH0_DOMAIN` — your Auth0 tenant domain (e.g. `dev-xxx.us.auth0.com`)
- `AUTH0_AUDIENCE` — your API identifier
- `VITE_AUTH0_CLIENT_ID` — your SPA application Client ID

If these are empty, the app will start but login will not work.

### 3. Start the stack

```bash
docker compose up -d
```

(`docker-compose up -d` works too.)

### 4. First run: wait for setup to finish

On **first start**, the following run automatically inside the containers. You do **not** need to run them yourself:

| Step | Where it runs | What it does |
|------|----------------|--------------|
| Composer install | `app` container entrypoint | Installs PHP dependencies if `vendor/` is missing |
| APP_KEY | `app` container entrypoint | Generates `APP_KEY` in `.env` if not set |
| Migrations | `app` container entrypoint | Runs `php artisan migrate` (SQLite DB is created automatically) |
| Frontend build | `frontend-init` one-off container | Runs `npm install` and `npm run build` if `public/build/` is missing |

**Give it a few minutes** on first run. Nginx only starts after the app is healthy and the frontend build has completed.

### 5. Open the app

Go to **http://localhost:8080**.

---

## Summary: do you need to run anything else?

- **No** `composer install` — the app container runs it on first start.
- **No** `npm install` / `npm run build` — the `frontend-init` service runs them on first start.
- **No** `php artisan migrate` — the app container runs it on every start.
- **Yes** `cp .env.example .env` and filling Auth0 (and optionally `APP_KEY` is auto-generated) — you must do this before `docker compose up`.

---

## Auth0 setup

Configure Auth0 and then set the values in `.env` **before** running `docker compose up` (or before using the app).

1. **API** — In Auth0: APIs → Create. Copy the **Identifier** → set as `AUTH0_AUDIENCE` in `.env`.
2. **SPA** — Applications → Create → Single Page Application. Copy **Client ID** → set as `VITE_AUTH0_CLIENT_ID` in `.env`.
3. **Domain** — In the Auth0 dashboard (e.g. tenant name or Settings → General), copy your tenant domain (e.g. `dev-xxx.us.auth0.com`) → set as `AUTH0_DOMAIN` in `.env`.
4. In the **API** → Applications tab, authorize your new SPA application.
5. In the **SPA** application settings set:
   - **Callback URL:** `http://localhost:8080/callback`
   - **Logout URL:** `http://localhost:8080`
   - **Allowed Web Origins:** `http://localhost:8080`

Keep `APP_URL` and `VITE_APP_URL` as `http://localhost:8080` for this Docker setup.

---

## Changing Auth0 or `VITE_*` in `.env`

The frontend is built at container start; `VITE_*` values are baked into the build. If you change Auth0 or any `VITE_*` variable:

1. Delete the build output:  
   `rm -rf public/build`
2. Start again:  
   `docker compose up -d`

The `frontend-init` service will run again and rebuild with the new values.

---

## API

`GET /api/countries?page=1&per_page=20` — Bearer token required. Returns **503** if the upstream countries API is unreachable.

---

## Stopping

```bash
docker compose down
```

MIT.
