<h2>Launch week is a checklist, not a feeling</h2>
<p>Staging approval is not the finish line. DNS, forms, and Google tools turn a design into an asset. I walk every Webstar build through this list — Simple through Growth — so you are not debugging form mail at midnight.</p>

<h2>Domain and DNS</h2>
<ul>
<li>Domain registered in <strong>your</strong> registrar account</li>
<li>Choose www or non-www; redirect the other</li>
<li>Lower TTL a day before cutover if moving hosts</li>
<li>Point A record or nameservers to agreed hosting</li>
<li>Enable 2FA on the registrar before launch week</li>
</ul>

<h2>SSL and hosting</h2>
<p>HTTPS loads without mixed-content warnings. Note backup location (Care Plan, host panel, or WaaS included hosting).</p>

<h2>Do not break email</h2>
<p>If you use Google Workspace or Microsoft 365, confirm MX records when web DNS changes — common DIY failure.</p>

<h2>Function tests</h2>
<ul>
<li>Contact form from mobile data → inbox you monitor</li>
<li>Tap-to-call on iOS and Android</li>
<li>Booking button (Business tier)</li>
<li>404 and favicon</li>
<li>Form labels and focus states for accessibility</li>
</ul>

<h2>Google</h2>
<ul>
<li>GA4 under your login; test call/form events</li>
<li>Search Console verified; submit sitemap</li>
<li>Block staging from indexing if it still exists</li>
<li>GBP website field = production URL; hours match site — see <a href="<?php echo webstar_h(webstar_url('resources/google-business-profile-checklist-metro-detroit/')); ?>">GBP checklist</a></li>
</ul>

<h2>Trust and comms</h2>
<p>Privacy policy linked if you collect form data. Update email signatures and social bios with the new URL. Tell staff what notification emails look like. Note the end of the post-launch fix window.</p>

<h2>Handoff differences</h2>
<p><strong>Outright:</strong> file archive and credentials doc. <strong>WaaS:</strong> managed hosting during the term; export after month six per <a href="<?php echo webstar_h(webstar_url('plans/')); ?>">plans</a>.</p>

<h2>When to call</h2>
<p>DNS not resolving after 24 hours, redirect loops, form spam floods — use the fix window. Freeze business name and domain for two weeks after go-live when possible.</p>

<p>Print this for your office manager. Boring launch days are good launch days. Need help? <a href="<?php echo webstar_h(webstar_url('contact/')); ?>">Contact</a>.</p>
