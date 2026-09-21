# Path A — Deploy Superior Ice via Webstar

**Working preview URL (use this):**  
https://webstarbusinessservices.com/clients/superior-ice-adventures/

Optional pretty hostname (only after cPanel document root is remapped):  
https://superior-ice-adventures.webstarbusinessservices.com  
→ document root must be `$HOME/webstarbusinessservices.com/clients/superior-ice-adventures`  
(not a separate empty folder — that causes Apache **403 Forbidden**)

Source of truth: `clients/superior-ice-adventures/` in the **webstar** repo.

---

## One-time cPanel setup

1. Deploy Webstar (below) so `clients/superior-ice-adventures/index.php` exists under the main site.
2. Confirm this URL prompts for a password (401), not Forbidden (403):  
   https://webstarbusinessservices.com/clients/superior-ice-adventures/
3. **Optional subdomain:** Subdomains → `superior-ice-adventures`  
   Document root → `/home/YOURUSER/webstarbusinessservices.com/clients/superior-ice-adventures`  
   SSL for the hostname. Then you can switch `staging_url` back to the subdomain.

---

## Deploy (each update)

1. Edit `clients/superior-ice-adventures/` as needed.
2. Push to **https://github.com/imcdon/webstar** (`main`).
3. cPanel → Webstar Git → **Update from Remote** → **Deploy HEAD Commit**.
4. Portal → **Open preview** (uses the `/clients/...` URL).

Credentials when prompted:

- `webstar` / `webstarAdmin2026`
- `superior` / `preview123`

---

## Why you saw Forbidden

The subdomain pointed at a **different** folder that Apache could not serve (403, no PHP). The copy under the main Webstar site works and returns the login prompt (401).
