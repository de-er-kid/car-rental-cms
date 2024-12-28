<?php


/**
 * Class Car_Benifits_Metabox
 *
 * Adds a metabox for managing FAQ content.
 */

class Car_Benifits_Metabox
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_car_benefits_meta_box']);
        add_action('save_post', [$this, 'save_car_benefits_meta_box_data']);
    }

    public function add_car_benefits_meta_box()
    {
        add_meta_box(
            'car_benefits_meta_box',
            'Car Benefits',
            [$this, 'render_car_benefits_meta_box'],
            'cars',
            'normal',
            'high'
        );
    }

    public function render_car_benefits_meta_box($post)
    {
        wp_nonce_field('save_car_benefits', 'car_benefits_nonce');

        $benefit_title = get_post_meta($post->ID, '_benefit_title', true);
        $benefit_content = get_post_meta($post->ID, '_benefit_content', true);
        $benefit_image = get_post_meta($post->ID, '_benefit_image', true);

        ?>
        <p>
            <label for="benefit_title"><strong>Benefit Title:</strong></label>
            <input type="text" name="benefit_title" id="benefit_title" value="<?php echo esc_attr($benefit_title); ?>"
                style="width:100%;" />
        </p>
        <p>
            <label for="benefit_content"><strong>Content:</strong></label>
            <?php
            $editor_settings = [
                'textarea_name' => 'benefit_content',
                'textarea_rows' => 5,
                'media_buttons' => true,
            ];
            wp_editor($benefit_content, 'benefit_content_editor', $editor_settings);
            ?>
        </p>
        <p>
            <label for="benefit_image"><strong>Image:</strong></label>
            <input type="hidden" name="benefit_image" id="benefit_image" value="<?php echo esc_attr($benefit_image); ?>"
                style="width:80%;" />
            <img alt="" src="<?php echo esc_attr($benefit_image); ?>" style="width: 200px" class="benefit-image-preview">
            <button type="button" class="button button-secondary upload_image_button">Upload Image</button>
        </p>
        <script>
            jQuery(document).ready(function ($) {
                $('.upload_image_button').on('click', function (e) {
                    e.preventDefault();
                    var button = $(this);
                    var custom_uploader = wp.media({
                        title: 'Select Image',
                        button: { text: 'Use this image' },
                        multiple: false
                    })
                        .on('select', function () {
                            var attachment = custom_uploader.state().get('selection').first().toJSON();
                            $('#benefit_image').val(attachment.url);
                            $('.benefit-image-preview').attr('src', attachment.url);
                        })
                        .open();
                });
            });
        </script>
        <?php
    }

    public function save_car_benefits_meta_box_data($post_id)
    {
        if (!isset($_POST['car_benefits_nonce']) || !wp_verify_nonce($_POST['car_benefits_nonce'], 'save_car_benefits')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['benefit_title'])) {
            update_post_meta($post_id, '_benefit_title', sanitize_text_field($_POST['benefit_title']));
        }

        if (isset($_POST['benefit_content'])) {
            update_post_meta($post_id, '_benefit_content', wp_kses_post($_POST['benefit_content']));
        }

        if (isset($_POST['benefit_image'])) {
            update_post_meta($post_id, '_benefit_image', esc_url_raw($_POST['benefit_image']));
        }
    }

}