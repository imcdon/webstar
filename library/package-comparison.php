<?php

declare(strict_types=1);

$packages = webstar_packages();
$featureLabels = webstar_feature_labels();
?>
<div class="compare">
    <p class="compare__hint">Swipe sideways to compare packages.</p>
    <div class="compare__scroll" tabindex="0" role="region" aria-label="Package comparison table">
        <table class="compare-table">
            <caption>Compare Webstar packages</caption>
            <thead>
                <tr>
                    <th scope="col">Feature</th>
                    <?php foreach ($packages as $pkg) :
                        $featured = ($pkg['badge'] ?? '') !== '';
                        $colClass = $featured ? 'compare-col--featured' : '';
                        ?>
                        <th scope="col" class="<?php echo webstar_h($colClass); ?>">
                            <?php echo webstar_h((string) $pkg['nav_label']); ?>
                            <?php if ($featured) : ?>
                                <span class="compare-badge"><?php echo webstar_h((string) $pkg['badge']); ?></span>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Price</th>
                    <?php foreach ($packages as $pkg) :
                        $featured = ($pkg['badge'] ?? '') !== '';
                        $colClass = $featured ? 'compare-col--featured' : '';
                        ?>
                        <td class="<?php echo webstar_h($colClass); ?>">
                            <span class="compare-value"><?php echo webstar_h((string) $pkg['price']); ?></span>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <th scope="row">Turnaround</th>
                    <?php foreach ($packages as $pkg) :
                        $featured = ($pkg['badge'] ?? '') !== '';
                        $colClass = $featured ? 'compare-col--featured' : '';
                        ?>
                        <td class="<?php echo webstar_h($colClass); ?>">
                            <?php echo webstar_h((string) $pkg['turnaround']); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php foreach ($featureLabels as $key => $label) : ?>
                    <tr>
                        <th scope="row"><?php echo webstar_h($label); ?></th>
                        <?php foreach ($packages as $pkg) :
                            $featured = ($pkg['badge'] ?? '') !== '';
                            $colClass = $featured ? 'compare-col--featured' : '';
                            $value = $pkg['features'][$key] ?? false;
                            ?>
                            <td class="<?php echo webstar_h($colClass); ?>">
                                <?php if (is_bool($value)) : ?>
                                    <?php if ($value) : ?>
                                        <span class="compare-check" aria-hidden="true">✓</span>
                                        <span class="visually-hidden">Included</span>
                                    <?php else : ?>
                                        <span class="compare-dash" aria-hidden="true">—</span>
                                        <span class="visually-hidden">Not included</span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="compare-value"><?php echo webstar_h((string) $value); ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <tr class="compare-cta">
                    <th scope="row"><span class="visually-hidden">Get started</span></th>
                    <?php foreach ($packages as $pkg) :
                        $featured = ($pkg['badge'] ?? '') !== '';
                        $colClass = $featured ? 'compare-col--featured' : '';
                        ?>
                        <td class="<?php echo webstar_h($colClass); ?>">
                            <a class="home-section__btn home-section__btn--primary" href="package.php?slug=<?php echo webstar_h((string) $pkg['slug']); ?>">
                                Details
                            </a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>
