# Client staging sites

Staging previews live under `clients/{slug}/` and are deployed with the Webstar site (or cloned separately into that path).

## Superior Ice Adventures

- **Local:** `http://localhost/webstar-business-solutions/clients/superior-ice-adventures/`
- **Staging:** `https://superior-ice-adventures.webstarbusinessservices.com`
- **Auth:** Browser username/password prompt (HTTP Basic). Same credentials as the Client Portal:
  - Admin: `webstar` / `webstarAdmin2026`
  - Client: `superior` / `preview123`
- Gate is **off** on the future live domain `superioriceadventures.com`.

cPanel:

1. Subdomain `superior-ice-adventures` → document root **`$HOME/webstarbusinessservices.com/clients/superior-ice-adventures`** (not `public_html`).
2. SSL for the subdomain.
3. Deploy the Webstar repo (Update + Deploy) so that folder is filled from `clients/superior-ice-adventures/` in git.

Live client domain `superioriceadventures.com` should use **`$HOME/superioriceadventures.com`**, never a shared `public_html` used by Webstar.

See [DEPLOY.md](../DEPLOY.md) and [portal/README.md](../portal/README.md).
