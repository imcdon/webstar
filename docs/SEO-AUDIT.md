# Webstar Business Services SEO Audit

Saved to act on later. Source: [SEO-AUDIT.pdf](SEO-AUDIT.pdf). Oct 19, 2026 · Pratyksh Sharma. Crawl of all 58 sitemap URLs, PageSpeed Insights (Lighthouse 13.5, emulated Moto G Power, slow 4G), and a Chrome homepage load.

Overall score: **72/100**. Area scores are the auditor’s judgement; the overall score is their average. PageSpeed figures are Google’s.

webstarbusinessservices.com is technically clean and well structured for local search. Speed and proof are holding it back: one 3 MB background photo makes the mobile homepage take 18.8 seconds to show its main content, and the site has only one live portfolio example.

## Area scores

- **Technical SEO — 92.** Strong: HTTPS, redirects, canonicals, sitemap, and schema are correct.
- **On-page SEO — 70.** Good structure; 40 of 58 titles and 29 descriptions are too long and get cut off in Google.
- **Content and keywords — 75.** 30+ unique city and industry pages; thin portfolio, examples, and wedding pages.
- **Page speed — 55.** Mobile performance 69, LCP 18.8 s; desktop 79.
- **Local SEO — 70.** Google Business Profile linked; no site-wide LocalBusiness schema or Google review links; citations unverified.

## Biggest problems

1. A 3 MB homepage background image (`IMG_4687.JPG`) is 86% of the page’s weight and makes LCP 18.8 s on mobile.
2. Titles and descriptions get truncated. 40 titles exceed 60 characters, mostly because “— Webstar Business Services” adds 27 characters to each.
3. Thin proof. `/examples/` shows one client site, and the whole site has only 9 meaningful images, so city and industry pages have nothing visual to back their claims.
4. Wedding pages are invisible to search. `/weddings/` and its two subpages render their content with JavaScript, have no schema, and serve 48–154 words to crawlers.

## Quick wins

- Compress `IMG_4687.JPG` to WebP under 200 KB and preload it: expect mobile LCP under 4 s.
- Shorten the title suffix to `| Webstar` site-wide.
- Convert the Galano `.otf` fonts to `.woff2`.
- Add LocalBusiness (`ProfessionalService`) details to the existing Organization schema.

## Site overview

The site is a hand-built static site with 58 URLs in its sitemap, all returning 200 and all indexable.

- **Pages in sitemap (58):** 13 core and hub pages (home, packages + 3 tiers, plans, process, examples, about, contact, service areas, industries, resources), 10 city pages, 4 region pages, 5 service pages, 13 industry pages, 7 guides, 3 legal pages, 3 wedding pages.
- **Status codes:** All 58 return 200; unknown URLs return a real 404.
- **robots.txt:** Allows everything except `/clients/` and `/portal/`; points to the sitemap.
- **Sitemap:** `/sitemap.xml` with lastmod dates (updated 15–16 Oct 2026).
- **Noindex pages:** None.
- **Tracking:** Google Analytics 4 and Google Tag Manager both load.
- **Search Console:** Not checked; needs access. Confirm how many of the 58 pages Google has actually indexed.

## Technical SEO

The technical foundation is strong. The only real issue is the JavaScript-rendered wedding section.

Fix: render the wedding pages as static HTML like the rest of the site. Or, if they are a separate brand, move them to their own domain so they don’t dilute the Metro Detroit business topic.

- **HTTPS and HSTS — pass.** `http://` and non-www redirect to `https://www.`; HSTS header set.
- **Trailing slash duplicates — pass.** `/packages` and `/packages/` both load, but the canonical points to `/packages/`.
- **Canonical tags — pass.** All 58 pages self-canonical.
- **404 handling — pass.** Unknown URLs return a true 404.
- **Mobile — pass.** Viewport set; Lighthouse SEO 100, Best Practices 100.
- **Structured data — pass.** 55 of 58 pages carry valid JSON-LD: Organization, WebSite, BreadcrumbList, Service, FAQPage, Article, HowTo.
- **Wedding section — fail.** `/weddings/`, `/weddings/services/`, and `/weddings/examples/` are a JavaScript app: 4 KB of HTML, 48–154 words for crawlers, no schema.
- **Language — pass.** `lang="en"` on every page.
- **Accessibility — minor.** Lighthouse accessibility 96 mobile / 95 desktop; some text fails colour contrast.

## On-page SEO

Every page has a unique title, a unique description, and one H1, but most titles and descriptions are too long, so Google cuts them off.

- **Title tags:** 18 of 58 are fine. 40 are over 60 characters; 50 end with “— Webstar Business Services” (27 characters).
- **Meta descriptions:** 28 of 58 are fine. 29 are over 160 characters (homepage 197, `/packages/` 244); 1 is under 70.
- **H1:** 57 of 58. `/weddings/examples/` has no H1 in its HTML.
- **Duplicate titles or descriptions:** none.
- **Image alt text:** 69 of 69. 9 content images have alt text; 60 star icons are correctly marked decorative (`alt=""`).
- **Open Graph tags:** title and image set on every page.

Switching the suffix to `| Webstar` brings 33 of the 40 long titles under 60 characters. Shorten the other 7 by hand:

- How to Get Your First 10 Google Reviews (Without Being Pushy) | Webstar (71) → How to Get Your First 10 Google Reviews | Webstar
- Webstar Weddings — Custom Wedding Websites & Digital Invitations (64) → Custom Wedding Websites & Digital Invitations | Webstar
- Guides & Resources — Michigan Small Business Websites | Webstar (63) → Small Business Website Guides | Webstar
- Wix & Squarespace vs a Custom Small Business Website | Webstar (62) → Wix & Squarespace vs a Custom Website | Webstar
- Web Design Service Areas — Metro Detroit & Michigan | Webstar (61) → Web Design Service Areas in Metro Detroit | Webstar
- Google Business Profile Checklist for Metro Detroit | Webstar (61) → Google Business Profile Checklist (Detroit) | Webstar
- Website Launch Checklist: Domain, Hosting & Go-Live | Webstar (61) → Website Launch Checklist: Domain & Hosting | Webstar

Industry pages miss the location. “Web Design Services for Restaurants” competes nationally. “Restaurant Website Design in Metro Detroit” targets buyers Webstar can actually serve. Apply the same pattern to all 13 industry titles and H1s.

Descriptions: trim to 140–155 characters, lead with the service and place, and end with one concrete detail (price from $350, Google Business Profile included).

## Content and keywords

The page structure targets the right local searches. The gaps are proof and depth, not more pages.

What works:

- City and industry pages are genuinely unique. Sample pairs share only 2–16% of their text (Troy vs Royal Oak 4%, Plumbers vs Law firms 16%), so Google is unlikely to treat them as doorway pages.
- Core pages have solid depth: about 400–830 words each, with FAQ schema on 38 pages.
- The 7 guides answer real buyer questions, such as website cost in Michigan, Wix vs custom, and Google Business Profile setup.

Gaps:

- `/examples/` — 154 words. One client site (Chrome City Guide Service); no screenshots, results, or story.
- `/contact/` — 238 words. Fine for a contact page; add the service area and a map of it.
- `/accessibility/` — 359 words. Legal page; fine.
- `/resources/` — 386 words. A list of guides; add a short intro on who they’re for.
- `/packages/business-growth-website/` — 398 words. The highest-value package has the least content of the three tiers.
- `/weddings/` (3 pages) — 48–154 words. JavaScript-only.

Keywords to own (volumes still need confirming in Google Keyword Planner; Semrush’s free quota ran out during this audit):

- web design Metro Detroit / Detroit web design → `/web-design-services-metro-detroit/`, `/web-design-services-detroit/`
- web design Troy MI, Royal Oak, Novi, Ann Arbor (each city) → the matching city page
- small business website cost Michigan → `/website-cost-michigan/` and the 2026 cost guide
- affordable website design Michigan → `/affordable-website-design-michigan/`
- website as a service / website subscription → `/plans/`
- Google Business Profile setup Detroit → `/google-business-profile-setup/`
- restaurant / contractor / salon website design Detroit → the matching industry page, once the title includes Detroit

Content to add:

1. 3–5 case studies, one per main industry, each with the business, a before-and-after screenshot, and a result (calls, Google Maps views, rankings). This is the biggest content gap.
2. One real project photo or screenshot on each city and industry page. The site has only 9 content images across 58 pages.
3. Build out the Business Growth package page to the depth of the other two tiers.
4. Link each guide to the service page it supports, for example the cost guide to `/packages/` and the GBP checklist to `/google-business-profile-setup/`.

## Page speed and Core Web Vitals

The homepage scores 69 on mobile because one photo takes 18.8 seconds to load on a mid-range phone. The rest of the page is light. Real-user Core Web Vitals: no data, because the site has too little traffic for Chrome to report.

Homepage, PageSpeed Insights:

- Performance: mobile 69, desktop 79 (good is 90+).
- Largest Contentful Paint: mobile 18.8 s, desktop 3.3 s (good is under 2.5 s).
- First Contentful Paint: mobile 2.6 s, desktop 0.8 s (good is under 1.8 s).
- Total Blocking Time: mobile 90 ms, desktop 30 ms (good is under 200 ms).
- Cumulative Layout Shift: mobile 0.022 (good is under 0.1).
- Total page weight: 3,511 KB (good is under 1,600 KB).

Causes, in order of impact:

1. `/assets/images/IMG_4687.JPG` is 3,010 KB, loaded as a CSS background, so the browser finds it late. It is 86% of the page’s weight and it is the LCP element.
2. The custom font loads as `.otf` (`GalanoClassicAltRegular.otf`) instead of compressed `.woff2`.
3. About 144 KB of unused JavaScript, most likely from Google Tag Manager and a separate gtag script both loading. If GTM already fires GA4, remove the separate gtag script.

Fix: export the image at 1,920 px wide as WebP or AVIF (target under 200 KB), add `<link rel="preload" as="image">` for it, and serve a smaller version below 768 px. That alone should bring mobile LCP under about 3 s and the score into the high 80s or 90s. Re-test in PageSpeed Insights after deploying.

Server response averaged about 260 ms across all 58 pages, which is fine.

## Local SEO

Webstar is set up correctly as a service-area business, but its schema and off-site signals don’t yet tell Google where and how well it serves.

- **Name, phone, email — pass.** Webstar Business Services LLC, 248-564-3663, ian@webstarbusinessservices.com in the footer of every page.
- **Address — OK.** No street address, which is correct for a service-area business; footer says “Metro Detroit, MI”.
- **Google Business Profile — linked.** Organization schema `sameAs` points to the Maps listing; the homepage mentions GBP setup.
- **Business schema type — gap.** Organization + ProfessionalService on one page only; no LocalBusiness with `areaServed` cities, `priceRange`, or opening hours site-wide.
- **Reviews on site — partial.** Homepage has a “What clients say” section; no link to leave or read Google reviews.
- **City pages — strong.** 10 city + 4 region pages with unique copy.
- **Citations — not checked.** Verify Yelp, BBB, Bing Places, Apple Business Connect, Nextdoor, and the Michigan chamber listings use the same name and phone.

Fixes:

1. Upgrade the homepage schema to `ProfessionalService` (a LocalBusiness subtype). Include `areaServed` listing the 10 cities and 3 counties, `telephone`, `priceRange` (“$350–$$$”), logo, and `sameAs` for every profile.
2. Add a “Read our Google reviews” link and a review-request link on `/contact/` and in the footer. Then follow the site’s own guide on getting the first 10 Google reviews.
3. Set up Bing Places and Apple Business Connect, and list on 5–10 Michigan and small-business directories with identical name, phone, and website.
4. Get links from local business groups (chambers of commerce in Troy, Royal Oak, Birmingham-Bloomfield). Add a “Website by Webstar” footer credit on client sites, starting with Chrome City Guide Service.

## Prioritized action plan

Do the first five this week. Together they fix the speed problem and every title and description that Google truncates. After items 1–5, re-test the homepage in PageSpeed Insights. The target is mobile performance 90+ and LCP under 2.5 s.

1. **High.** Compress and preload the homepage background image (`IMG_4687.JPG`, 3 MB to under 200 KB WebP).
2. **High.** Change the title suffix to `| Webstar` site-wide and hand-shorten the 7 remaining long titles.
3. **Medium.** Trim the 29 long meta descriptions to 140–155 characters.
4. **Medium.** Convert the Galano font from `.otf` to `.woff2`; remove the duplicate gtag if GTM already fires GA4.
5. **Medium.** Upgrade the homepage schema to ProfessionalService with `areaServed`, `priceRange`, and `sameAs`.
6. **Medium.** Add “Metro Detroit” to all 13 industry page titles and H1s.
7. **Medium.** Render the 3 wedding pages as static HTML, or move them to their own domain.
8. **High.** Publish 3–5 case studies with screenshots and results; expand `/examples/`.
9. **Medium.** Add a real project image to each city and industry page.
10. **High.** Build citations (Bing Places, Apple Business Connect, Yelp, BBB, chambers) and add review links.
11. **Low.** Expand the Business Growth package page to the depth of the other tiers.
12. **High.** Connect Search Console, check how many of the 58 pages are indexed, and confirm keyword volumes in Google Keyword Planner.

## Sources

- Crawl of all 58 URLs in `webstarbusinessservices.com/sitemap.xml` and `robots.txt`, 19 Oct 2026.
- PageSpeed Insights report, mobile and desktop, 19 Oct 2026 (Lighthouse 13.5, emulated Moto G Power, slow 4G).
- Homepage load measured in Chrome (resource sizes and server response times), 19 Oct 2026.
