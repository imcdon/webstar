import { loadScoring } from "./config.mjs";

export function hostnameFromUrl(urlStr) {
  try {
    return new URL(urlStr).hostname.replace(/^www\./, "").toLowerCase();
  } catch {
    return "";
  }
}

export function classifyWebsiteUrl(urlStr, scoring = loadScoring()) {
  const flags = {};
  if (!urlStr || !String(urlStr).trim()) {
    flags.no_website = true;
    return flags;
  }
  let url;
  try {
    url = new URL(urlStr);
  } catch {
    flags.site_error = true;
    return flags;
  }
  const host = url.hostname.replace(/^www\./, "").toLowerCase();
  for (const weak of scoring.weakUrlHosts) {
    if (host === weak || host.endsWith(`.${weak}`)) {
      flags.weak_url = true;
      break;
    }
  }
  for (const suffix of scoring.builderSubdomainSuffixes) {
    if (host === suffix || host.endsWith(`.${suffix}`)) {
      flags.builder_subdomain = true;
      break;
    }
  }
  const social = ["facebook.com", "instagram.com", "tiktok.com", "linktr.ee"];
  if (social.some((s) => host.includes(s))) {
    flags.weak_url = true;
  }
  return flags;
}

export function isSocialHost(host) {
  return /(^|\.)facebook\.com$|instagram\.com|tiktok\.com|linktr\.ee/.test(host);
}
