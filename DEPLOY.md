# Deploy: GitHub → cPanel (Git pull)

Production domain: **https://webstarbusinessservices.com**

Workflow: edit locally → `git push` → cPanel **Git Version Control** → **Pull / Update**.

```text
Local XAMPP  →  GitHub (main)  →  cPanel clone (document root)
```

Superior Ice and other client staging sites use **separate** GitHub repos (see [clients/README.md](clients/README.md) and [portal/README.md](portal/README.md)).

---

## What is in this repo

- Main marketing site (PHP)
- Client portal (`portal/`)
- `vendor/` (PHPMailer) so production does not need Composer after pull
- Example configs: `library/mail-config.example.php`, `library/portal-clients.example.php`

## What is NOT in Git (server-only / separate repos)

| Path | Notes |
|------|--------|
| `library/mail-config.php` | SMTP secrets — create on server from the example |
| `library/portal-clients.php` | Portal users + password hashes — create on server from the example |
| `clients/superior-ice-adventures/` | Deploy from the Superior Ice repo into the staging subdomain |

---

## Local setup (once)

1. Clone this repo (or use your existing XAMPP copy).
2. Copy `library/mail-config.example.php` → `library/mail-config.php` and fill SMTP values for local testing.
3. Copy `library/portal-clients.example.php` → `library/portal-clients.php` and set password hashes:
   ```bash
   php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```
4. Open `http://localhost/webstar-business-solutions/` (adjust folder name if different).

`vendor/` is committed. If you change Composer dependencies locally:

```bash
composer install --no-dev
git add composer.lock vendor
```

---

## First push to GitHub (done when the repo exists)

1. Empty GitHub repository (no auto README), e.g. `webstar-business-services`.
2. From the project root:
   ```bash
   git init
   git add .
   git status   # confirm mail-config.php, portal-clients.php, clients/superior-ice-adventures/ are NOT listed
   git commit -m "Initial commit: Webstar Business Services site and portal"
   git branch -M main
   git remote add origin git@github.com:YOUR_USER/YOUR_REPO.git
   git push -u origin main
   ```

---

## cPanel one-time setup

### 1. DNS

At your registrar / Reclaim DNS:

- **A** `@` → hosting server IP
- **www** → same A record or CNAME per host docs

### 2. Domain + document root

1. cPanel → **Domains** → add **webstarbusinessservices.com** (if not already).
2. Choose a clone path that will contain **`index.php` at the top level**, for example:
   - `/home/YOURUSER/webstarbusinessservices.com`
3. Set the domain **document root** to that same folder (not a parent that would force `/webstar-business-solutions/` in the public URL).

### 3. Clone the GitHub repo (Git Version Control)

1. cPanel → **Git Version Control** → **Create** / **Clone**.
2. **Clone URL** (pick one):
   - **SSH (recommended for private repos):** `git@github.com:YOUR_USER/YOUR_REPO.git`  
     Add a **read-only deploy key** from cPanel (or generate SSH key in cPanel → SSH Access) as a Deploy Key on the GitHub repo (Settings → Deploy keys).
   - **HTTPS:** `https://github.com/YOUR_USER/YOUR_REPO.git` with a fine-grained personal access token (Contents: read). Rotate the token periodically.
3. **Repository Path** = the document root from step 2 (e.g. `/home/YOURUSER/webstarbusinessservices.com`).
4. Branch: **main**.
5. Clone / create the repository.

### 4. SSL

cPanel → **SSL/TLS Status** (or Let’s Encrypt / AutoSSL) → enable for:

- `webstarbusinessservices.com`
- `www.webstarbusinessservices.com`

### 5. Server-only config files

On the server (File Manager or SSH), inside the clone root:

1. Copy `library/mail-config.example.php` → `library/mail-config.php`  
   Fill Reclaim mailbox / SMTP (`smtp_host`, user, password, `mail_from`, `mail_to`, etc.).
2. Copy `library/portal-clients.example.php` → `library/portal-clients.php`  
   Paste real `password_hash` values for admin (`webstar`) and each client. **Change passwords after go-live.**
3. Do **not** commit these files. After each Git pull they stay on the server (Git ignores them if you never add them; if a pull ever conflicts, re-create from the examples).

`vendor/` is already in the repo — no `composer install` required on the server for normal pulls.

### 6. Superior Ice staging (separate repo)

1. cPanel → **Subdomains** → `superior-ice-adventures` under `webstarbusinessservices.com`.
2. Document root → e.g. `/home/YOURUSER/webstarbusinessservices.com/clients/superior-ice-adventures` (create the folder if needed).
3. **Separate** Git Version Control clone of the Superior Ice Adventures GitHub repo into that document root (or deploy via SFTP from that repo).
4. SSL for `superior-ice-adventures.webstarbusinessservices.com`.
5. Ensure production `library/portal-clients.php` has:
   - `staging_url` → `https://superior-ice-adventures.webstarbusinessservices.com`

More portal notes: [portal/README.md](portal/README.md).

---

## Ongoing deploy

1. Develop and test on local XAMPP.
2. Commit and push to `main`:
   ```bash
   git add -A
   git status
   git commit -m "Describe the change"
   git push origin main
   ```
3. cPanel → **Git Version Control** → select this repo → **Update** / **Pull**.
4. Smoke-test (below).  
   Server files `mail-config.php` and `portal-clients.php` are untouched by pull.

Optional later: a cPanel cron that runs `git -C /path/to/site pull` — only if you want automatic deploys without a manual gate.

---

## Post-deploy checklist

| Check | Expect |
|-------|--------|
| `https://webstarbusinessservices.com/` | Home loads; brand is Webstar Business Services |
| `https://webstarbusinessservices.com/portal/` | Login page |
| Admin login | All Projects list (after `portal-clients.php` is configured) |
| Contact / package intake | Email arrives (after `mail-config.php`) |
| `https://webstarbusinessservices.com/sitemap.php` | Sitemap XML |
| Push → Pull | A small text change appears on live |
| SIA staging subdomain | Separate repo loads; portal Open preview uses `staging_url` on production |

---

## Troubleshooting

- **Site only loads under `/webstar-business-solutions/`** — document root is wrong; point the domain at the folder that contains `index.php`.
- **Contact form “config” error** — missing or placeholder `library/mail-config.php` on the server.
- **Portal login fails** — missing `library/portal-clients.php` or wrong hashes on the server.
- **cPanel cannot clone** — deploy key / PAT permissions; confirm branch name is `main`.
- **Pull overwrote something** — never commit secrets; restore server-only files from examples if needed.
