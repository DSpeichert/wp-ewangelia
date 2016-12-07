<?php
/**
 * @package   WP-Ewangelia
 * @author    Daniel Speichert <daniel@speichert.pl>
 * @license   GPL-2.0+
 * @copyright 2017 Daniel Speichert
 *
 * @wordpress-plugin
 * Plugin Name:       Liturgia
 * Plugin URI:        none
 * Description:       Widget Ewangelii + Shortcode Liturgii
 * Version:           1.0.0
 * Author:            Daniel Speichert
 * Author URI:        https://github.com/DSpeichert
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/DSpeichert/wp-ewangelia
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'widget.php');

// register widgets
add_action('widgets_init', function () {
    register_widget('Ewangelia_Widget');
});

// register custom post type
add_action('init', function () {
    register_post_type(
        'liturgy',
        [
            'labels'              => [
                'name'          => 'Liturgia',
                'singular_name' => 'Liturgia',
            ],
            'public'              => true,
            'exclude_from_search' => true,
            'has_archive'         => true,
            'rewrite'             => ['slug' => 'liturgia'],
            'supports'            => ['title', 'editor', 'custom-fields'],
        ]
    );
});

// register custom columns in admin view
add_filter('manage_liturgy_posts_columns', function ($columns) {
    return array_merge($columns, [
        'liturgy_type' => 'Typ Litrugii',
        'liturgy_date' => 'Data Liturgii',
    ]);
});

add_action('manage_posts_custom_column', function ($column, $post_id) {
    switch ($column) {
        case 'liturgy_type':
            echo get_post_meta($post_id, 'liturgy_type', true);
            break;

        case 'liturgy_date':
            echo get_post_meta($post_id, 'liturgy_date', true);
            break;
    }
}, 10, 2);

// add shortcode
add_action('init', function () {
    add_shortcode('liturgia', function ($atts = [], $content = null) {
        wp_ewangelia_pull();
        ob_start();
        require(plugin_dir_path(__FILE__) . 'liturgy_view.php');
        $content = ob_get_clean();

        return $content;
    });
});

// register pulling ewangelia
add_action('ewangelia_cron_hook', 'wp_ewangelia_pull');

// activation hook
register_activation_hook(__FILE__, function () {
    wp_schedule_event(strtotime('today midnight'), 'daily', 'ewangelia_cron_hook');
    wp_ewangelia_pull();
});

// deactivation hook
register_deactivation_hook(__FILE__, function () {
    wp_unschedule_event(wp_next_scheduled('ewangelia_cron_hook'), 'ewangelia_cron_hook');
});

// add Tools submenu for Admin
add_action('admin_menu', function () {
    add_management_page(
        'Ewangelia',
        'Ewangelia',
        'manage_options',
        'ewangelia',
        function () {
            // check user capabilities
            if (!current_user_can('manage_options')) {
                return;
            }
            ?>
            <div class="wrap">
                <h1><?= esc_html(get_admin_page_title()); ?></h1>
                <form method="post">
                    <input type="hidden" name="action" value="pull"/>
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_ewangelia_pull'); ?>"/>
                    <?php
                    submit_button('Importuj teraz nowe z RSS');
                    ?>
                </form>
                <form method="post">
                    <input type="hidden" name="action" value="import_legacy"/>
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wp_ewangelia_import_legacy'); ?>"/>
                    <?php
                    submit_button('Importuj stary format');
                    ?>
                </form>

                <?php
                if (isset($_POST['action']) && isset($_POST['nonce']) && $_POST['action'] === 'pull' && wp_verify_nonce($_POST['nonce'], 'wp_ewangelia_pull')) {
                    echo '<p>Importowanie danych...</p>';
                    echo '<p>';
                    wp_ewangelia_pull(true);
                    echo '</p>';
                } elseif (isset($_POST['action']) && isset($_POST['nonce']) && $_POST['action'] === 'import_legacy' && wp_verify_nonce($_POST['nonce'], 'wp_ewangelia_import_legacy')) {
                    echo '<p>Importowanie starej struktury danych...</p>';
                    echo '<p>';
                    wp_ewangelia_import_legacy(true);
                    echo '</p>';
                }

                ?>
            </div>
            <?php
        });
});

function wp_ewangelia_pull($debug = false)
{
    $rss = fetch_feed('http://evangelizo.org/rss/evangelizo_rss-pl.xml');
    if (is_wp_error($rss)) {
        if ($debug) {
            $error_string = $rss->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
        }

        return;
    }

    $rss_items = $rss->get_items(0, $rss->get_item_quantity(100));
    foreach ($rss_items as $item) {
        $content = $item->get_description();
        if (empty($content)) {
            continue;
        }
        $date = explode(' - ', $item->get_id())[0]; // guid
        $type = $item->get_category()->get_label(); // category
        $title = explode(' : ', $item->get_title())[1];


        if ($debug) {
            echo 'Processing ' . $item->get_id() . '<br />';
        }

        // find if already in DB
        $found_posts = get_posts(
            [
                'post_type'   => 'liturgy',
                'meta_query'  => [
                    [
                        'key'   => 'liturgy_type',
                        'value' => $type,
                    ],
                    [
                        'key'   => 'liturgy_date',
                        'value' => $date,
                    ],
                ],
                'numberposts' => 1,
            ]
        );

        if (empty($found_posts)) {
            // not in DB, add now
            $pid = wp_insert_post(
                [
                    'post_date'    => $date,
                    'post_content' => $content,
                    'post_title'   => $title,
                    'post_status'  => 'publish',
                    'post_type'    => 'liturgy',
                    'meta_input'   => [
                        'liturgy_type' => $type,
                        'liturgy_date' => $date,
                    ],
                ]
            );

            if ($debug) {
                echo 'Added ' . $item->get_id() . ' as post ID ' . $pid . '<br />';
            }
        } elseif ($debug) {
            echo 'Found ' . $item->get_id() . ' already in DB.<br />';
        }

    }
}

function wp_ewangelia_import_legacy($debug = false)
{
    global $wpdb;

    $r_count = $wpdb->get_var('select count(*) from ewangelia');

    for ($i = 0; $i < ceil($r_count / 50); $i++) {
        $old_records = $wpdb->get_results('SELECT * FROM ewangelia LIMIT 50 OFFSET ' . $i * 50);
        if ($old_records) {
            foreach ($old_records as $r) {
                if (empty(trim($r->content)) || empty(trim($r->title))) {
                    continue;
                }

                list($d, $m, $y) = explode('-', $r->date);

                // find if already in DB
                $found_posts = get_posts(
                    [
                        'post_type'   => 'liturgy',
                        'meta_query'  => [
                            [
                                'key'   => 'liturgy_type',
                                'value' => 'EVANGELIUM',
                            ],
                            [
                                'key'   => 'liturgy_date',
                                'value' => $y . '-' . $m . '-' . $d,
                            ],
                        ],
                        'numberposts' => 1,
                    ]
                );

                if (empty($found_posts)) {
                    // not in DB, add now
                    $pid = wp_insert_post(
                        [
                            'post_date'    => $y . '-' . $m . '-' . $d,
                            'post_content' => trim($r->content),
                            'post_title'   => trim(wp_strip_all_tags($r->title, true)),
                            'post_status'  => 'publish',
                            'post_type'    => 'liturgy',
                            'meta_input'   => [
                                'liturgy_type' => 'EVANGELIUM',
                                'liturgy_date' => $y . '-' . $m . '-' . $d,
                            ],
                        ]
                    );

                    if ($debug) {
                        echo 'Added ' . $r->date . ' as post ID ' . $pid . '<br />';
                    }
                } elseif ($debug) {
                    echo 'Found ' . $r->date . ' already in DB.<br />';
                }
            }
        }
    }
}
