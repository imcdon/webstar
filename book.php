<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/calendar.php';
$currentPage = 'book';
$config = webstar_config();
$pageTitle = 'Book a Free Consultation | Webstar';
$pageDescription = 'Book a free 1-on-1 consultation with Webstar Business Services. Talk through your website, Google setup, and whether a package or monthly plan fits.';
$canonicalPath = 'book/';

$bookStatus = isset($_GET['book_status']) ? (string) $_GET['book_status'] : '';
$bookReason = isset($_GET['book_reason']) ? (string) $_GET['book_reason'] : '';
$bookFieldsParam = isset($_GET['book_fields']) ? (string) $_GET['book_fields'] : '';
$bookFieldLabels = [
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'slot' => 'Time',
    'topic' => 'What you want to talk about',
];
$bookValidationFields = [];
if ($bookReason === 'validation' && $bookFieldsParam !== '') {
    foreach (explode(',', $bookFieldsParam) as $field) {
        $field = trim($field);
        if (isset($bookFieldLabels[$field])) {
            $bookValidationFields[] = $bookFieldLabels[$field];
        }
    }
}

$planSlug = isset($_GET['plan']) ? (string) $_GET['plan'] : '';
$planPrefill = '';
if ($planSlug !== '') {
    $plan = webstar_offer_by_slug($planSlug);
    if ($plan !== null) {
        $title = (string) ($plan['title'] ?? '');
        $price = (string) ($plan['price'] ?? '');
        $period = trim((string) ($plan['period'] ?? ''));
        if ($period !== '') {
            $planPrefill = 'I’m interested in the ' . $title . ' plan (' . $price . ' ' . $period . ').';
        } else {
            $planPrefill = 'I’m interested in the ' . $title . ' (' . $price . ').';
        }
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['book_token']) || !is_string($_SESSION['book_token'])) {
    $_SESSION['book_token'] = bin2hex(random_bytes(16));
}
$bookToken = $_SESSION['book_token'];
$bookSlots = webstar_calendar_public_slots();

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content contact-panel">
            <h1 class="home-section__heading">Book a free 1-on-1</h1>

            <div class="contact-form-block" id="book">
                <h2 class="contact-form-block__title">Choose a time</h2>

                <?php if ($bookStatus === 'success') : ?>
                    <div class="schedule-message schedule-message--success" role="status">
                        <p>You’re booked. A calendar invite is on its way to your email.</p>
                        <?php if ($bookReason === 'mail_config') : ?>
                            <p>You’re booked — your calendar invite should still arrive from Google. We couldn’t send the extra confirmation email from the site; Ian has your details on the calendar.</p>
                        <?php elseif ($bookReason === 'summary') : ?>
                            <p>The time is saved, but the short summary email did not send. I’ll still have your booking on the calendar.</p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($bookStatus === 'error') : ?>
                    <div class="schedule-message schedule-message--error" role="alert">
                        <?php if ($bookReason === 'validation' && $bookValidationFields !== []) : ?>
                            <p>Please check the following and try again:</p>
                            <ul class="schedule-message__list">
                                <?php foreach ($bookValidationFields as $label) : ?>
                                    <li><?php echo webstar_h($label); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif ($bookReason === 'taken') : ?>
                            <p>That time was just taken. Pick another open time.</p>
                        <?php elseif ($bookReason === 'config') : ?>
                            <p>Online booking is not connected yet. Call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php else : ?>
                            <p>We could not book that time. Please try again, call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a>, or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($bookSlots['ok'])) : ?>
                    <div class="schedule-message schedule-message--error" role="status">
                        <p>Online booking is not available right now<?php
                        $bookFailReason = (string) ($bookSlots['reason'] ?? '');
                        if ($bookFailReason !== '') {
                            echo ' (' . webstar_h($bookFailReason) . ')';
                        }
                        ?>. Call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a> and we’ll find a time.</p>
                    </div>
                <?php else :
                    $bookTimezone = (string) ($bookSlots['timezone'] ?? 'America/Detroit');
                    $bookTzLabel = $bookTimezone;
                    try {
                        $tzNow = new DateTimeImmutable('now', new DateTimeZone($bookTimezone));
                        $bookTzLabel = $tzNow->format('T') . ' · ' . $bookTimezone;
                    } catch (Exception) {
                        $bookTzLabel = $bookTimezone;
                    }
                    $bookOpenByDate = [];
                    $bookOpenOptions = [];
                    foreach (($bookSlots['days'] ?? []) as $day) {
                        $openSlots = [];
                        foreach ($day['slots'] as $slot) {
                            if (($slot['status'] ?? '') !== 'open') {
                                continue;
                            }
                            $openSlots[] = [
                                'start' => (string) $slot['start'],
                                'label' => (string) $slot['label'],
                            ];
                            $bookOpenOptions[] = [
                                'start' => (string) $slot['start'],
                                'label' => (string) $day['label'] . ' · ' . (string) $slot['label'],
                            ];
                        }
                        if ($openSlots !== []) {
                            $bookOpenByDate[(string) $day['date']] = [
                                'label' => (string) $day['label'],
                                'slots' => $openSlots,
                            ];
                        }
                    }
                    $bookFirstDate = $bookOpenByDate !== [] ? (string) array_key_first($bookOpenByDate) : '';
                    if ($bookOpenByDate === []) :
                        ?>
                    <p class="contact-form-block__lead">No open times in the next few weeks. Call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                    <?php else : ?>
                    <form class="intake-form contact-form book-form" method="post" action="<?php echo webstar_h(webstar_url('library/process-consultation.php')); ?>" novalidate>
                        <input type="hidden" name="book_token" value="<?php echo webstar_h($bookToken); ?>">
                        <div class="schedule-form__honeypot" aria-hidden="true">
                            <label for="book-website">Website</label>
                            <input type="text" id="book-website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="book-picker" data-book-picker>
                            <p class="book-picker__meta">Free 30-minute call · <?php echo webstar_h($bookTzLabel); ?></p>

                            <div class="book-fallback" data-book-fallback>
                                <div class="intake-form__field">
                                    <label for="book-slot-select">Time <span class="schedule-form__required">*</span></label>
                                    <select class="schedule-form__input" id="book-slot-select" name="slot" required>
                                        <option value="">Select a day and time</option>
                                        <?php foreach ($bookOpenOptions as $option) : ?>
                                            <option value="<?php echo webstar_h((string) $option['start']); ?>"><?php echo webstar_h((string) $option['label']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="book-picker__ui" data-book-ui hidden>
                                <div class="book-picker__layout">
                                    <div class="book-cal" aria-label="Choose a day">
                                        <div class="book-cal__nav">
                                            <button type="button" class="book-cal__nav-btn" data-book-prev aria-label="Previous month">‹</button>
                                            <h3 class="book-cal__month" data-book-month></h3>
                                            <button type="button" class="book-cal__nav-btn" data-book-next aria-label="Next month">›</button>
                                        </div>
                                        <div class="book-cal__weekdays" aria-hidden="true">
                                            <span>S</span><span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span>
                                        </div>
                                        <div class="book-cal__grid" data-book-grid role="grid" aria-labelledby="book-cal-month-label"></div>
                                    </div>
                                    <div class="book-times" aria-live="polite">
                                        <h3 class="book-times__title" data-book-day-label>Choose a day</h3>
                                        <div class="book-slots" data-book-times></div>
                                        <p class="book-times__empty" data-book-empty hidden>No open times on that day. Pick another day.</p>
                                    </div>
                                </div>
                            </div>

                            <script type="application/json" id="book-slots-data"><?php echo json_encode([
                                'timezone' => $bookTimezone,
                                'firstDate' => $bookFirstDate,
                                'days' => $bookOpenByDate,
                            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
                            <script src="<?php echo webstar_h(webstar_url('scripts/book-picker.js')); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/scripts/book-picker.js'); ?>" defer></script>
                        </div>

                        <div class="intake-form__field">
                            <label for="book-name">Name <span class="schedule-form__required">*</span></label>
                            <input class="schedule-form__input" type="text" id="book-name" name="name" required maxlength="120" autocomplete="name">
                        </div>
                        <div class="intake-form__field">
                            <label for="book-email">Email <span class="schedule-form__required">*</span></label>
                            <input class="schedule-form__input" type="email" id="book-email" name="email" required maxlength="254" autocomplete="email">
                        </div>
                        <div class="intake-form__field">
                            <label for="book-phone">Phone <span class="schedule-form__required">*</span></label>
                            <input class="schedule-form__input" type="tel" id="book-phone" name="phone" required maxlength="30" autocomplete="tel">
                        </div>
                        <div class="intake-form__field">
                            <label for="book-topic">What do you want to discuss? <span class="intake-form__optional">(optional)</span></label>
                            <textarea class="schedule-form__input" id="book-topic" name="topic" maxlength="2000" rows="3" placeholder="A brief note about your business or what you’d like to cover."><?php echo webstar_h($planPrefill); ?></textarea>
                        </div>

                        <div class="intake-form__actions">
                            <button type="submit" class="home-section__btn home-section__btn--primary">Book a 1-on-1</button>
                        </div>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
