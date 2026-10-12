# GitHub → cPanel: one-time deploy + auto-deploy on every push

This sets up two things:

1. **First deploy** — get the code from GitHub onto your ExonHost cPanel,
   running.
2. **Auto-deploy** — after that, every `git push` to the `main` branch
   updates the live site on its own, usually within a few seconds. No
   manual upload, no clicking anything in cPanel again.

Do the sections in order. Everything here assumes cPanel is already open
in your browser, as you said.

---

## Part A — First-time deploy (do this once)

### A1. Request SSH access (if you haven't already)

ExonHost doesn't enable SSH/Terminal by default — it's granted on request.
Open a support ticket from your ExonHost client area asking them to
**"enable SSH access for my cPanel account."** This is usually free and
quick. You need this for the one-time setup below (not for the ongoing
auto-deploy — that part runs without you touching SSH again).

### A2. Create the MySQL database

cPanel → **MySQL® Databases**:

1. Create New Database → name it `visionstudio` (cPanel will prefix it,
   e.g. `yourcpaneluser_visionstudio` — note the full name).
2. Add New User → create a user + strong password (note both).
3. Add User To Database → select the user and database, check **All
   Privileges**.

### A3. Clone the repo from GitHub

cPanel → **Git™ Version Control** → **Create**:

- Clone URL: `https://github.com/adibaashrafinfo-source/Vision-Studio.git`
- Repository Path: `/home/yourcpaneluser/vision-studio` (outside
  `public_html` — important, see next step for why)
- Branch: `main`

### A4. Point your domain at the `public/` folder

cPanel → **Domains** → your domain → **Manage** → set **Document Root** to:

```
/home/yourcpaneluser/vision-studio/public
```

(If cPanel won't let you edit the document root for your primary domain,
set this domain up as an **Addon Domain** instead — those always allow a
custom document root.)

### A5. Open Terminal and finish the setup

cPanel → **Terminal** (now that SSH/Terminal is enabled per A1):

```bash
cd ~/vision-studio
composer install --no-dev --optimize-autoloader

cp .env.example .env
nano .env
```

In `.env`, set:

```env
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=yourcpaneluser_visionstudio
DB_USERNAME=yourcpaneluser_vsadmin
DB_PASSWORD=the-password-from-A2

ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@your-domain.com
ADMIN_PASSWORD=choose-a-strong-password

DEPLOY_WEBHOOK_SECRET=
```

Leave `DEPLOY_WEBHOOK_SECRET` empty for now — you'll generate it in Part B.
Save with `Ctrl+O`, Enter, then `Ctrl+X`.

Then:

```bash
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Visit `https://your-domain.com` — the store should be live with the
sample catalog. Log into `/admin/login` with the `ADMIN_EMAIL`/
`ADMIN_PASSWORD` you just set, and **change the password** if you used a
simple one.

---

## Part B — Auto-deploy on every `git push`

Now wire it up so pushing to GitHub updates the live site automatically.

### B1. Generate a secret

In Terminal (still connected from Part A):

```bash
cd ~/vision-studio
php -r "echo bin2hex(random_bytes(20)), PHP_EOL;"
```

Copy the random string it prints — this is your webhook secret.

### B2. Put the secret in `.env`

```bash
nano .env
```

Set:

```env
DEPLOY_WEBHOOK_SECRET=the-random-string-from-b1
```

Save and exit, then:

```bash
php artisan config:clear
php artisan config:cache
```

### B3. Add the webhook on GitHub

On GitHub: open the repo → **Settings** → **Webhooks** → **Add webhook**.

| Field | Value |
|---|---|
| Payload URL | `https://your-domain.com/deploy-webhook` |
| Content type | `application/json` |
| Secret | the same random string from B1 |
| Which events | **Just the push event** |
| Active | ✅ checked |

Click **Add webhook**.

### B4. Test it

Push any small change to `main` from your computer (or click **Redeliver**
on the webhook you just created, under its **Recent Deliveries** tab on
GitHub). Then check:

```bash
tail -f ~/vision-studio/storage/logs/deploy.log
```

You should see something like:

```
Sat Oct 12 ... :: deploying abc1234 -> def5678
... composer install output ...
... migrate output ...
Sat Oct 12 ... :: deploy finished successfully
```

GitHub's webhook page also shows a green checkmark next to the delivery if
your server responded `200 Deploy started.`

From now on: **push to `main` → live site updates within a few seconds,
automatically.**

---

## How it works (for your own understanding, not required reading)

- GitHub sends a POST request to `/deploy-webhook` on every push, signed
  with your secret (`X-Hub-Signature-256` header).
- `DeployWebhookController` checks that signature matches before doing
  anything — a stranger who finds the URL can't trigger a deploy without
  knowing your secret.
- It only acts on pushes to `main`, and starts `deploy.sh` in the
  background so GitHub gets an instant response (it only waits ~10
  seconds before considering a webhook "failed").
- `deploy.sh` itself double-checks there's actually a new commit before
  doing anything (`git fetch` + compare), and uses a lock file so two
  deploys can never run on top of each other.

---

## If your host blocks background processes (fallback)

Some very locked-down shared hosts disable PHP's ability to start
background processes (`proc_open`). If `storage/logs/deploy.log` never
appears after a push, or the webhook delivery on GitHub shows a red ✕ with
"Could not start deploy script", set up a safety-net cron job instead —
it polls for new commits every few minutes rather than reacting instantly,
but needs nothing from PHP:

cPanel → **Cron Jobs** → **Add New Cron Job**:

- **Common Settings:** Every 5 Minutes (`*/5 * * * *`)
- **Command:**
  ```
  /bin/bash /home/yourcpaneluser/vision-studio/deploy.sh
  ```

This is safe to run alongside the webhook (it does nothing when there's no
new commit), or entirely on its own if the webhook doesn't work on your
plan.

---

## Troubleshooting

| Symptom | Fix |
|---|---|
| GitHub shows a red ✕ on the webhook delivery | Check `Response` tab on that delivery in GitHub — it shows the exact error message your server returned. |
| "Invalid signature." | The secret in GitHub's webhook settings doesn't match `DEPLOY_WEBHOOK_SECRET` in `.env` — re-copy it exactly, then `php artisan config:clear && php artisan config:cache`. |
| "Webhook secret not configured on server." | `DEPLOY_WEBHOOK_SECRET` is empty in `.env`, or you forgot to `config:cache` after setting it. |
| "Could not start deploy script" | Your host disabled PHP background processes — use the cron fallback above. |
| Deploy log shows an error mid-way (e.g. composer or migrate failing) | The same commands run fine from Terminal — run them manually from `~/vision-studio` to see the full error and fix it, then push again. |
