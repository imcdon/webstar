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

Portal users live in committed [`library/portal-clients.php`](library/portal-clients.php) (hashed passwords). Change hashes after go-live if needed.

Superior Ice staging lives in [`clients/superior-ice-adventures/`](clients/superior-ice-adventures/) and deploys with this repo via Path A — see [`clients/PATH-A.md`](clients/PATH-A.md).


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

### 2. Domain → folder map (do not use shared `public_html`)

cPanel Git keeps a **repository clone** separate from the **public document root**. Deploy copies files via `.cpanel.yml`.

**Never deploy this Webstar repo into `$HOME/public_html`.** That folder is the account default and is easy to mix with other domains (this is how Superior Ice files can wrongly end up in `public_html`).

| Domain | Document root (File Manager path) |
|--------|-----------------------------------|
| `webstarbusinessservices.com` | `$HOME/webstarbusinessservices.com` |
| `superior-ice-adventures.webstarbusinessservices.com` (staging) | `$HOME/superior-ice-adventures.webstarbusinessservices.com` |
| `superioriceadventures.com` (live client site) | `$HOME/superioriceadventures.com` — **not** `public_html` |

| Role | Example path |
|------|----------------|
| Git clone (private) | `$HOME/repositories/webstar` |
| Webstar deploy target (`DEPLOYPATH`) | `$HOME/webstarbusinessservices.com` |

1. Create `$HOME/webstarbusinessservices.com` in File Manager if it does not exist.
2. cPanel → **Domains** → set **webstarbusinessservices.com** document root to that folder (folder that contains Webstar `index.php` after deploy).
3. If Superior Ice files were uploaded into `public_html` by mistake: move or delete them from `public_html`, point `superioriceadventures.com` at `$HOME/superioriceadventures.com` (live) or use the staging path above for the Webstar subdomain — then redeploy Webstar.

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

Current deploy task (do **not** point `DEPLOYPATH` at `public_html`; do not use `--delete`):

```yaml
---
deployment:
  tasks:
    - export DEPLOYPATH=$HOME/webstarbusinessservices.com
    - export SIAPATH=$HOME/superior-ice-adventures.webstarbusinessservices.com
    - /bin/mkdir -p $DEPLOYPATH $SIAPATH
    - /usr/bin/rsync -a --exclude='.git' --exclude='.cpanel.yml' --exclude='library/mail-config.php' ./ $DEPLOYPATH/
    - /usr/bin/rsync -a --exclude='.git' --exclude='node_modules' --exclude='_raw' --exclude='includes/db.config.php' clients/superior-ice-adventures/ $SIAPATH/
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

### 7. Superior Ice staging subdomain

cPanel document root for this preview is **`$HOME/superior-ice-adventures.webstarbusinessservices.com`** (not `public_html`, and not a subfolder of the Webstar site).

1. Subdomain hostname: `superior-ice-adventures.webstarbusinessservices.com`
2. Document root → `/home/YOURUSER/superior-ice-adventures.webstarbusinessservices.com`
3. SSL for that hostname.
4. Deploy Webstar (`Update from Remote` → `Deploy HEAD Commit`). `.cpanel.yml` copies `clients/superior-ice-adventures/` into that folder so `index.php` is at the subdomain root. Or deploy the standalone Superior Ice GitHub repo into the same folder.
5. Visit the subdomain — the browser should **prompt for a username and password**. Use Client Portal credentials:
   - Admin: `webstar` / `webstarAdmin2026`
   - Client: `superior` / `preview123`
6. Portal “Open preview” uses `staging_url` → `https://superior-ice-adventures.webstarbusinessservices.com`.

Auth is enforced by `includes/staging-gate.php` (HTTP Basic) on Webstar/local hosts only — not on live `superioriceadventures.com`.

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

- **Files landed in `public_html`** — wrong document root or old deploy target. This repo must deploy only to `$HOME/webstarbusinessservices.com`. Fix domain document roots (table above), clean `public_html`, Update + Deploy again.
- **Site only loads under `/webstar-business-solutions/`** — document root is wrong; point the domain at the folder that contains `index.php`.
- **Deploy does nothing / no Deploy button** — `.cpanel.yml` missing from the clone root; pull latest `main`.
- **Files deploy to the wrong place** — edit `DEPLOYPATH` in `.cpanel.yml` only if your host path differs; never set it to a shared `public_html` used by another site.
- **Contact form “config” error** — missing or placeholder `library/mail-config.php` on the **document root**.
- **Portal login fails** — missing `library/portal-clients.php` or wrong hashes on the document root.
- **cPanel cannot clone** — deploy key / PAT permissions; confirm branch name is `main`.
- **Secrets wiped after deploy** — do not add `--delete` to the rsync line; keep the mail-config exclude.
