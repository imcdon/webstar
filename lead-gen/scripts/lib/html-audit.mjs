import * as cheerio from "cheerio";
import { loadScoring } from "./config.mjs";
import { hostnameFromUrl } from "./url-flags.mjs";

export function auditHtml(html, { finalUrl, lastModifiedHeader, scoring = loadScoring() }) {
  const flags = {};
  const $ = cheerio.load(html);
  const text = $("body").text().replace(/\s+/g, " ").trim();
  const lower = text.toLowerCase();
  const htmlLower = html.toLowerCase();

  const title = $("title").first().text().trim();
  if (!title || title.length < 15 || /^(home|welcome)$/i.test(title)) {
    flags.generic_title = true;
  }
  if (!$('meta[name="description"]').attr("content")?.trim()) {
    flags.no_meta_description = true;
  }
  if (!$('meta[name="viewport"]').length) {
    flags.mobile_a11y_low = true;
  }

  if (/lorem ipsum|your text here|coming soon|under construction|website coming soon/i.test(lower)) {
    flags.placeholder_copy = true;
  }
  if (/domain (is )?for sale|buy this domain|parked free|godaddy.*parked/i.test(lower + htmlLower)) {
    flags.parked_domain = true;
  }
  if (/powered by wix|create your website with wordpress\.com|squarespace|godaddy website builder/i.test(htmlLower)) {
    flags.powered_by_builder = true;
  }
  if (/wp-content\/themes\/twenty|just another wordpress site/i.test(htmlLower)) {
    flags.wp_default_theme = true;
  }

  const year = new Date().getFullYear();
  const copyMatch = text.match(/©?\s*(20\d{2})/g);
  if (copyMatch) {
    const years = copyMatch.map((m) => parseInt(m.replace(/\D/g, ""), 10)).filter(Boolean);
    const maxY = Math.max(...years);
    if (maxY < year - scoring.staleness.copyrightYearsBehind) {
      flags.stale_copyright = true;
    }
  }

  if (lastModifiedHeader) {
    const lm = new Date(lastModifiedHeader);
    if (!Number.isNaN(lm.getTime())) {
      const months =
        (Date.now() - lm.getTime()) / (1000 * 60 * 60 * 24 * 30);
      if (months > scoring.staleness.lastModifiedMonths) {
        flags.stale_last_modified = true;
      }
    }
  }

  const siteHost = hostnameFromUrl(finalUrl);
  const emails = new Set();
  $('a[href^="mailto:"]').each((_, el) => {
    const href = $(el).attr("href") || "";
    const m = href.replace(/^mailto:/i, "").split("?")[0].trim().toLowerCase();
    if (m.includes("@")) emails.add(m);
  });
  const emailRe = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/gi;
  for (const m of text.match(emailRe) || []) {
    emails.add(m.toLowerCase());
  }

  if (siteHost && !scoring.builderSubdomainSuffixes.some((s) => siteHost.endsWith(s))) {
    let hasFree = false;
    let hasDomain = false;
    for (const em of emails) {
      const domain = em.split("@")[1];
      if (scoring.freeEmailDomains.includes(domain)) hasFree = true;
      if (domain === siteHost || em.endsWith(`@${siteHost}`)) hasDomain = true;
    }
    if (hasFree) flags.free_email_on_site = true;
    if (emails.size > 0 && !hasDomain) flags.no_domain_email = true;
  }

  const internalLinks = new Set();
  $("a[href]").each((_, el) => {
    const href = $(el).attr("href");
    if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:")) return;
    try {
      const u = new URL(href, finalUrl);
      if (u.hostname.replace(/^www\./, "") === siteHost) internalLinks.add(u.pathname);
    } catch {
      /* ignore */
    }
  });
  const words = text.split(/\s+/).filter(Boolean).length;
  if (internalLinks.size < scoring.thinOnePage.minInternalLinks && words < scoring.thinOnePage.minWordCount) {
    flags.thin_one_page = true;
  }

  const hasTel = $('a[href^="tel:"]').length > 0;
  const hasContact =
    hasTel ||
    emails.size > 0 ||
    /contact/i.test($("a").text()) ||
    $("form").length > 0;
  if (!hasContact) flags.no_contact_path = true;
  if (!hasTel && /\(\d{3}\)|\d{3}-\d{3}-\d{4}/.test(text)) {
    flags.no_tel_link = true;
  }

  if (!htmlLower.includes("application/ld+json") && !htmlLower.includes('"@type":"localbusiness"')) {
    flags.no_local_schema = true;
  }

  // Enterprise / multi-location signals in navigation (used by classify.mjs for large_firm; not a lead signal)
  const navText = $("nav, header, footer, [role=navigation]").text().replace(/\s+/g, " ").toLowerCase();
  const navHrefs = $("nav a[href], header a[href], footer a[href]")
    .map((_, el) => ($(el).attr("href") || "").toLowerCase())
    .get()
    .join(" ");
  if (
    /\b(investors?|investor relations|franchis(e|ing)|our locations|all locations|find a (location|store|dealer)|store locator|shareholders?)\b/.test(
      navText,
    ) ||
    /\/(investors|franchise|franchising|locations|store-locator)\b/.test(navHrefs)
  ) {
    flags.enterprise_nav = true;
  }

  return flags;
}
