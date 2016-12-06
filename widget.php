<?php

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

class Ewangelia_Widget extends WP_Widget
{

    /**
     *
     * ewangelia-widget
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * widget file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $widget_slug = 'ewangelia-widget';

    /*--------------------------------------------------*/
    /* Constructor
    /*--------------------------------------------------*/

    /**
     * Specifies the classname and description, instantiates the widget,
     * loads localization files, and includes necessary stylesheets and JavaScript.
     */
    public function __construct()
    {
        parent::__construct(
            $this->widget_slug,
            'Ewangelia',
            [
                'classname'   => $this->widget_slug . '-class',
                'description' => __('Pokazuje Ewangelię na dzień dzisiejszy.', $this->widget_slug),
            ]
        );
    }


    /*--------------------------------------------------*/
    /* Widget API Functions
    /*--------------------------------------------------*/

    /**
     * Outputs the content of the widget.
     *
     * @param array $args     The array of form elements
     * @param array $instance The current instance of the widget
     * @return null
     */
    public function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);

        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }

        require(plugin_dir_path(__FILE__) . 'widget_view.php');

        echo $after_widget;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return null
     */
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = 'Ewangelia na dziś';
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
            <label for="<?php echo $this->get_field_name('link_page'); ?>"><?php _e('ID strony z liturgią:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('link_page'); ?>" name="<?php echo $this->get_field_name('link_page'); ?>" type="text"
                   value="<?php echo esc_attr($instance['link_page']); ?>"/>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['link_page'] = (!empty($new_instance['link_page'])) ? (int)trim(strip_tags($new_instance['link_page'])) : '';

        return $instance;
    }
}

