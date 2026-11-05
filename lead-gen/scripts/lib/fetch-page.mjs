import { isSocialHost } from "./url-flags.mjs";

// Browser-like headers: many DIY-builder hosts (Wix, Squarespace, GoDaddy) return 403 to non-browser agents.
const HEADERS = {
  "User-Agent":
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
  Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
  "Accept-Language": "en-US,en;q=0.9",
  "Upgrade-Insecure-Requests": "1",
};

/** Statuses that mean "we were blocked / rate limited", not "the site is broken". */
const BLOCKED_STATUSES = new Set([401, 403, 405, 406, 429, 503, 999]);

export async function fetchPage(urlStr, { timeoutMs = 20000 } = {}) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), timeoutMs);
  try {
    const res = await fetch(urlStr, {
      redirect: "follow",
      signal: controller.signal,
      headers: HEADERS,
    });
    const finalUrl = res.url;
    const host = new URL(finalUrl).hostname.replace(/^www\./, "").toLowerCase();
    const flags = {};
    if (!finalUrl.startsWith("https://")) flags.no_https = true;
    if (isSocialHost(host)) flags.redirects_to_social = true;
    if (!res.ok) {
      if (BLOCKED_STATUSES.has(res.status)) flags.fetch_blocked = true;
      else flags.site_error = true;
      return { ok: false, status: res.status, finalUrl, html: "", lastModified: null, flags };
    }
    const html = await res.text();
    const lastModified = res.headers.get("last-modified");
    return { ok: true, status: res.status, finalUrl, html, lastModified, flags };
  } catch {
    return {
      ok: false,
      status: 0,
      finalUrl: urlStr,
      html: "",
      lastModified: null,
      flags: { site_error: true },
    };
  } finally {
    clearTimeout(timer);
  }
}
