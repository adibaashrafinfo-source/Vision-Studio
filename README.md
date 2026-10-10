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
- [ ] Step 5 — `site_settings` → live storefront control (hero, logo, footer, socials, theme)
- [ ] Step 6 — Image upload wiring
- [ ] Step 7 — Seed sample data

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

## Deployment checklist (cPanel)

1. Confirm the hosting plan has PHP 8.2+, MySQL, Composer / SSH, and "Setup PHP App".
2. Create a MySQL database + user in cPanel and put the credentials in `.env`
   (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
3. Point the domain's document root at this project's `public/` folder.
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan key:generate` (if `APP_KEY` is empty), then `php artisan migrate --seed`.
6. `php artisan storage:link` so uploaded images are web-accessible.
7. Set `APP_ENV=production` and `APP_DEBUG=false` before going live.
