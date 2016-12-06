<?php
// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
?>
<p style="text-align: right;"><?php echo date('l, j F Y'); ?></p>
<h2>Pierwsze Czytanie</h2>
<?php
$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_query'     => [
            [
                'key'   => 'liturgy_type',
                'value' => 'LECTIO 1',
            ],
            [
                'key'   => 'liturgy_date',
                'value' => date('Y-m-d'),
            ],
        ],
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php the_content(); ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono pierwszego czytania na dzisiejszy dzień.</p>
<?php endif; ?>

<h2>Psalm</h2>
<?php
$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_query'     => [
            [
                'key'   => 'liturgy_type',
                'value' => 'PSALMUS',
            ],
            [
                'key'   => 'liturgy_date',
                'value' => date('Y-m-d'),
            ],
        ],
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php the_content(); ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono psalmu na dzisiejszy dzień.</p>
<?php endif; ?>

<h2>Drugie Czytanie</h2>
<?php
$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_query'     => [
            [
                'key'   => 'liturgy_type',
                'value' => 'LECTIO 2',
            ],
            [
                'key'   => 'liturgy_date',
                'value' => date('Y-m-d'),
            ],
        ],
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php the_content(); ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono drugiego czytania na dzisiejszy dzień.</p>
<?php endif; ?>

<h2>Ewangelia</h2>
<?php
$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_query'     => [
            [
                'key'   => 'liturgy_type',
                'value' => 'EVANGELIUM',
            ],
            [
                'key'   => 'liturgy_date',
                'value' => date('Y-m-d'),
            ],
        ],
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php the_content(); ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono ewangelii na dzisiejszy dzień.</p>
<?php endif; ?>

<h2>Kazanie</h2>
<?php
$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_query'     => [
            [
                'key'   => 'liturgy_type',
                'value' => 'MEDITATIO',
            ],
            [
                'key'   => 'liturgy_date',
                'value' => date('Y-m-d'),
            ],
        ],
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php the_content(); ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono kazania na dzisiejszy dzień.</p>
<?php endif; ?>
