# Vision Studio

A premium Bangladeshi eyewear e-commerce store — storefront + admin panel — built to run
entirely on standard cPanel shared hosting. No third-party database, no external SaaS
backend, images stored on local disk.

## Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+ · **Laravel 11** |
| Database | **MySQL 8** (your own cPanel MySQL) |
| Storefront | Blade templates · Tailwind CSS · Alpine.js |
| Admin panel | Laravel Blade (auth-protected `/admin` route group) |
| Image storage | Laravel local `storage/app/public`, symlinked to `public/storage` |

The design is rebuilt 1:1 from the HTML references in `design-reference/` (shipped as
`Main Storefront Design Reference.zip` and `Admin Dashboard.zip`).

## Build progress

- [x] **Step 1** — Laravel scaffold, MySQL `.env` config, all migrations + Eloquent models
- [x] **Step 2** — Admin auth (session-based `admin` guard, Super Admin, branded login)
- [x] **Step 3** — Admin panel pages, built 1:1 from the reference design with a shared
      navy shell (sidebar + topbar) and real controllers/CRUD: Dashboard, Orders,
      Products, Categories, Customers, Website, Marketing, Reports, Blog, Reviews,
      Coupons, Appearance, Settings
- [x] **Step 4** — Storefront pages (Home, Category, Product, Cart, Checkout, Account)
      1:1 from the reference, wired to live data: session cart, coupons, real
      checkout → orders, customer auth (register/login/account)
- [x] **Step 5** — `site_settings` → live storefront control: hero, logo, footer,
      socials, announcement, theme colours, store name, shipping, and payment-method
      toggles all set in admin and read live by the storefront
- [x] Step 6 — Image upload wiring
- [x] Step 7 — Seed sample data
- [x] Step 8 — cPanel production deployment guide ([CPANEL-DEPLOY.md](./CPANEL-DEPLOY.md))
- [x] Step 9 — GitHub push → auto-deploy webhook ([DEPLOY-WEBHOOK.md](./DEPLOY-WEBHOOK.md))

## Database schema

15 tables (see `database/migrations/2026_10_10_*`):

`categories`, `products`, `product_images`, `product_variants`, `admins`, `customers`,
`customer_addresses`, `orders`, `order_items`, `coupons`, `campaigns`, `reviews`,
`review_images`, `blog_posts`, `site_settings`.

## Local development

```bash
composer install
cp .env.example .env         # then set DB_CONNECTION=sqlite for a quick local run
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install && npm run dev    # Vite (Tailwind + Alpine)
php artisan serve
```

> The committed `.env.example` targets **MySQL** (production). For a zero-setup local run,
> set `DB_CONNECTION=sqlite` and `touch database/database.sqlite`.

### Admin login

After `php artisan migrate --seed`, sign in at **`/admin/login`** with the seeded
Super Admin (defaults: `admin@visionstudio.test` / `password` — override via the
`ADMIN_*` vars in `.env`). Create more admins with `php artisan vision:make-admin`.

## Quick live preview (before cPanel)

Want a clickable link to review the design before the final cPanel deploy?
Two options, both documented step-by-step:

- **[VERCEL-DEPLOY.md](./VERCEL-DEPLOY.md)** — runs as a Docker container
  (PHP 8.2 + FrankenPHP); the Dockerfile was built and smoke-tested locally
  before being committed.
- **[RAILWAY-DEPLOY.md](./RAILWAY-DEPLOY.md)** — simpler Nixpacks-based PHP
  build, no Dockerfile involved.

Either way, uploaded images on that preview are **not persistent** — only
the final cPanel deploy below stores them permanently.

## Deployment (cPanel — production)

Full step-by-step guide, written for cPanel specifically (MySQL setup,
document root, Terminal commands, permissions, and a troubleshooting
table): **[CPANEL-DEPLOY.md](./CPANEL-DEPLOY.md)**.

Want `git push` to update the live site automatically, with no manual
redeploy step? **[DEPLOY-WEBHOOK.md](./DEPLOY-WEBHOOK.md)** sets up a
GitHub webhook that redeploys on every push to `main`.
