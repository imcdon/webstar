# Client staging sites

Staging previews for in-progress client sites live under this folder on the **server** (and optionally locally for XAMPP).

They are **not** part of the Webstar GitHub repo long-term. Each client site is deployed from **its own** GitHub repository via cPanel Git Version Control into `clients/{slug}/`.

## Production pattern

1. Create subdomain `{slug}.webstarbusinessservices.com` in cPanel.
2. Point the subdomain document root at `…/clients/{slug}/` (under the Webstar site tree, or a sibling path you prefer).
3. Clone/pull the client repo into that folder.
4. Register the project in the server-only file `library/portal-clients.php` (see `library/portal-clients.example.php` and [DEPLOY.md](../DEPLOY.md)).

## Local XAMPP

You may keep a local copy under `clients/{slug}/` for portal preview links. That folder is gitignored for Superior Ice Adventures; other clients should follow the same pattern if added to `.gitignore`.

See [portal/README.md](../portal/README.md) for portal credentials and checklist details.
