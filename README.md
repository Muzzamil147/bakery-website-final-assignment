# Golden Crust Bakery — Website + Admin Panel

A responsive website for a local bakery, with a secure admin panel for managing
products, team members, gallery photos, and contact messages. Built with PHP
and MySQL for a "Final Project" web development assignment.

## Tech Stack

- **PHP** 8.x (no framework — plain PHP with PDO)
- **MySQL** 8.x
- Vanilla HTML/CSS/JS (no build step, no frontend framework)

## Pages

**Public site:**
- `index.php` — Home (hero, features, featured products)
- `about.php` — About (business story, team members)
- `services.php` — Products menu, grouped by category
- `gallery.php` — Photo gallery
- `contact.php` — Contact form (saves to database)

**Admin panel** (`/admin`):
- `login.php` — Secure login (bcrypt password hashing, session-based auth)
- `dashboard.php` — Overview stats + recent contact messages
- `products.php` — Full CRUD for products, with image upload
- `categories.php` — Full CRUD for product categories
- `team.php` — Full CRUD for team members, with image upload
- `gallery.php` — Full CRUD for gallery photos, with image upload
- `messages.php` — View / mark read / delete contact form submissions

## Database

6 tables, with a real foreign-key relationship (`products.category_id → categories.id`):

- `admins` — admin login accounts
- `categories` — product categories
- `products` — bakery products (belongs to a category)
- `team_members` — staff bios
- `gallery` — photo gallery
- `contact_messages` — contact form submissions

Schema + seed data: [`database.sql`](database.sql)

## Setup

**1. Create the database:**
```bash
mysql -u root < database.sql
```
This creates the `golden_crust_bakery` database, all 6 tables, and seed data
(sample products, team members, gallery photos, and one admin account).

**2. Create a database user** (or edit `config/database.php` to use one you
already have):
```sql
CREATE USER 'bakery_app'@'localhost' IDENTIFIED BY 'BakeryApp!2026';
GRANT SELECT, INSERT, UPDATE, DELETE ON golden_crust_bakery.* TO 'bakery_app'@'localhost';
FLUSH PRIVILEGES;
```
These credentials are already set in `config/database.php` — change them there
if you use different ones.

**3. Start the PHP built-in server** (run from this folder):
```bash
php -S localhost:8080
```

**4. Open the site:**
- Public site: http://localhost:8080
- Admin panel: http://localhost:8080/admin/login.php

**Default admin login:**
- Username: `admin`
- Password: `bakery123`

## Security Notes

- Passwords are hashed with PHP's `password_hash()` (bcrypt) — never stored in plain text.
- All database queries use PDO **prepared statements** — no raw string concatenation, so no SQL injection.
- All admin forms include a **CSRF token**, checked on every POST.
- Image uploads are validated by real file content (`finfo`/MIME sniffing), not just
  the filename extension — a `.png`-renamed script is rejected. Files are also capped at 5MB
  and saved under a random filename.
- All admin pages check `is_logged_in()` and redirect to login if there's no valid session.
- All user-generated output is escaped with `htmlspecialchars()` before being echoed into HTML.

## Notes for Screenshots / Demo Video

Good moments to capture for the submission deliverables:
1. Home page (desktop + mobile width)
2. Products page showing categories
3. Admin login
4. Admin dashboard with live stats
5. Adding a product with an image, then seeing it appear on the public Products page
6. Editing and deleting a product
7. Submitting the public contact form, then seeing it appear in Admin → Messages
