# Deploying Vision Studio to cPanel (production)

This is the real, final deployment — your own hosting, your own MySQL
database, images stored permanently on that server's disk. No third-party
services involved.

Follow the steps in order. Where your host's cPanel looks slightly
different (every host skins it a bit differently), the **icon name is the
same everywhere** — search for it in the cPanel search box if you can't
find it.

> **You're on ExonHost — one thing to do first.** ExonHost runs standard
> cPanel, so every step below applies as written. The one difference: on
> ExonHost, **SSH/Terminal access is not turned on by default** — it's
> granted on request. Before step 4, open a support ticket from your
> ExonHost client area asking them to **"enable SSH access for my cPanel
> account"**. This is usually free and takes them a few hours, not days —
> do this first so it's ready by the time you need it.

---

## 0. Before you start — what your hosting plan needs

Open cPanel → **Software** section and confirm you have:

- **PHP 8.2 or higher** (check under "Select PHP Version" / "MultiPHP Manager")
- **MySQL** databases (under "MySQL® Databases")
- **Terminal** or **SSH Access** (under "Advanced") — you need this to run a
  few one-time commands. On ExonHost this is granted on request (see the
  note above) rather than shown by default — if you don't see it yet,
  that's expected until support enables it for you.
- **Git™ Version Control** (under "Files") — optional but makes step 2 much
  easier.

If "Terminal"/"SSH Access" is genuinely not available on your plan, stop
here and ask your host to enable it (or move to a plan that has it) before
continuing — the steps below assume you have it.

---

## 1. Create the MySQL database

cPanel → **MySQL® Databases**.

1. Under **Create New Database**, type `visionstudio` → Create Database.
   cPanel will actually name it something like `yourcpaneluser_visionstudio`
   — that's normal, write down the **full name**.
2. Under **MySQL Users → Add New User**, create a user (e.g. `vsadmin`) with
   a strong password. Write the password down. cPanel will name it
   `yourcpaneluser_vsadmin`.
3. Under **Add User To Database**, add that user to the database you just
   created, with **All Privileges** checked.

You now have three values you'll need in step 5:
`yourcpaneluser_visionstudio`, `yourcpaneluser_vsadmin`, and the password.

---

## 2. Upload the code

**Option A — Git (recommended, easiest to update later):**

cPanel → **Git™ Version Control** → **Create**:

- Clone URL: `https://github.com/adibaashrafinfo-source/Vision-Studio.git`
- Repository Path: something outside `public_html`, e.g.
  `/home/yourcpaneluser/vision-studio` (NOT inside `public_html` — see step 3
  for why).
- Checkout the `main` branch.

**Option B — upload a zip:**

On your own computer: `git clone` the repo, zip the folder (or download it
as a zip from GitHub → Code → Download ZIP), then cPanel → **File Manager**
→ upload the zip to a folder **outside** `public_html` (e.g.
`/home/yourcpaneluser/vision-studio`) → right-click → **Extract**.

Either way, you should end up with the project's files (the ones with
`artisan`, `composer.json`, `app/`, `public/`, etc.) sitting in
`/home/yourcpaneluser/vision-studio`.

---

## 3. Point the domain at the `public/` folder

Laravel's web root is the project's `public/` subfolder, not the project
root — this keeps `.env`, `app/`, and everything else outside what a
visitor's browser can reach.

cPanel → **Domains** (or **Addon Domains** if this isn't your primary
domain) → find your domain → **Manage** → change **Document Root** to:

```
/home/yourcpaneluser/vision-studio/public
```

Save. (If your cPanel plan doesn't let you edit the document root for the
primary domain, use an **Addon Domain** instead, which always lets you set a
custom document root — point it at the same `public` folder.)

---

## 4. Open Terminal and install dependencies

cPanel → **Terminal**.

```bash
cd ~/vision-studio
composer install --no-dev --optimize-autoloader
```

If `composer: command not found`, try `composer1` or `composer2`, or ask
your host which alias they use — nearly every host has one.

---

## 5. Configure `.env`

Still in Terminal:

```bash
cp .env.example .env
nano .env
```

Edit these lines (save with `Ctrl+O`, Enter, then exit with `Ctrl+X`):

```env
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourcpaneluser_visionstudio
DB_USERNAME=yourcpaneluser_vsadmin
DB_PASSWORD=the-password-you-set-in-step-1

ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@your-domain.com
ADMIN_PASSWORD=choose-a-strong-password-now
```

> **Important:** on cPanel, `DB_HOST` is almost always `localhost`, not
> `127.0.0.1` — using the wrong one is the #1 cause of a "could not connect"
> error on shared hosting.

Then generate the app key and run migrations + seed the sample data:

```bash
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
```

`--seed` creates your admin account (from the `ADMIN_*` values you just set)
plus the sample categories/products/orders so the store isn't empty on day
one. Safe to re-run later — it won't duplicate anything.

---

## 6. Set folder permissions

Laravel needs to write to two folders. Still in Terminal:

```bash
chmod -R 775 storage bootstrap/cache
```

If your host runs PHP as a different user than your shell user, you may
instead need `chmod -R 777 storage bootstrap/cache` — try 775 first, only
go to 777 if you get a "permission denied" / "could not write" error on the
site.

---

## 7. Production performance (recommended)

Still in Terminal, cache the config/routes/views for faster page loads:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> If you change anything in `.env` later, run `php artisan config:clear`
> first, make the change, then `config:cache` again — otherwise the old
> cached values keep being used.

---

## 8. Go live checklist

- [ ] Visit `https://your-domain.com` — homepage loads with real hero image,
      categories, flash sale, best sellers.
- [ ] Visit `https://your-domain.com/admin/login` — log in with the
      `ADMIN_EMAIL` / `ADMIN_PASSWORD` you set in step 5.
- [ ] In admin, upload a new product image and confirm it displays on the
      storefront (confirms `storage:link` + folder permissions worked).
- [ ] Place a real test order through checkout, confirm it appears in
      admin → Orders.
- [ ] cPanel → **SSL/TLS Status** → make sure AutoSSL is on and your domain
      shows a green padlock (free SSL, usually automatic within an hour of
      pointing the domain here).
- [ ] Double-check `.env` has `APP_ENV=production` and `APP_DEBUG=false`
      (both already set in `.env.example` — just don't change them).

---

## Updating the site later

Whenever you push new code to GitHub:

```bash
cd ~/vision-studio
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear && php artisan config:cache
php artisan route:cache && php artisan view:cache
```

(If you used the Git Version Control UI instead of plain `git`, it has a
**"Pull or Deploy"** button that does the `git pull` part for you.)

---

## Troubleshooting

| Symptom | Likely cause |
|---|---|
| 500 Server Error, blank page | Run `tail -50 storage/logs/laravel.log` in Terminal for the real error. Usually a missing `APP_KEY` (`php artisan key:generate`) or wrong folder permissions (step 6). |
| "SQLSTATE[HY000] [2002]" on any page | `DB_HOST` should be `localhost`, not `127.0.0.1`, on cPanel. |
| "Access denied for user" | Re-check `DB_USERNAME`/`DB_PASSWORD` in `.env`, and that the user was added to the database with **All Privileges** in step 1. |
| Homepage loads but with no styling / broken layout | Document root isn't pointed at `public/` (step 3) — you're seeing the raw project folder instead. |
| Uploaded images don't show, broken image icon | `php artisan storage:link` wasn't run, or `public/storage` isn't a symlink — re-run step 5's last command. |
| Changes to `.env` don't seem to take effect | You ran `config:cache` before editing — run `php artisan config:clear` first, edit, then `config:cache` again. |
