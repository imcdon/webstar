<?php

declare(strict_types=1);

/**
 * Monthly options that sit alongside the one-time website packages.
 *
 * - care_plans: optional month-to-month support after any website launch.
 * - waas: Website as a Service — build + hosting + Google + Standard Care Plan
 *   for a flat monthly fee with a 6-month minimum; client owns the files after month 6.
 */
return [
    'care_plans' => [
        'care-essential' => [
            'slug' => 'care-essential',
            'title' => 'Essential Care',
            'price' => '$49',
            'period' => 'per month',
            'summary' => 'Keep the site online, backed up, and secure.',
            'includes' => [
                'Hosting on Webstar’s managed server (or management of your own cPanel host)',
                'Daily backups with 30-day retention',
                'Uptime monitoring and SSL renewal',
                'PHP, security, and dependency updates',
                '30 minutes of content edits per month',
                'Email support, 1-business-day response',
            ],
            'badge' => '',
            'tier' => 1,
        ],
        'care-standard' => [
            'slug' => 'care-standard',
            'title' => 'Standard Care',
            'price' => '$99',
            'period' => 'per month',
            'summary' => 'Everything in Essential plus regular edits and a monthly report.',
            'includes' => [
                'Everything in Essential Care',
                '60 minutes of content edits per month',
                'Monthly Analytics and Search Console report in plain English',
                'Google Business Profile check and photo/post refresh',
                'Priority support, same-business-day response',
            ],
            'badge' => 'Included with Website as a Service',
            'tier' => 2,
        ],
        'care-premium' => [
            'slug' => 'care-premium',
            'title' => 'Premium Care',
            'price' => '$179',
            'period' => 'per month',
            'summary' => 'For businesses that change often and want a hands-on partner.',
            'includes' => [
                'Everything in Standard Care',
                '120 minutes of content edits or small features per month',
                'Quarterly strategy call and conversion review',
                'Seasonal promo updates and new landing sections',
                'Phone and text support during business hours',
            ],
            'badge' => '',
            'tier' => 3,
        ],
    ],
    'waas' => [
        'slug' => 'website-as-a-service',
        'title' => 'Website as a Service',
        'price' => '$150',
        'period' => 'per month',
        'minimum_term' => '6-month minimum',
        'meta_description' => 'Website as a Service: custom website build, hosting, Google Business Profile, Analytics, and Standard Care for $150 per month with a 6-month minimum. You own the files after month 6.',
        'summary' => 'No large upfront payment. We build your site, host it, set up Google, and keep it maintained for one flat monthly fee. After six months the files are yours.',
        'includes' => [
            'Custom website build at the tier that fits your business (scope fixed in writing at signing)',
            'Google Business Profile, Analytics 4, and Search Console setup',
            'Standard Care Plan included: hosting, backups, monitoring, 60 minutes of edits, monthly report',
            'Domain registered in your name; you keep it no matter what',
            'After month 6 you own all site files — keep the plan, switch to a Care Plan, or move to your own hosting',
            'Cancel any time after the minimum term with a full export of your site',
        ],
        'ideal_for' => 'New businesses and owners who prefer a predictable monthly cost over a one-time project fee.',
        'faq' => [
            [
                'q' => 'What happens after six months?',
                'a' => 'You own the site files outright. Most clients keep paying $150/mo for hosting, Google reporting, and edits. You can also drop to a smaller Care Plan or take your files to your own hosting at no charge.',
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
