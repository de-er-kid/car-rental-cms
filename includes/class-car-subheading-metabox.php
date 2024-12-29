<?php

/**
 * cars cpt - subheading metabox
 */

class Car_Subheading_Metabox
{

    private $post_type = 'cars';

    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
    }

    public function add_meta_box()
    {
        add_meta_box(
            'car_subheading',
            __('Car Subheading', 'car-rental-cms'),
            array($this, 'render_meta_box'),
            $this->post_type,
            'normal',
            'high'
        );
    }

    public function render_meta_box($post)
    {
        wp_nonce_field('car_subheading_nonce', 'car_subheading_nonce');
        $value = get_post_meta($post->ID, '_car_subheading', true);
        ?>
        <label for="car_subheading"><?php _e('Subheading:', 'car-rental-cms'); ?></label>
        <input type="text" id="car_subheading" name="car_subheading" value="<?php echo esc_attr($value); ?>" size="50">
        <?php
    }

    public function save_meta_box($post_id)
    {
        if (!isset($_POST['car_subheading_nonce']) || !wp_verify_nonce($_POST['car_subheading_nonce'], 'car_subheading_nonce')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['car_subheading'])) {
            update_post_meta($post_id, '_car_subheading', sanitize_text_field($_POST['car_subheading']));
        }
    }
}