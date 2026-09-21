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

1. Subdomain `superior-ice-adventures` → document root **`$HOME/superior-ice-adventures.webstarbusinessservices.com`** (not `public_html`).
2. SSL for the subdomain.
3. Deploy the Webstar repo (Update + Deploy). `.cpanel.yml` copies `clients/superior-ice-adventures/` into that document root.
   Or deploy the standalone [superior-ice-adventures](https://github.com/imcdon/superior-ice-adventures) repo into the same folder.

Live client domain `superioriceadventures.com` should use **`$HOME/superioriceadventures.com`**, never a shared `public_html` used by Webstar.

See [DEPLOY.md](../DEPLOY.md) and [portal/README.md](../portal/README.md).
