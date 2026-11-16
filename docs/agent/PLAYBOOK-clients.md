# Playbook — Path A clients

Use for `clients/metro-detroit-holiday-lighting/`, `clients/superior-ice-adventures/`, `clients/djstackle/`, `clients/gypsie-locs-hair-studio/`, and `clients/shared/shop/`.

## Pre-edit

1. Read [clients/README.md](../../clients/README.md) and **only** the target `clients/{slug}/DEPLOY.md`.
2. Run [SECURITY-CHECKLIST.md](../../clients/SECURITY-CHECKLIST.md) mentality before push — no secrets, staging auth understood.
3. Do not change unrelated client folders.

## Path A pattern

- Staging under `/clients/{slug}/` on main domain (robots disallow).
- Live domains may be separate document roots on cPanel.
- Staging notes bar: `clients/shared/webstar-staging-banner.php`.

## DJ's Tackle + Square shop

1. [clients/djstackle/DEPLOY.md](../../clients/djstackle/DEPLOY.md)
2. [clients/shared/shop/README.md](../../clients/shared/shop/README.md)
3. `shop-config.example.php` → local `shop-config.php` (gitignored)
4. Seed: `php bin/seed-local.php` from client dir when docs say so
5. Reference: [docs/SQUARE-PHP-REFERENCE.md](../SQUARE-PHP-REFERENCE.md) — not full vendor tree

## Superior Ice / MDHL

- [PATH-A.md](../../clients/PATH-A.md) checklist
- Per-client DEPLOY for subdomain vs folder

## Post-edit

- [ ] `shop-config.php` and sqlite not staged
- [ ] Staging gate behavior unchanged unless requested
- [ ] Local URL tested under `clients/{slug}/`