# Quick live preview on Vercel (before cPanel)

**Heads-up before you start:** Vercel doesn't run PHP natively. This deploys
Vision Studio as a self-contained Docker container (PHP 8.2 + FrankenPHP/Caddy,
built from `Dockerfile.vercel`) that Vercel runs as a Function. It's a genuine
clickable preview, but it is **not** the final production setup — that's
still cPanel + MySQL, per the checklist in `README.md`.

I built and test-compiled this image locally before handing it off, so the
main risk (Docker build failing) is already covered. What's left is config
on your end: environment variables, same as any other host.

## 1. Reuse the MySQL database (you already made one)

You created a MySQL database on Railway earlier — keep it. You don't need a
new database; Vercel just needs the connection details for that same one.

Go to Railway → your **MySQL** service → **Variables** tab → note down the
real values (not the `${{...}}` reference) for:
`MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`.

(If you'd rather not depend on Railway at all, any other MySQL host works —
PlanetScale, Aiven, Clever Cloud all have free tiers. The env vars below are
the same either way.)

## 2. Import the project into Vercel

1. Go to [vercel.com/new](https://vercel.com/new) → **Import Git Repository**
   → select `adibaashrafinfo-source/Vision-Studio`.
2. Vercel detects `Dockerfile.vercel` + `vercel.json` automatically and
   builds it as a container Function — no framework preset needed.

## 3. Set environment variables

In the project's **Settings → Environment Variables**, add:

```
APP_NAME=Vision Studio
APP_ENV=production
APP_DEBUG=true
APP_TIMEZONE=Asia/Dhaka
APP_LOCALE=en
APP_KEY=base64:O576SbrOmSiawJJz1SUOsCnDPevhJdyESe92lgOZvvw=

DB_CONNECTION=mysql
DB_HOST=<the real MYSQLHOST value from Railway>
DB_PORT=<the real MYSQLPORT value from Railway>
DB_DATABASE=<the real MYSQLDATABASE value from Railway>
DB_USERNAME=<the real MYSQLUSER value from Railway>
DB_PASSWORD=<the real MYSQLPASSWORD value from Railway>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

ADMIN_NAME=Vision Studio Admin
ADMIN_EMAIL=admin@visionstudio.test
ADMIN_PASSWORD=ChangeThisPassword123
```

Why `SESSION_DRIVER=database` here specifically: Vercel can run multiple
copies of the container and recycle them, so sessions/cache can't safely
live on local disk like they would on a normal server — they need to sit in
MySQL instead. (The `sessions` table migration for this is already in the
repo.) `APP_DEBUG=true` is left on so if anything is still misconfigured you
see the real Laravel error page instead of a blank 500 — set it back to
`false` once the site is confirmed working.

## 4. Deploy

Click **Deploy**. The container's startup command runs
`php artisan migrate --force --seed` automatically before serving — no
console/SSH step needed, and it's safe to re-run on every redeploy.

## 5. Open it

Vercel gives you a `https://your-project.vercel.app` URL.

- **Storefront:** `/`, `/products`, `/product/{slug}`, `/cart`, `/checkout`
- **Admin panel:** `/admin/login` → `admin@visionstudio.test` / the password
  you set above.

## Known limitations of this preview (fine for a design review, not for launch)

- **Uploaded images are not persistent.** Product/category/blog photos, the
  logo, and the hero image you upload through the admin panel live on the
  container's local disk, which Vercel can recycle at any time. They can
  vanish on the next deploy or scale event. The final cPanel deploy is where
  uploads become permanently persistent.
- **Cold starts.** If the container has been idle, the first request after
  a while can take a few seconds longer while it spins back up.

## When you're happy with the design

Follow the **Deployment checklist (cPanel)** section in `README.md` — that's
the permanent, spec-compliant target with persistent image storage.
