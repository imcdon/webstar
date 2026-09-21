# Client Portal & staging subdomains

Webstar hosts in-progress client sites under `clients/` and links them from the Client Portal after login.

## Local preview

- Portal: `http://localhost/webstar-business-solutions/portal/`
- **Universal admin:** username `webstar` / password `webstarAdmin2026` (sees all client projects; change after deploy)
- Demo client: username `superior` / password `preview123` (change after handoff)
- Superior Ice staging: `http://localhost/webstar-business-solutions/clients/superior-ice-adventures/`  
  (browser prompts for the same portal username/password on staging hosts)

On localhost, My Projects “Open preview” uses each project’s `local_path`. On production, it uses `staging_url`. Staging sites require portal credentials (HTTP Basic Auth).

## Production: Superior Ice subdomain (cPanel / Reclaim)

1. **Subdomain**  
   cPanel → Subdomains → create `superior-ice-adventures` under `webstarbusinessservices.com`.  
   Document root: `$HOME/superior-ice-adventures.webstarbusinessservices.com`

2. **SSL**  
   Enable SSL for `superior-ice-adventures.webstarbusinessservices.com` (or a wildcard `*.webstarbusinessservices.com` if available).

3. **Deploy client code**  
   Prefer deploying the **Superior Ice Adventures** GitHub repo into that document root (cPanel Git Version Control → clone/pull), not long-term full history inside the Webstar repo. Locally, the Webstar repo may hold a copy/symlink for portal preview. Webstar `.cpanel.yml` also rsyncs `clients/superior-ice-adventures/` into the same path.

4. **Portal registry**  
   Ensure `library/portal-clients.php` has the client user and a project row with:
   - `local_path` → `clients/superior-ice-adventures/`
   - `staging_url` → `https://superior-ice-adventures.webstarbusinessservices.com`

## New client project checklist

1. Create `clients/{slug}/` (or point the subdomain document root at the client deploy).
2. cPanel → Subdomains → `{slug}.webstarbusinessservices.com` → that folder.
3. Add/update the client in `library/portal-clients.php` (copy from `portal-clients.example.php` if needed). Generate a hash:
   ```bash
   php -r "echo password_hash('their-password', PASSWORD_DEFAULT), PHP_EOL;"
   ```
4. Deploy the client repo via GitHub → cPanel Git pull into that folder.
5. Change the default portal password after handoff. Do not commit plaintext passwords.

## Auth notes

- Sessions: `HttpOnly`, `SameSite=Lax`, `Secure` when HTTPS.
- Staging sites are publicly reachable by URL in v1; the portal is how clients find them. Subdomain cookie gatekeeping can be added later.
