# Fish with Jarett — Path A staging

- **Local:** `http://localhost/webstar-business-solutions/clients/fishwithjarett/`
- **Staging:** `https://webstarbusinessservices.com/clients/fishwithjarett/`
- **Auth:** HTTP Basic — use Client Portal credentials (password manager). Hashes only in `library/portal-clients.php` / `includes/staging-users.php`.
- **Gate:** On for Webstar/local hosts; off on `fishwithjarett.com`.

Public source repo (when created): GitHub `fishwithjarett`. Sync changes into this Path A folder for portal preview.

See [SECURITY-CHECKLIST.md](../SECURITY-CHECKLIST.md) before every push.
