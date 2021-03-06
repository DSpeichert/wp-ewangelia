<?php
// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

global $post;

$query = new WP_Query(
    [
        'post_type'      => 'liturgy',
        'meta_key'       => 'liturgy_type',
        'meta_value'     => 'EVANGELIUM',
        'posts_per_page' => 1,
    ]
);

if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
    <p>
        <em><?php echo date_i18n(get_option('date_format'), strtotime($post->post_date)) ?></em><br/>
        <a href="<?php the_permalink(); ?>" rel="bookmark" title="Link do <?php the_title_attribute(); ?>"><?php the_title(); ?></a><br/>
        <?php echo get_the_excerpt();
        if (array_key_exists('link_page', $instance) && !empty($instance['link_page'])) {
            echo ' <a href="' . get_permalink($instance['link_page']) . '">Czytaj całość</a>';
        }
        ?>
    </p>
<?php endwhile;
    wp_reset_postdata();
else : ?>
    <p>Nie znaleziono Ewangelii na dzisiejszy dzień.</p>
<?php endif; ?>
