# Countries

Small app: Auth0 login, then browse countries (name + flag). Data comes from [REST Countries](https://restcountries.com), cached for an hour, paginated in PHP.

**Stack:** Laravel, Vue 3, Vite, Tailwind, Auth0, Docker (PHP-FPM + nginx).

---

## You need

Docker with Compose (v2.20+). An Auth0 account.

---

## Run it

```bash
cp .env.example .env
docker-compose up -d
```

(`docker compose up -d` works too.)

First start runs Composer, migrations, and a one-off frontend build — give it a few minutes. Then open **http://localhost:8080**.

---

## Auth0 (fill `.env` before `up`)

1. **API** — Auth0 → APIs → Create. Copy the **Identifier** → `AUTH0_AUDIENCE`.
2. **SPA** — Applications → Create → Single Page App. Copy **Client ID** → `VITE_AUTH0_CLIENT_ID`.
3. **Domain** — Your tenant (e.g. `dev-xxx.us.auth0.com`) → `AUTH0_DOMAIN`.
4. In the API’s **Applications** tab, authorize that SPA.
5. SPA **Settings**: Callback `http://localhost:8080/callback`, Logout `http://localhost:8080`, Web origin `http://localhost:8080`.

`APP_URL` / `VITE_APP_URL` should stay `http://localhost:8080` for this setup.

---

## Changing Auth0 or `VITE_*` in `.env`

Delete `public/build` and run `docker-compose up -d` again so Vite rebuilds.

---

## API

`GET /api/countries?page=1&per_page=20` — Bearer token required. **503** if the countries API is unreachable.

MIT.
