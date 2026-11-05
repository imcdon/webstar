import { loadScoring } from "./config.mjs";
import { parseFlags } from "./db.mjs";

export function computeLeadScore(flags, scoring = loadScoring()) {
  let score = 0;
  for (const [key, weight] of Object.entries(scoring.weights)) {
    if (flags[key]) score += weight;
  }
  return Math.min(100, score);
}

export function countSignals(flags) {
  const ignore = new Set(["psi_performance", "psi_seo", "psi_accessibility", "enterprise_nav", "fetch_blocked"]);
  return Object.entries(flags).filter(([k, v]) => v === true && !ignore.has(k)).length;
}

export function isQualified(flags, score, scoring = loadScoring()) {
  const q = scoring.qualify;
  if (score < q.minScore) return false;
  if (flags.no_website && q.noWebsiteCountsAsQualified) return true;
  return countSignals(flags) >= q.minSignalCount;
}

export function buildPainSummary(flags, row, scoring = loadScoring()) {
  const parts = [];
  const labels = scoring.painLabels ?? {};
  for (const [key, label] of Object.entries(labels)) {
    if (flags[key]) parts.push(label);
  }
  if (row.psi_performance != null && flags.psi_perf_low) {
    parts.push(`Mobile perf ${Math.round(row.psi_performance)}`);
  }
  return parts.slice(0, 4).join(" · ");
}

/** Flags that describe website quality (vs geo/segment noise). */
const SITE_FLAG_KEYS = [
  "no_website",
  "weak_url",
  "builder_subdomain",
  "redirects_to_social",
  "parked_domain",
  "site_error",
  "no_https",
  "powered_by_builder",
  "wp_default_theme",
  "placeholder_copy",
  "generic_title",
  "thin_one_page",
  "no_meta_description",
  "stale_copyright",
  "stale_last_modified",
  "free_email_on_site",
  "no_domain_email",
  "psi_perf_low",
  "psi_seo_low",
  "mobile_a11y_low",
];

/**
 * Rank their current web presence for outreach.
 * @returns {{ website_status: string, website_grade: string, website_need: string, website_score: number }}
 */
export function rankWebsite(flags, scoring = loadScoring()) {
  if (flags.no_website) {
    return {
      website_status: "no_website",
      website_grade: "none",
      website_need: "high",
      website_score: 0,
    };
  }
  if (flags.weak_url || flags.redirects_to_social) {
    return {
      website_status: "social_only",
      website_grade: "F",
      website_need: "high",
      website_score: 10,
    };
  }
  if (flags.parked_domain || flags.site_error) {
    return {
      website_status: "broken_or_parked",
      website_grade: "F",
      website_need: "high",
      website_score: 5,
    };
  }
  // The host blocked our fetch (403/429…): the site exists but we could not grade it. Check by hand.
  if (flags.fetch_blocked) {
    return {
      website_status: "could_not_fetch",
      website_grade: "?",
      website_need: "unknown",
      website_score: 50,
    };
  }

  let penalty = 0;
  for (const key of SITE_FLAG_KEYS) {
    if (flags[key] && scoring.weights[key]) penalty += scoring.weights[key];
  }
  const website_score = Math.max(0, Math.min(100, 100 - penalty));

  let website_grade = "A";
  if (website_score < 35) website_grade = "F";
  else if (website_score < 50) website_grade = "D";
  else if (website_score < 65) website_grade = "C";
  else if (website_score < 80) website_grade = "B";

  const website_need =
    website_grade === "F" || website_grade === "D"
      ? "high"
      : website_grade === "C"
        ? "medium"
        : "low";

  return {
    website_status: website_grade === "A" || website_grade === "B" ? "has_site" : "weak_site",
    website_grade,
    website_need,
    website_score,
  };
}

export function flagsFromRow(row) {
  return parseFlags(row.audit_flags);
}
