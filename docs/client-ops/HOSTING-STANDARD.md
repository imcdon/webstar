# Hosting standard

**Audience:** Webstar ops. Public summary lives in the "Where will my site be hosted?" FAQ on `/plans/` and `/packages/`.

Two hosting paths exist. Pick one in the agreement (hosting checkbox) at the scope call.

| Path | Who | Where the site lives | Billing |
|------|-----|----------------------|---------|
| **A — Client host** | One-time packages, no Care Plan | Client's own cPanel account at an approved host | Client pays host directly |
| **B — Webstar managed** | Website as a Service · Care Plans (hosting option) | One cPanel account per client under the Webstar WHM reseller | Included in the monthly fee |

In both paths the **domain is registered in the client's name** and **DNS runs on Cloudflare** (free plan) with the client as account owner and Webstar added as a member.

---

## 1. Webstar managed hosting (Path B)

**Platform:** cPanel/WHM reseller. Reclaim Hosting first (current provider for webstarbusinessservices.com). Alternatives if capacity or features require: A2 Hosting, InMotion, KnownHost. Cloudways (DigitalOcean) is the fallback for sites that outgrow shared cPanel.

**Per-client cPanel account setup**

1. WHM → Create account: username from the client slug; package = `webstar-client` (disk 5 GB, bandwidth unlimited, 1 addon domain, email off unless scoped).
2. Document root `$HOME/public_html` for the live domain. Never share a cPanel account between clients.
3. MultiPHP → pin PHP to the version the site was built on; note it in the client record.
4. AutoSSL on; verify after DNS goes live.
5. Backups: JetBackup (or cPanel Backup) daily, 14-day retention, plus a monthly off-server copy to the client Drive folder.
6. Git Version Control → clone the client repo (GitHub, deploy key); `.cpanel.yml` deploys to the document root. Same pattern as [DEPLOY.md](../../DEPLOY.md).
7. Server-only files (`library/mail-config.php` or equivalent) are created on the server and excluded from deploys.
8. Add to uptime monitor (99.5% target per agreements).

**Staging** stays under the Webstar site (`clients/{slug}/` or `{slug}.webstarbusinessservices.com`, HTTP Basic gated) per [clients/PATH-A.md](../../clients/PATH-A.md). Production never shares a document root with staging.

**Credentials:** cPanel, Cloudflare, GitHub deploy key, and Google accounts stored in the password manager under the client vault. No plaintext in the repo, email, or portal notes.

---

## 2. Client-owned hosting (Path A)

**Approved handoff hosts** (cPanel, AutoSSL, Git available): Hostinger (Business plan or higher), Namecheap (Stellar Plus+), A2 Hosting, InMotion, SiteGround (Site Tools — no cPanel, but equivalent). Avoid site-builder platforms (Wix, Squarespace, GoDaddy Website Builder) — the PHP site will not run there.

**Client sets up:** the hosting account, in their own name, with their own card. Webstar can sit on a screen-share for this. The client shares a cPanel login (or creates a Webstar user) via the password manager, not email.

**Webstar deploys** via Git Version Control if available, otherwise cPanel File Manager upload / SFTP. Record PHP version and host in the client record.

**Handoff:** when the project closes, remove Webstar's cPanel user or ask the client to rotate the password. Webstar keeps manager access on Google properties only if a Care Plan is active.

---

## 3. Domains and DNS

- Domain registered by the client in the client's name (Cloudflare Registrar or Namecheap preferred). If Webstar must register it, transfer to the client at launch.
- Cloudflare DNS, free plan. Client owns the Cloudflare account; Webstar is a member. Proxy (orange cloud) on for the site; DNS-only for mail records.
- Standard records: `A @` → host IP · `CNAME www` → `@` · mail per provider ([GOOGLE-WORKSPACE-DNS.md](GOOGLE-WORKSPACE-DNS.md) pattern for Workspace) · SPF/DKIM/DMARC always set even if mail is elsewhere.
- Cloudflare SSL mode: Full (strict) once AutoSSL is issued.

---

## 4. Migration runbook (cPanel → cPanel)

Used for Path B → Path A handoffs, Care Plan onboarding of an existing site, or host changes.

1. **Source:** cPanel → Backup → Download a Full Account Backup (or JetBackup full). For non-cPanel sources: zip the document root + export the DB.
2. **Target:** create the cPanel account (or have the client create their hosting). Match PHP version.
3. Restore: WHM → Restore a Full Backup (own reseller) or upload files + import DB; update config paths and DB credentials; recreate `mail-config.php`-type server files by hand.
4. Test on the target's temp URL or by hosts-file override. Check forms, SSL readiness, 404s, redirects.
5. Lower Cloudflare TTL to Auto; switch the `A` record; confirm AutoSSL; set Cloudflare SSL to Full (strict).
6. Monitor 24 h; then remove the site from the source account (keep the final backup in the client Drive folder for 90 days).

---

## 5. Handoff checklist

- [ ] Domain in client's name; auto-renew on; Webstar as technical contact only
- [ ] Cloudflare account owned by client; Webstar member access confirmed or removed
- [ ] Hosting account in client's name (Path A) or ownership-transfer email sent (WaaS month 6)
- [ ] Final full backup archived to client Drive folder (dated)
- [ ] GBP / GA4 / Search Console: client is primary owner; Webstar manager only (or removed)
- [ ] Credentials delivered through password manager share; nothing in email
- [ ] Client has the "how to update your site" handoff doc
- [ ] Portal project marked complete; warranty end date recorded
