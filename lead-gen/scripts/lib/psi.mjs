import { getPsiKey } from "./env.mjs";
import { loadScoring } from "./config.mjs";

export async function runPsi(urlStr, strategy = "mobile") {
  const key = getPsiKey();
  const categories = ["performance", "seo", "accessibility"];
  const params = new URLSearchParams({ url: urlStr, strategy, key });
  for (const c of categories) params.append("category", c);

  const res = await fetch(`https://www.googleapis.com/pagespeedonline/v5/runPagespeed?${params}`);
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(`PSI failed: ${data.error?.message || res.statusText}`);
  }
  const cats = data.lighthouseResult?.categories ?? {};
  const scoring = loadScoring();
  const scores = {
    performance: Math.round((cats.performance?.score ?? 0) * 100),
    seo: Math.round((cats.seo?.score ?? 0) * 100),
    accessibility: Math.round((cats.accessibility?.score ?? 0) * 100),
  };
  const flags = {};
  if (scores.performance < scoring.psi.performanceMin) flags.psi_perf_low = true;
  if (scores.seo < scoring.psi.seoMin) flags.psi_seo_low = true;
  if (scores.accessibility < scoring.psi.accessibilityMin) flags.mobile_a11y_low = true;
  return { scores, flags };
}
