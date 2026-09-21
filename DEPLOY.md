# Deploy: GitHub → cPanel (Git + `.cpanel.yml`)

Production domain: **https://webstarbusinessservices.com**  
GitHub: **https://github.com/imcdon/webstar** (`main`)

Workflow: edit locally → `git push` → cPanel **Update from Remote** → **Deploy HEAD Commit** (runs [`.cpanel.yml`](.cpanel.yml)).

```text
Local XAMPP  →  GitHub (main)  →  cPanel repo clone  →  Deploy (.cpanel.yml)  →  document root
```

Superior Ice and other client staging sites use **separate** GitHub repos (see [clients/README.md](clients/README.md) and [portal/README.md](portal/README.md)).

---

## What is in this repo

- Main marketing site (PHP)
- Client portal (`portal/`)
- `vendor/` (PHPMailer) so production does not need Composer after pull
- [`.cpanel.yml`](.cpanel.yml) — cPanel deploy tasks (rsync into the public document root)
- Example configs: `library/mail-config.example.php`, `library/portal-clients.example.php`

## What is NOT in Git (server-only / separate repos)

| Path | Notes |
|------|--------|
| `library/mail-config.php` | SMTP secrets — create on server from the example |
| `clients/superior-ice-adventures/` | Deploy from the Superior Ice repo into the staging subdomain |

Portal users live in committed [`library/portal-clients.php`](library/portal-clients.php) (hashed passwords). Change hashes after go-live if needed.


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

## First push to GitHub

Canonical repo: **https://github.com/imcdon/webstar** (`main`).

If setting up a fresh clone:

```bash
git init
git add .
git status   # confirm mail-config.php, portal-clients.php, clients/superior-ice-adventures/ are NOT listed
git commit -m "Initial commit: Webstar Business Services site and portal"
git branch -M main
git remote add origin https://github.com/imcdon/webstar.git
git push -u origin main
```

---

## cPanel one-time setup

### 1. DNS

At your registrar / Reclaim DNS:

- **A** `@` → hosting server IP
- **www** → same A record or CNAME per host docs

### 2. Two paths (important)

cPanel Git keeps a **repository clone** separate from the **public document root**. Deploy copies files via `.cpanel.yml`.

| Role | Example path | Notes |
|------|----------------|-------|
| Git clone (private) | `$HOME/repositories/webstar` | Where cPanel clones `imcdon/webstar` |
| Document root (public) | `$HOME/webstarbusinessservices.com` | Must match `DEPLOYPATH` in `.cpanel.yml` |

If this domain is the account’s **primary** site and you use `public_html`, change `DEPLOYPATH` in `.cpanel.yml` to `$HOME/public_html/` and push that change before deploying.

1. Create the public folder if needed (File Manager), e.g. `webstarbusinessservices.com`.
2. cPanel → **Domains** → set **webstarbusinessservices.com** document root to that folder (not a parent that would force `/webstar-business-solutions/` in the URL).

### 3. Clone the GitHub repo (Git Version Control)

1. cPanel → **Git Version Control** → **Create** / **Clone**.
2. **Clone URL** (pick one):
   - **SSH (recommended if the repo is private):** `git@github.com:imcdon/webstar.git`  
     Add a **read-only deploy key** from cPanel (or generate SSH key in cPanel → SSH Access) as a Deploy Key on the GitHub repo (Settings → Deploy keys).
   - **HTTPS:** `https://github.com/imcdon/webstar.git` (public repo — no token needed for clone; use a fine-grained PAT if you later make it private).
3. **Repository Path** = the **clone** path (e.g. `/home/YOURUSER/repositories/webstar`) — **not** the public document root.
4. Branch: **main**.
5. Clone / create the repository.
6. Confirm [`.cpanel.yml`](.cpanel.yml) is present at the clone root (it ships in this repo).

### 4. First deploy

1. Git Version Control → **Manage** this repo → **Pull or Deploy**.
2. **Update from Remote** (pull latest `main`).
3. **Deploy HEAD Commit** — runs the tasks in `.cpanel.yml` (rsync into `DEPLOYPATH`).

Current deploy task (do not use `--delete`; that would risk wiping server-only files):

```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=$HOME/webstarbusinessservices.com/
    - /usr/bin/rsync -a --exclude='.git' --exclude='.cpanel.yml' --exclude='library/mail-config.php' --exclude='library/portal-clients.php' ./ $DEPLOYPATH
```

### 5. SSL

cPanel → **SSL/TLS Status** (or Let’s Encrypt / AutoSSL) → enable for:

- `webstarbusinessservices.com`
- `www.webstarbusinessservices.com`

### 6. Server-only config files

On the server (File Manager or SSH), inside the **document root** (`DEPLOYPATH`):

1. Copy `library/mail-config.example.php` → `library/mail-config.php`  
   Fill Reclaim mailbox / SMTP (`smtp_host`, user, password, `mail_from`, `mail_to`, etc.).
2. Portal logins are deployed with the site via `library/portal-clients.php`  
   (admin `webstar` / client `superior` — see [portal/README.md](portal/README.md)). Change hashes after go-live if you want.
3. Do **not** commit `mail-config.php`. Deploy rsync excludes it so later deploys do not overwrite it.

`vendor/` is already in the repo — no `composer install` required on the server for normal deploys.

### 7. Superior Ice staging (separate repo)

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
3. cPanel → **Git Version Control** → **Manage** → **Pull or Deploy**:
   - **Update from Remote**
   - **Deploy HEAD Commit**
4. Smoke-test (below).  
   Server files `mail-config.php` and `portal-clients.php` in the document root are preserved (excluded from rsync).

If you enabled **automatic deployment** for the repo, a push that updates the cPanel clone may run `.cpanel.yml` without the manual Deploy click — still verify after the first auto-deploy.

---

## Post-deploy checklist

| Check | Expect |
|-------|--------|
| `https://webstarbusinessservices.com/` | Home loads; brand is Webstar Business Services |
| `https://webstarbusinessservices.com/portal/` | Login page |
| Admin login | All Projects list (after `portal-clients.php` is configured) |
| Contact / package intake | Email arrives (after `mail-config.php`) |
| `https://webstarbusinessservices.com/sitemap.php` | Sitemap XML |
| Push → Update → Deploy | A small text change appears on live |
| SIA staging subdomain | Separate repo loads; portal Open preview uses `staging_url` on production |

---

## Troubleshooting

- **Site only loads under `/webstar-business-solutions/`** — document root is wrong; point the domain at the folder that contains `index.php`.
- **Deploy does nothing / no Deploy button** — `.cpanel.yml` missing from the clone root; pull latest `main`.
- **Files deploy to the wrong place** — edit `DEPLOYPATH` in `.cpanel.yml` to match your real document root, push, Update, Deploy.
- **Contact form “config” error** — missing or placeholder `library/mail-config.php` on the **document root**.
- **Portal login fails** — missing `library/portal-clients.php` or wrong hashes on the document root.
- **cPanel cannot clone** — deploy key / PAT permissions; confirm branch name is `main`.
- **Secrets wiped after deploy** — do not add `--delete` to the rsync line; keep the mail-config / portal-clients excludes.
