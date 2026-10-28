<?php

declare(strict_types=1);

/**
 * Monthly options that sit alongside the one-time website packages.
 *
 * - care_plans: optional month-to-month support after any website launch.
 * - waas: Website as a Service — build + hosting + Google + ongoing care
 *   for a flat monthly fee with a 6-month minimum; client owns the files after month 6.
 */
return [
    'care_plans' => [
        'care' => [
            'slug' => 'care',
            'title' => 'Care Plan',
            'price' => '$50',
            'period' => 'per month',
            'summary' => 'Hosting, backups, edits, Google reporting, and a quarterly strategy call.',
            'includes' => [
                'Hosting on Webstar’s managed server (or management of your own cPanel host)',
                'Daily backups with 30-day retention',
                'Uptime monitoring and SSL renewal',
                'PHP, security, and dependency updates',
                '120 minutes of content edits or small features per month',
                'Monthly Analytics and Search Console report in plain English',
                'Google Business Profile check and photo/post refresh',
                'Quarterly strategy call and conversion review',
                'Seasonal promo updates and new landing sections',
                'Phone and text support during business hours',
            ],
            'badge' => '',
            'tier' => 1,
        ],
    ],
    'waas' => [
        'slug' => 'website-as-a-service',
        'title' => 'Website as a Service',
        'price' => '$150',
        'period' => 'per month',
        'minimum_term' => '6-month minimum',
        'meta_description' => 'Website as a Service: custom website build, hosting, Google Business Profile, Analytics, and ongoing care for $150 per month with a 6-month minimum. You own the files after month 6.',
        'summary' => 'No large upfront payment. We build your site, host it, set up Google, and keep it maintained for one flat monthly fee. After six months the files are yours.',
        'includes' => [
            'Custom website build at the tier that fits your business (scope fixed in writing at signing)',
            'Google Business Profile, Analytics 4, and Search Console setup',
            'Hosting, backups, monitoring, 60 minutes of edits, and a monthly report included',
            'Domain registered in your name; you keep it no matter what',
            'After month 6 you own all site files — keep the plan, switch to a Care Plan, or move to your own hosting',
            'Cancel any time after the minimum term with a full export of your site',
        ],
        'ideal_for' => 'New businesses and owners who prefer a predictable monthly cost over a one-time project fee.',
        'faq' => [
            [
                'q' => 'What happens after six months?',
                'a' => 'You own the site files outright. Most clients keep paying $150/mo for hosting, Google reporting, and edits. You can also switch to the $50 Care Plan or take your files to your own hosting at no charge.',
            ],
            [
                'q' => 'What if I cancel before six months?',
                'a' => 'The remaining months of the minimum term are due before files transfer. Your domain is always yours because it is registered in your name.',
            ],
            [
                'q' => 'Which website tier do I get?',
                'a' => 'We agree on the scope (pages, booking, brand kit) in writing before work starts, based on what your business needs. Changes after signing use a simple change order.',
            ],
            [
                'q' => 'How is this billed?',
                'a' => 'Stripe autopay on the same day each month. The first payment starts the build; the site launches once it passes your review.',
            ],
        ],
    ],
];
