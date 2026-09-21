# Client staging sites

Staging previews live under `clients/{slug}/` and are deployed with the Webstar site (or cloned separately into that path).

## Superior Ice Adventures

- **Local:** `http://localhost/webstar-business-solutions/clients/superior-ice-adventures/`
- **Staging:** `https://superior-ice-adventures.webstarbusinessservices.com`
- **Auth:** Browser username/password prompt (HTTP Basic). Same credentials as the Client Portal:
  - Admin: `webstar` / `webstarAdmin2026`
  - Client: `superior` / `preview123`
- Gate is **off** on the future live domain `superioriceadventures.com`.

cPanel: create subdomain `superior-ice-adventures` → document root `…/webstarbusinessservices.com/clients/superior-ice-adventures`, then SSL. After Webstar deploy, the folder is populated from this repo.

See [DEPLOY.md](../DEPLOY.md) and [portal/README.md](../portal/README.md).
