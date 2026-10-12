# Client staging sites

**Before every GitHub push:** run [SECURITY-CHECKLIST.md](SECURITY-CHECKLIST.md). Security is the priority.

Staging previews live under `clients/{slug}/` and are deployed with the Webstar site. New clients: copy [`.gitignore.template`](.gitignore.template) to `clients/{slug}/.gitignore`.

**Preview header:** all Path A sites use the shared bar in [`shared/webstar-staging-banner.php`](shared/webstar-staging-banner.php) (Notes dropdown). Each client’s `includes/partials/webstar-staging-banner.php` sets `$webstar_staging_notes` then requires that shared file.

## Fish with Jarett — Path A

- **Local:** `http://localhost/webstar-business-solutions/clients/fishwithjarett/`
- **Staging:** `https://webstarbusinessservices.com/clients/fishwithjarett/`
- **Auth:** Client Portal credentials (HTTP Basic). See [SECURITY-CHECKLIST.md](SECURITY-CHECKLIST.md).

## Superior Ice Adventures — Path A

**Follow [PATH-A.md](PATH-A.md)** for the full checklist.

- **Local:** `http://localhost/webstar-business-solutions/clients/superior-ice-adventures/`
- **Staging:** `https://superior-ice-adventures.webstarbusinessservices.com`
- **Auth:** Browser username/password prompt (HTTP Basic). Use **Client Portal** credentials (server-only / password manager)—do **not** commit live passwords to the repo. Gate is **off** on the future live domain `superioriceadventures.com`.

cPanel (summary):

1. Subdomain `superior-ice-adventures` → document root **`$HOME/superior-ice-adventures.webstarbusinessservices.com`** (not `public_html`).
2. SSL for the subdomain.
3. Deploy the Webstar repo (Update + Deploy). `.cpanel.yml` copies `clients/superior-ice-adventures/` into that document root.

Live client domain `superioriceadventures.com` should use **`$HOME/superioriceadventures.com`**, never a shared `public_html` used by Webstar.

See [DEPLOY.md](../DEPLOY.md) and [portal/README.md](../portal/README.md).
