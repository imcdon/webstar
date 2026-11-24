<?php

declare(strict_types=1);

/**
 * DJ's Tackle structured catalog — one listing per product family, with
 * selectable options. Every option combination becomes a Square variation
 * with its own SKU and stock count.
 *
 * Pricing resolution order per variation:
 *   1. 'prices' keyed by "Value1 / Value2" (option order)
 *   2. 'price_by' => ['OptionName' => ['Value' => cents]]
 *   3. 'price_cents'
 *
 * Consumed by: bin/seed-local.php, ../shared/shop/bin/seed-sandbox.php,
 *              bin/export-products-csv.php, bin/set-inventory.php
 */

$colors = [
    'Red & Black',
    'Red Ice',
    'Galaxy',
    'Royal D',
    'Chartreuse',
    "Hooked'em",
    'Antifreeze Ice',
    'Antifreeze',
    'Tequila Sunrise',
    'Black Ice',
    'Orange Ice',
    'Krazy Karen',
    'Blue Ice',
    'White Ice',
    'Wonderbread',
    'Dirty Larry',
    'Lincoln Log',
    'Lincoln',
    'Green Goby',
    'Drunken Sparkle',
    'Purple Ice',
    'Purple Panther',
    'Nightmare',
    'Bloody Mary',
    'Yellow Jacket',
    'Lemon Head',
    'Captains Choice',
    'Fire Tiger',
    'Pink Ice',
    'Sapphire Sparkle',
    'Scary Larry',
    'Rootbeer Ice',
    'Day Drinker',
    'Midnight Moon',
    'Pimp Daddy',
    'Vampire',
];

$jigColorImages = [
    'Red & Black' => 'assets/img/products/jigs/IMG_6289.jpg',
    'Red Ice' => 'assets/img/products/jigs/IMG_6290.jpg',
    'Galaxy' => 'assets/img/products/jigs/IMG_6292.jpg',
    'Royal D' => 'assets/img/products/jigs/IMG_6297.jpg',
    'Chartreuse' => 'assets/img/products/jigs/IMG_6298.jpg',
    "Hooked'em" => 'assets/img/products/jigs/IMG_6299.jpg',
    'Antifreeze Ice' => 'assets/img/products/jigs/IMG_6300.jpg',
    'Antifreeze' => 'assets/img/products/jigs/IMG_6303.jpg',
    'Tequila Sunrise' => 'assets/img/products/jigs/IMG_6305.jpg',
    'Black Ice' => 'assets/img/products/jigs/IMG_6307.jpg',
    'Orange Ice' => 'assets/img/products/jigs/IMG_6308.jpg',
    'Krazy Karen' => 'assets/img/products/jigs/IMG_6309.jpg',
    'Blue Ice' => 'assets/img/products/jigs/IMG_6312.jpg',
    'White Ice' => 'assets/img/products/jigs/IMG_6313.jpg',
    'Wonderbread' => 'assets/img/products/jigs/IMG_6314.jpg',
    'Dirty Larry' => 'assets/img/products/jigs/IMG_6316.jpg',
    'Lincoln Log' => 'assets/img/products/jigs/IMG_6317.jpg',
    'Lincoln' => 'assets/img/products/jigs/IMG_6318.jpg',
    'Green Goby' => 'assets/img/products/jigs/IMG_6319.jpg',
    'Drunken Sparkle' => 'assets/img/products/jigs/IMG_6321.jpg',
    'Purple Ice' => 'assets/img/products/jigs/IMG_6322.jpg',
    'Purple Panther' => 'assets/img/products/jigs/IMG_6324.jpg',
    'Nightmare' => 'assets/img/products/jigs/IMG_6325.jpg',
    'Bloody Mary' => 'assets/img/products/jigs/IMG_6326.jpg',
    'Yellow Jacket' => 'assets/img/products/jigs/IMG_6327.jpg',
    'Lemon Head' => 'assets/img/products/jigs/IMG_6328.jpg',
    'Captains Choice' => 'assets/img/products/jigs/IMG_6329.jpg',
    'Fire Tiger' => 'assets/img/products/jigs/IMG_6330.jpg',
    'Pink Ice' => 'assets/img/products/jigs/IMG_6331.jpg',
    'Sapphire Sparkle' => 'assets/img/products/jigs/IMG_6332.jpg',
    'Scary Larry' => 'assets/img/products/jigs/IMG_6333.jpg',
    'Rootbeer Ice' => 'assets/img/products/jigs/IMG_6334.jpg',
    'Day Drinker' => 'assets/img/products/jigs/IMG_6335.jpg',
    'Midnight Moon' => 'assets/img/products/jigs/IMG_6336.jpg',
    'Pimp Daddy' => 'assets/img/products/jigs/IMG_6337.jpg',
    'Vampire' => 'assets/img/products/jigs/IMG_6338.jpg',
];

$cdn = 'https://d2j6dbq0eux0bg.cloudfront.net/images/100207139';

return [
    [
        'key' => 'walleye-jigs',
        'name' => 'Walleye Jigs (2 pack)',
        'category' => 'Walleye Jigs',
        'description' => "Hand-poured walleye jigs with chip-resistant paint and razor-sharp hooks. Sold in 2-packs. Pick your weight and color.",
        'local_image' => $jigColorImages['Antifreeze'],
        'images' => [
            "$cdn/products/642715698/4276142471.jpg",
            "$cdn/products/642715698/4276142473.jpg",
            "$cdn/products/642715698/4276144179.jpg",
            "$cdn/products/642715698/4276144181.jpg",
        ],
        'color_images' => $jigColorImages,
        'options' => [
            'Size' => ['3/8 oz', '1/2 oz', '5/8 oz', '3/4 oz', '1 oz', '1.5 oz'],
            'Color' => $colors,
        ],
        'price_by' => [
            'Size' => [
                '3/8 oz' => 275,
                '1/2 oz' => 275,
                '5/8 oz' => 300,
                '3/4 oz' => 300,
                '1 oz' => 300,
                '1.5 oz' => 350,
            ],
        ],
        'quantity' => 25,
        'sku_prefix' => 'DJT-WJ',
    ],
    [
        'key' => 'bottom-bouncers',
        'name' => 'Bottom Bouncers',
        'category' => 'Bottom Bouncers',
        'description' => 'Bottom bouncers for trolling crawler harnesses along the bottom. Choose your weight.',
        'local_image' => 'seed/images/650096449.jpg',
        'images' => ["$cdn/4278518528.jpg"],
        'options' => [
            'Size' => ['1/2 oz', '3/4 oz', '1 oz', '1.5 oz', '2 oz', '2.5 oz', '3 oz', '4 oz', '5 oz', '6 oz', '8 oz'],
        ],
        'price_by' => [
            'Size' => [
                '1/2 oz' => 200, '3/4 oz' => 200, '1 oz' => 200, '1.5 oz' => 200,
                '2 oz' => 250, '2.5 oz' => 250, '3 oz' => 250,
                '4 oz' => 300, '5 oz' => 300,
                '6 oz' => 400, '8 oz' => 400,
            ],
        ],
        'quantity' => 50,
        'sku_prefix' => 'DJT-BB',
    ],
    [
        'key' => 'crawler-harness',
        'name' => 'Crawler Harness — Colorado Blade',
        'category' => 'Crawler Harnesses',
        'description' => '36 in. double-hook crawler harness with beads and a Colorado blade. Choose your blade size.',
        'local_image' => 'seed/images/650096453.jpg',
        'images' => [
            "$cdn/products/650096453/4278520121.jpg",
            "$cdn/products/657197493/4278518747.jpg",
            "$cdn/products/657227030/4278520131.jpg",
        ],
        'options' => [
            'Blade Size' => ['Size 4', 'Size 5', 'Size 6'],
        ],
        'price_cents' => 375,
        'quantity' => 25,
        'sku_prefix' => 'DJT-CH',
    ],
    [
        'key' => 'stinger-hooks',
        'name' => 'Stinger Hooks (10 pack)',
        'category' => 'Stinger Hooks',
        'description' => 'Size #10 stinger hooks, 10 per pack. Available plain or with a glow bead.',
        'local_image' => 'seed/images/643425138.jpg',
        'images' => [
            "$cdn/products/643425138/4217178170.jpg",
            "$cdn/products/643425639/4217198317.jpg",
        ],
        'options' => [
            'Style' => ['Plain', 'With Glow Bead'],
        ],
        'price_by' => [
            'Style' => ['Plain' => 1000, 'With Glow Bead' => 1100],
        ],
        'quantity' => 25,
        'sku_prefix' => 'DJT-SH',
    ],
    [
        'key' => 'arctic-spinners',
        'name' => 'Arctic Spinners',
        'category' => 'Arctic Spinners',
        'description' => 'We are an authorized Arctic Spinners dealer. We can get any color on their site but do not carry a large stock, so orders may take a bit longer to ship.',
        'local_image' => 'seed/images/650099919.jpg',
        'images' => [
            "$cdn/products/650099919/4278544837.jpg",
            "$cdn/products/650130274/4278544787.jpg",
        ],
        'options' => [
            'Finish' => ['Standard', 'Glow'],
        ],
        'price_by' => [
            'Finish' => ['Standard' => 600, 'Glow' => 630],
        ],
        'quantity' => 25,
        'sku_prefix' => 'DJT-AS',
    ],
    [
        'key' => 'river-ready-box',
        'name' => 'River Ready Box',
        'category' => 'River Ready Boxes',
        'description' => "Stocked tackle boxes ready for the river. Small boxes include 4 colors (5 per color) of jig heads with matching plastics and 10 stinger hooks. Large boxes include 8 colors (5 per color) and 20 stinger hooks.",
        'local_image' => 'seed/images/733129922.jpg',
        'images' => [
            "$cdn/4276076872.jpg",
            "$cdn/products/733129922/4828439667.jpg",
            "$cdn/4276058828.jpg",
            "$cdn/products/733135612/4828451551.jpg",
        ],
        'options' => [
            'Box Size' => ['Small', 'Large'],
            'Jig Style' => ['Round Ball', 'Walleye', 'Walleye 1.5 oz'],
        ],
        'prices' => [
            'Small / Round Ball' => 4750,
            'Small / Walleye' => 5000,
            'Small / Walleye 1.5 oz' => 5500,
            'Large / Round Ball' => 9000,
            'Large / Walleye' => 9500,
            'Large / Walleye 1.5 oz' => 10000,
        ],
        'quantity' => 25,
        'sku_prefix' => 'DJT-RRB',
    ],
];
