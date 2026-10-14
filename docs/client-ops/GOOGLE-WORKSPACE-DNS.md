# Google Workspace + DNS (webstarbusinessservices.com)

Primary company mailbox: **ian@webstarbusinessservices.com**

DNS is edited in **cPanel → Zone Editor** (nameservers: Reclaim). Do not change website **A** / **www** records while fixing mail.

## Target DNS records

| Type | Name | Priority | Value |
|------|------|----------|--------|
| MX | `@` | 1 | `smtp.google.com` |
| TXT | `@` | — | `v=spf1 include:_spf.google.com ip4:162.243.34.57 ~all` |
| TXT | `google._domainkey` | — | *(paste full value from Google Admin → Authenticate email)* |
| TXT | `_dmarc` | — | `v=DMARC1; p=none; rua=mailto:ian@webstarbusinessservices.com; pct=100` |

Rules:

- Exactly **one** TXT that starts with `v=spf1`.
- Keep any separate `google-site-verification=...` TXT on `@` (do not merge into SPF).
- Remove old Google MX hosts (`ASPMX.L.GOOGLE.COM`, etc.) and any non-Google mail MX.
- `ip4:162.243.34.57` authorizes the Reclaim web server for contact-form SMTP. Drop it only after forms send only via Workspace.

## Setup order

### 1. Google Admin

1. [admin.google.com](https://admin.google.com) → **Account → Domains** — domain verified.
2. **Apps → Google Workspace → Gmail** — Gmail active for the domain.
3. User: **ian@webstarbusinessservices.com** (primary company email sitewide). Optional aliases (`info@`, `weddings@`) may forward to `ian@` if you create them later.

### 2. Zone Editor (Reclaim / cPanel)

1. Confirm MX `@` → `smtp.google.com` priority `1`.
2. Replace the existing SPF TXT with the SPF row above.
3. Admin → **Gmail → Authenticate email** → generate 2048-bit DKIM → add TXT `google._domainkey` → wait → **Start authentication**.
4. Update `_dmarc` TXT to the DMARC row above (`p=none` first).

### 3. Verify

```text
dig MX webstarbusinessservices.com +short
dig TXT webstarbusinessservices.com +short
dig TXT google._domainkey.webstarbusinessservices.com +short
dig TXT _dmarc.webstarbusinessservices.com +short
```

Expect MX `1 smtp.google.com.`, SPF with `include:_spf.google.com`, DKIM `v=DKIM1...`, DMARC `v=DMARC1; p=none; ...`.

Also: [MX Toolbox](https://mxtoolbox.com/) + Admin **Check Gmail setup**.

### 4. Mail tests

1. Sign in to Gmail as `ian@webstarbusinessservices.com`.
2. Send to an external inbox; confirm From domain and headers: SPF/DKIM/DMARC pass.
3. Install signature: [EMAIL-SIGNATURE.md](EMAIL-SIGNATURE.md).

### 5. Website SMTP (`mail-config.php` on server only)

Prefer Workspace SMTP after DNS is clean:

- `smtp_host` = `smtp.gmail.com`
- `smtp_port` = `587`, `smtp_secure` = `tls`
- `smtp_user` / `mail_from` = `ian@webstarbusinessservices.com`
- Use a Google App Password (or SMTP relay per Google docs)

Never commit `library/mail-config.php`. See [DEPLOY.md](../../DEPLOY.md).

## After things are stable

Raise DMARC from `p=none` → `p=quarantine` → `p=reject` once reports look clean (1–2 weeks of monitoring recommended).
