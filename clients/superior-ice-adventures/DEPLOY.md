# Superior Ice Adventures — Deploy & Setup

Live domain: **https://superioriceadventures.com**

cPanel-ready PHP site (HTML/CSS/PHP + MySQL). No Node/Composer required at runtime.

## Local (XAMPP)

1. Ensure Apache + MySQL are running in XAMPP.
2. Confirm `includes/db.config.php` exists (gitignored). For first setup:
   ```
   copy includes\db.config.example.php includes\db.config.php
   ```
   Default local credentials: `root` / empty password / database `superior_ice_adventures`.
3. Create tables and seed admin + categories:
   ```
   C:\xampp\php\php.exe database\setup.php
   ```
4. Open: `http://localhost/superior-ice-adventures/`
5. Admin: `http://localhost/superior-ice-adventures/admin/`
   - Username: `admin`
   - Password: `admin123` (change after first login)

Local URLs stay under `/superior-ice-adventures/`. Canonical tags, sitemap, and mail From always use the live domain.

## Production — superioriceadventures.com

1. In your registrar / DNS, point the domain at your host:
   - **A record** `@` → hosting server IP
   - **CNAME** `www` → `superioriceadventures.com` (or same A record)
2. In cPanel → **Domains** / **Addon Domains** / **Subdomains**:
   - Attach `superioriceadventures.com`
   - Set the **document root** to this project folder (site root, not a subfolder)
3. Enable **SSL** (AutoSSL / Let’s Encrypt) for `superioriceadventures.com` and `www`
4. Upload project files (exclude `includes/db.config.php` from your local machine; also skip `_raw/` and `node_modules/`)
5. In cPanel → **MySQL Databases**:
   - Create a database (e.g. `yourprefix_sia`)
   - Create a user and grant **All Privileges**
6. In phpMyAdmin:
   - Select that database
   - Import `database/schema-cpanel.sql`
   - Import `database/seed-cpanel.sql`
7. On the server only, create `includes/db.config.php` from `db.config.example.php` using the **full prefixed** cPanel DB name and user
8. Confirm `mod_rewrite` is on (usual on cPanel). `.htaccess` forces HTTPS and redirects `www` → apex

After DNS + SSL propagate, the site should load at:

- https://superioriceadventures.com/
- https://superioriceadventures.com/admin/
- https://superioriceadventures.com/sitemap.php
- https://superioriceadventures.com/robots.txt

## Content

All marketing copy lives in `includes/config.php`. Identity, services, About, FAQ, gallery, and contact intros are filled for launch.

Site URL settings:

- `$site_domain` = `superioriceadventures.com`
- `$site_url` = `https://superioriceadventures.com`
- `$mail_from` = `noreply@superioriceadventures.com`

### Image pipeline (Soldotna-style)

1. Drop originals in `_raw/images/<folder>/` (see `_raw/README.md`).
2. Run:
   ```
   npm install
   npm run media:images
   ```
3. Optimized `.webp` files land in `assets/img/...` (max width 2400px, quality 82).

Do not upload `_raw/` or `node_modules/` to cPanel — only the processed `assets/img/` WebPs.

## Contact form

Set `$contact_form_to` (and `$email`) in `includes/config.php`. Submissions use PHP `mail()` from `noreply@superioriceadventures.com`. On shared hosting, confirm the host allows outbound mail; create that mailbox or an alias in cPanel if the host requires it. Some hosts require SMTP — switch later if needed.

## Admin blog

- Single admin role (draft / publish)
- Featured article toggle
- Categories CRUD
- Markdown body (headings, lists, bold/italic, links)

## Fonts & colors

- Fonts: Inter + Fraunces (Google Fonts)
- Palette: off-white `#fbf9f5`, flannel red `#710F10`, black `#0a0a0a`
