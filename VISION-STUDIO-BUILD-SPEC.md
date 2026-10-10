# Vision Studio — Full-Stack Build Specification
### For Claude Code — PHP (Laravel) + MySQL, self-hosted, no third-party DB

---

## 1. Stack Decision (cPanel / shared hosting)

| Layer | Technology | Why |
|---|---|---|
| Backend | **PHP 8.2+ with Laravel 11** | Laravel runs on standard cPanel shared hosting (no Node process needed), has built-in MySQL ORM (Eloquent), migrations, auth, file storage — fastest path to a production admin panel on shared hosting. |
| Database | **MySQL 8 (your own hosting's MySQL, via phpMyAdmin/cPanel)** | No external DB service. All data lives in your hosting account. |
| Frontend (storefront) | **Blade templates + Tailwind CSS + Alpine.js** | Server-rendered pages (good for SEO), styled to match your Claude Design HTML exactly, small JS footprint for interactivity (cart, filters). |
| Admin Panel | **Laravel Blade + Alpine.js** (same stack, separate `/admin` route group, auth-protected) | One codebase, one deploy, no separate app to host. |
| File/Image storage | **Laravel's local `storage/app/public` disk, symlinked to `public/storage`** | Images stay on your own hosting, not S3/Cloudinary. |
| Session/Auth | **Laravel's built-in session + Sanctum (for any AJAX calls)** | No third-party auth provider. |

> If your current hosting plan doesn't give SSH/Composer access, you need a plan that does (most cPanel hosts in BD offer "Composer" + "Setup PHP App" in cPanel — confirm this before starting). Laravel needs Composer to install.

---

## 2. What to upload to Claude Code as design reference

**Upload the HTML source files, not PDF or screenshots.**

Reason: PDF/PNG only shows pixels — Claude Code cannot read exact class names, spacing values, or component structure from an image and will "guess" the rebuild, causing drift from the design. The `.dc.html` files already contain the exact markup, CSS classes, and inline SVG icons — that's the ground truth.

Steps:
1. For each page in the Claude Design canvas (Main, Category, Product, Cart, Checkout, Account, Admin-Dashboard, Admin-Orders, Admin-Products, Admin-Categories, Admin-Customers, Admin-Website, Admin-Marketing, Admin-Reports, Admin-Blog, Admin-Reviews, Admin-Coupons, Admin-Appearance, Admin-Settings), open it and save/export its HTML source as a plain `.html` file.
2. Also take one full-page screenshot per page (PNG) — upload these too, as a *visual* cross-check only, not as the primary source.
3. Put them in a folder structure like:
   ```
   design-reference/
     storefront/Main.html, Category.html, Product.html, Cart.html, Checkout.html, Account.html
     admin/Admin-Dashboard.html, Admin-Orders.html, ... (all 13)
     screenshots/ (matching PNGs, optional but helpful)
   ```
4. Upload the whole `design-reference/` folder to the Claude Code project folder (e.g. `reference/`) before giving it the build prompt below. Tell Claude Code explicitly: *"These HTML files are the exact design — rebuild every page to match them 100%, same classes/spacing/colors, just converted to Blade + real data."*

---

## 3. Master build prompt (paste this into Claude Code)

```
I'm building "Vision Studio," a premium Bangladeshi eyewear e-commerce store.
Stack: PHP 8.2 + Laravel 11, MySQL 8, Blade templates, Tailwind CSS, Alpine.js.
Everything — frontend, backend, admin panel, and database — must run on my own
cPanel hosting. No third-party database, no external SaaS backend, no Firebase/
Supabase. Images stored on local disk (storage/app/public), not S3.

I've uploaded exact HTML design references in /reference/storefront/ and
/reference/admin/ — these are the EXACT target design (colors, spacing, fonts,
components). Rebuild every page from these files 1:1 — same layout, same CSS
classes/values (convert inline <style> into Tailwind config / a shared Blade
component CSS file), same icons (inline SVGs, keep them exactly), same content
structure — but wired to real Eloquent models and real data instead of the
static placeholder content in the HTML.

Build in this order:
1. Laravel project scaffold + MySQL connection (.env) + migrations for all
   tables (see schema in BUILD-SPEC.md section 4).
2. Admin auth (Laravel session-based, single "Super Admin" role to start).
3. Admin panel pages — convert each Admin-*.html into a Blade view at
   /admin/{page}, backed by a controller + Eloquent model + real CRUD:
   Dashboard, Orders, Products, Categories, Customers, Website, Marketing,
   Reports, Blog, Reviews, Coupons, Appearance, Settings.
4. Storefront pages — convert Main/Category/Product/Cart/Checkout/Account.html
   into public Blade views, pulling products/categories/etc. from the same
   database the admin panel edits (so admin changes reflect live on the site).
5. Make the "Website" and "Appearance" admin pages actually control the
   storefront's hero image/headline/colors/logo/footer/social links — store
   these as key-value rows in a `site_settings` table, read them in the
   storefront layout.
6. Image uploads (logo, product photos, category images, blog images, hero
   background) must upload to storage/app/public and save the path in MySQL —
   no hardcoded /_blob/ URLs (those were only for the design mockup).
7. Seed the database with the sample products/orders/customers/categories
   currently shown in the reference HTML, so the site looks populated
   immediately after setup.

Every page must match the reference HTML's visual design at 100% fidelity —
treat any visual difference as a bug to fix, not an acceptable variation.
```

---

## 4. Database schema (MySQL)

```sql
-- core catalog
categories(id, name, slug, description, image_path, banner_path, icon, parent_id, position, status, created_at, updated_at)
products(id, name, slug, sku, category_id, price, compare_price, stock_qty, status, short_desc, long_desc, created_at, updated_at)
product_images(id, product_id, image_path, is_primary, position)
product_variants(id, product_id, name, value, price_delta, stock_qty)  -- e.g. frame color/lens type

-- customers & auth
customers(id, name, email, phone, password, location_city, dob, gender, customer_group, notes, status, created_at)
customer_addresses(id, customer_id, label, address_line, city, is_default)
admins(id, name, email, password, role)

-- orders
orders(id, order_no, customer_id, status, payment_method, payment_status, subtotal, discount, shipping_fee, total, created_at)
order_items(id, order_id, product_id, qty, unit_price, subtotal)

-- marketing
coupons(id, code, title, description, type, value, min_order, max_discount, usage_limit, used_count, starts_at, ends_at, status)
campaigns(id, name, channel, audience_count, starts_at, ends_at, status)
reviews(id, product_id, customer_id, rating, title, comment, status, created_at)
review_images(id, review_id, image_path)

-- content
blog_posts(id, title, slug, category, excerpt, content, featured_image, status, author_id, published_at)
site_settings(id, key, value, group)   -- hero headline/image, logo, footer, socials, theme colors, etc.
```

---

## 5. Folder structure (Laravel)

```
app/Http/Controllers/Admin/{Dashboard,Orders,Products,Categories,Customers,Website,Marketing,Reports,Blog,Reviews,Coupons,Appearance,Settings}Controller.php
app/Http/Controllers/Storefront/{Home,Category,Product,Cart,Checkout,Account}Controller.php
app/Models/{Category,Product,ProductImage,Customer,Order,OrderItem,Coupon,Review,BlogPost,SiteSetting}.php
resources/views/admin/{dashboard,orders,products,categories,customers,website,marketing,reports,blog,reviews,coupons,appearance,settings}.blade.php
resources/views/storefront/{home,category,product,cart,checkout,account}.blade.php
resources/views/layouts/{admin.blade.php,storefront.blade.php}
public/storage -> symlink to storage/app/public
database/migrations/*.php
```

---

## 6. Deployment checklist (cPanel)

1. Confirm hosting plan has: PHP 8.2+, MySQL, Composer/SSH access, "Setup PHP App" in cPanel.
2. Create MySQL database + user in cPanel → put credentials in Laravel `.env`.
3. Point the domain's document root to the Laravel project's `public/` folder.
4. Run `php artisan migrate --seed` once on the server (via SSH or cPanel terminal).
5. Run `php artisan storage:link` so uploaded images are web-accessible.
6. Set `APP_ENV=production`, `APP_DEBUG=false` before going live.

---

**Next step:** upload the `reference/` HTML files + this file to your Claude Code project, then paste the Section 3 prompt.
