/**
 * Chain / large-firm / policy exclusion heuristics. Pure functions — classify.mjs applies them.
 */
import { loadExclusions, loadScoring } from "./config.mjs";

const MULTI_LABEL_TLDS = new Set(["co.uk", "com.au", "co.nz", "com.mx", "co.za"]);

/** Registrable domain (eTLD+1-ish): www.shop.example.com → example.com */
export function registrableDomain(urlOrHost) {
  if (!urlOrHost) return "";
  let host = String(urlOrHost).toLowerCase();
  try {
    host = new URL(host.includes("://") ? host : `http://${host}`).hostname;
  } catch {
    return "";
  }
  host = host.replace(/^www\./, "");
  const parts = host.split(".");
  if (parts.length <= 2) return host;
  const lastTwo = parts.slice(-2).join(".");
  if (MULTI_LABEL_TLDS.has(lastTwo)) return parts.slice(-3).join(".");
  return lastTwo;
}

function compile(patterns) {
  return (patterns ?? []).map((p) => new RegExp(p, "i"));
}

/** Social / DIY-builder / booking hosts shared by many small businesses — never a chain signal. */
export function sharedPlatformSet(exclusions = loadExclusions(), scoring = loadScoring()) {
  const set = new Set();
  for (const d of [
    ...(exclusions.sharedPlatformDomains ?? []),
    ...(scoring.weakUrlHosts ?? []),
    ...(scoring.builderSubdomainSuffixes ?? []),
  ]) {
    const r = registrableDomain(d);
    if (r) set.add(r);
  }
  return set;
}

export function makeChainFilter(exclusions = loadExclusions(), scoring = loadScoring()) {
  const shared = sharedPlatformSet(exclusions, scoring);
  const chainDomains = new Set(
    (exclusions.chainDomains ?? []).map((d) => registrableDomain(d) || d.toLowerCase()).filter((d) => !shared.has(d)),
  );
  const namePatterns = compile(exclusions.chainNamePatterns);
  const locatorPatterns = compile(exclusions.locatorUrlPatterns);
  const nonBusiness = new Set(exclusions.nonBusinessTypes ?? []);
  const policyPrimary = new Set(exclusions.policyExcludedPrimaryTypes ?? exclusions.policyExcludedTypes ?? []);
  const policyAny = new Set(exclusions.policyExcludedAnyTypes ?? []);
  const multiMin = exclusions.multiLocationDomainMin ?? 3;
  const large = exclusions.largeFirm ?? { ratingCountMin: 300, ratingCountAlone: 5000 };

  /** Domain to count for multi-location detection, or "" when it is a shared platform. */
  function countableDomain(url) {
    const d = registrableDomain(url);
    return d && !shared.has(d) ? d : "";
  }

  /**
   * @param row { display_name, website_url, primary_type, types (array), user_rating_count, flags (object) }
   * @param ctx { domainCounts: Map<registrableDomain, number> }
   * @returns {string|null} exclude reason or null when retained
   */
  function reasonFor(row, ctx) {
    const types = (row.types ?? []).filter(Boolean);

    // Non-business POIs (parks, schools, government, transit, cemeteries, clubs...)
    if (row.primary_type && nonBusiness.has(row.primary_type)) return "non_business";

    // Consumer-facing small business policy (skip medical, law, finance, B2B/industrial, dealers, lodging)
    if (row.primary_type && policyPrimary.has(row.primary_type)) return "excluded_type";
    if (types.some((t) => policyAny.has(t))) return "excluded_type";

    const domain = registrableDomain(row.website_url);
    if (domain && chainDomains.has(domain)) return "chain_domain";

    const name = row.display_name || "";
    if (name && namePatterns.some((re) => re.test(name))) return "chain_name";

    if (row.website_url && !shared.has(domain)) {
      let pathAndHost = "";
      try {
        const u = new URL(row.website_url);
        pathAndHost = `${u.hostname}${u.pathname}`.toLowerCase();
      } catch {
        pathAndHost = row.website_url.toLowerCase();
      }
      if (locatorPatterns.some((re) => re.test(pathAndHost))) return "locator_url";
    }

    const countable = countableDomain(row.website_url);
    if (countable && (ctx?.domainCounts?.get(countable) ?? 0) >= multiMin) return "multi_location_domain";

    const ratings = row.user_rating_count ?? 0;
    const enterpriseNav = Boolean(row.flags?.enterprise_nav);
    if (ratings >= large.ratingCountAlone) return "large_firm";
    if (enterpriseNav && ratings >= large.ratingCountMin) return "large_firm";

    return null;
  }

  return { reasonFor, registrableDomain, countableDomain, shared };
}
