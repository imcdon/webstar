# Superior Ice Adventures — Deploy & Setup

**Staging (Webstar):** https://superior-ice-adventures.webstarbusinessservices.com  
**Live (future):** https://superioriceadventures.com

cPanel-ready PHP site (HTML/CSS/PHP + MySQL). No Node/Composer required at runtime. Staging is password-gated and cohesive with [Webstar Business Services](https://github.com/imcdon/webstar) (`clients/superior-ice-adventures/`).

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
   HTTP Basic Auth uses Webstar portal credentials (`webstar` / `superior`).
5. Admin: `http://localhost/superior-ice-adventures/admin/`
   - Username: `admin`
   - Password: `admin123` (change after first login)

Local URLs stay under `/superior-ice-adventures/`. Canonical tags, sitemap, and mail From always use the live domain.

## Staging — superior-ice-adventures.webstarbusinessservices.com

### Git deploy (recommended)

1. In cPanel → **Domains** / **Subdomains**:
   - Subdomain: `superior-ice-adventures`
   - Domain: `webstarbusinessservices.com`
   - Document root: `$HOME/superior-ice-adventures.webstarbusinessservices.com`
2. Enable **SSL** for `superior-ice-adventures.webstarbusinessservices.com`
3. In cPanel → **Git Version Control**, clone this repo on the server.
4. Confirm [`.cpanel.yml`](.cpanel.yml) deploys to **`$HOME/superior-ice-adventures.webstarbusinessservices.com/`** — not `public_html`.
5. Create `includes/db.config.php` **once** on the server (never overwrite from Git — deploy excludes it).
6. Pull / Deploy from cPanel when you push to GitHub.

Alternatively, deploying the Webstar repo also rsyncs `clients/superior-ice-adventures/` into the same document root.

### Staging auth

HTTP Basic Auth (same as Webstar Client Portal):

- Admin: `webstar`
- Client: `superior`

Gate is **on** for `*.webstarbusinessservices.com` and localhost; **off** on live `superioriceadventures.com`.

### Database (first-time)

1. cPanel → **MySQL Databases**: create DB + user, grant All Privileges
2. phpMyAdmin: import `database/schema-cpanel.sql` then `database/seed-cpanel.sql`
3. On the server only, create `includes/db.config.php` from `db.config.example.php` using the full prefixed cPanel DB name and user

After SSL and deploy:

- https://superior-ice-adventures.webstarbusinessservices.com/
- https://superior-ice-adventures.webstarbusinessservices.com/admin/

## Production — superioriceadventures.com

When the live domain is ready:

- Document root: `$HOME/superioriceadventures.com` (not `public_html`)
- Point DNS A/CNAME + SSL at that folder
- Staging gate stays off automatically on `superioriceadventures.com`
- Keep `$site_domain` / `$site_url` / `$mail_from` as `superioriceadventures.com` (already set)

## Content

All marketing copy lives in `includes/config.php`.

Site URL settings (canonical / live):

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
