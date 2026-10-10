# Quick live preview on Railway (before cPanel)

This gets you a clickable `https://....up.railway.app` URL to click around the
real site from your own phone/browser — useful to sign off on the design
before the final cPanel deploy. Railway supports real MySQL, so this is a
much closer preview than SQLite.

**Note:** uploaded images (product/category/blog photos, logo, hero image)
live on Railway's container disk, which is *ephemeral* — they can disappear
on a redeploy unless you attach a Volume. Fine for a design review; not
meant to be the permanent home for your catalog images. The final cPanel
deploy (per `README.md`) is where that becomes persistent.

## 1. Create the project

1. Go to [railway.app](https://railway.app) → sign in with GitHub.
2. **New Project → Deploy from GitHub repo** → pick `adibaashrafinfo-source/Vision-Studio`.
   - Branch: `main` (or `claude/vision-studio-scaffold` if you want the very latest).
3. Railway detects PHP from `composer.json` automatically and reads
   `railway.toml` (already in the repo) for the start command.

## 2. Add a MySQL database

1. In the same project: **+ New → Database → Add MySQL**.
2. Click the new MySQL service → **Variables** tab. You'll need 5 values
   from here: `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`,
   `MYSQLPASSWORD`.

## 3. Set environment variables on the web service

Click your **web service** (not the MySQL one) → **Variables** → add these:

```
APP_NAME=Vision Studio
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Dhaka
APP_LOCALE=en
APP_KEY=base64:O576SbrOmSiawJJz1SUOsCnDPevhJdyESe92lgOZvvw=

DB_CONNECTION=mysql
DB_HOST=<paste MYSQLHOST from the MySQL service>
DB_PORT=<paste MYSQLPORT>
DB_DATABASE=<paste MYSQLDATABASE>
DB_USERNAME=<paste MYSQLUSER>
DB_PASSWORD=<paste MYSQLPASSWORD>

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

ADMIN_NAME=Vision Studio Admin
ADMIN_EMAIL=admin@visionstudio.test
ADMIN_PASSWORD=ChangeThisPassword123
```

> The `APP_KEY` above is a freshly generated one for this deployment —
> don't reuse it for the real cPanel production site; generate another
> with `php artisan key:generate --show` when you deploy there.

## 4. Get a public URL

1. On the web service → **Settings → Networking → Generate Domain**.
2. Railway gives you `https://your-app.up.railway.app`. Add one more variable:
   ```
   APP_URL=https://your-app.up.railway.app
   ```
   (paste the exact domain Railway generated for you)
3. Redeploy (Railway usually redeploys automatically when variables change;
   if not, click **Deploy** on the latest commit).

## 5. Open it

Wait for the deploy to go green, then open the Railway URL:

- **Storefront:** `/` — home, `/products`, `/product/{slug}`, `/cart`, `/checkout`
- **Admin panel:** `/admin/login` → `admin@visionstudio.test` / the password
  you set above.

The `migrate --force --seed` in the start command creates the database
tables and the admin account automatically on first boot — no SSH/terminal
needed. Every subsequent deploy re-runs it safely (migrations and the admin
seeder are idempotent).

## When you're happy with the design

Follow the **Deployment checklist (cPanel)** section in `README.md` to put
it on your real hosting — that's the permanent, spec-compliant target with
persistent image storage.
