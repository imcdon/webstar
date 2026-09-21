# Path A — Deploy Superior Ice via Webstar

Staging URL: **https://superior-ice-adventures.webstarbusinessservices.com**

Source of truth: `clients/superior-ice-adventures/` in the **webstar** repo.  
On Deploy, [`.cpanel.yml`](../.cpanel.yml) rsyncs that folder to:

`$HOME/superior-ice-adventures.webstarbusinessservices.com`

---

## One-time cPanel setup

1. **Subdomains** → create `superior-ice-adventures` under `webstarbusinessservices.com`.
2. **Document root** → `/home/YOURUSER/superior-ice-adventures.webstarbusinessservices.com`  
   - Not `public_html`  
   - Not inside `webstarbusinessservices.com/clients/...`
3. **SSL** for `superior-ice-adventures.webstarbusinessservices.com`.
4. Confirm the Webstar Git repo is already set up (clone under e.g. `$HOME/repositories/webstar`, deploy to `$HOME/webstarbusinessservices.com`).

---

## Deploy (each update)

1. Change files under `clients/superior-ice-adventures/` (test on local XAMPP if you want).
2. From the Webstar project root:
   ```bash
   git add clients/superior-ice-adventures
   git commit -m "Update Superior Ice staging"
   git push origin main
   ```
3. cPanel → **Git Version Control** → **webstar** → **Update from Remote** → **Deploy HEAD Commit**.
4. Open https://superior-ice-adventures.webstarbusinessservices.com  
   Browser should ask for credentials:
   - Admin: `webstar` / `webstarAdmin2026`
   - Client: `superior` / `preview123`

Portal → My Projects → **Open preview** uses the same URL.

---

## Verify

| Check | Expect |
|-------|--------|
| Subdomain loads | Credential prompt, then SIA home + staging banner |
| Wrong password | Stays on 401 / prompt |
| Live `superioriceadventures.com` | Unaffected (separate site; gate off there) |
| File Manager | `index.php` at the **root** of `superior-ice-adventures.webstarbusinessservices.com` |

---

## Do not

- Deploy the standalone Superior Ice GitHub repo into this same folder while using Path A (they will overwrite each other).
- Point this subdomain at `public_html`.
- Point `DEPLOYPATH` / `SIAPATH` in `.cpanel.yml` at `public_html`.
