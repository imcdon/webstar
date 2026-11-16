# Gypsie Locs Hair Studio — Path A deploy

## URLs

- **Local:** `http://localhost/webstar-business-solutions/clients/gypsie-locs-hair-studio/`
- **Staging:** `https://webstarbusinessservices.com/clients/gypsie-locs-hair-studio/`
- **Live (planned):** client-owned domain (not the GlossGenius subdomain)

## Auth

Staging on `*.webstarbusinessservices.com` uses HTTP Basic via Client Portal credentials (username `gypsie`, or admin). Gate is off on localhost and on the future live domain listed in `includes/staging-gate.php`.

Portal project is registered in `library/portal-clients.php`. Do **not** commit plaintext passwords — store them in a password manager only.

## Notes for Ian / client

- Primary CTA points to existing GlossGenius booking: `https://gypsielocshairstudio.glossgenius.com/`
- Confirm phone number before go-live (config currently uses the Ultra Salon Suites listing number).
- Replace placeholder gallery frames with real chair / before-after photos.
- `robots.txt` currently disallows all — open indexing only on the live domain.
