# Client project — security checklist (before GitHub)

**Security is the priority.** Run this checklist before every `git add` / `commit` / `push` that includes `clients/**` or a standalone client repo. Do not push until every applicable item is checked.

Related: [README.md](README.md) · root [`.gitignore`](../.gitignore) · [robots.txt](../robots.txt)

---

## 1. Secrets — never commit

- [ ] No `db.config.php`, `mail-config.php`, `.env`, `.env.*`, `*.local.php`
- [ ] No API keys, SMTP passwords, Stripe secrets, JWT/session secrets, private keys (`BEGIN PRIVATE KEY`)
- [ ] Only `*.example.php` / `*.example.env` with **placeholder** values (never real credentials)
- [ ] Confirm with:
  ```bash
  git status
  git diff --cached
  ```
  Scan the staged diff for: `password`, `secret`, `api_key`, `smtp_pass`, `BEGIN PRIVATE KEY`
- [ ] Client or site `.cpanel.yml` **excludes** secret files from rsync (same idea as Webstar excluding `library/mail-config.php`)

**Refuse to commit** any file that matches secret patterns. Prefer example + gitignore (see [`.gitignore.template`](.gitignore.template)).

---

## 2. Auth and staging

- [ ] Staging gate present and **on** for Webstar/local hosts; **off** only on the client’s live production domain
- [ ] No **plaintext** production or staging passwords in committed markdown or PHP comments (password **hashes** only in PHP registries)
- [ ] Docs say “use Client Portal / server-only credentials” — do **not** publish live passwords in the repo
- [ ] Default seed passwords (e.g. local admin bootstrap) documented as **change after first login** and rotated on staging/live
- [ ] Admin / portal areas require authentication
- [ ] Form POST handlers use CSRF/session checks where the app already has that pattern (do not add open write endpoints)

---

## 3. Exposure and crawl

- [ ] Staging / preview URLs protected with HTTP auth and/or `noindex` as appropriate
- [ ] Parent Webstar [`robots.txt`](../robots.txt) still has `Disallow: /clients/`
- [ ] No private client assets (contracts, PII CSVs, ID scans, signed PDFs) under web-reachable paths in the repo

---

## 4. Hardening (code in this push)

- [ ] SQL uses prepared statements (no user input concatenated into queries)
- [ ] User-controlled output is escaped (or equivalent safe rendering)
- [ ] Uploads (if any): type/size limits; stored outside executable dirs or with safe extensions
- [ ] Staging/live error display does not leak DB credentials or full stack traces

---

## 5. Repo hygiene before `git push`

- [ ] `git status` shows no unintended untracked secrets
- [ ] No `debug-*.log`, agent debug scripts with server paths, or local DB dumps
- [ ] Correct branch and remote; no force-push over production history unless explicitly required
- [ ] After deploy: server-only secrets still exist **only on the server** (deploy did not overwrite `db.config.php` / mail config)

---

## Quick refuse list (never stage these)

| Pattern | Action |
|---------|--------|
| `**/db.config.php` | gitignore; use `db.config.example.php` |
| `**/mail-config.php` | gitignore; use example |
| `.env`, `.env.*`, `*.local.php` | gitignore |
| Plaintext passwords in README/DEPLOY | Remove; point to portal / password manager |
| Contracts, PII exports under `clients/` | Keep out of git / out of public_html |

---

## After a failed check

1. Unstage the bad files: `git restore --staged <path>`  
2. Add to `.gitignore` if needed  
3. Rotate any credential that was ever committed or pasted into docs  
4. Re-run this checklist, then commit and push
