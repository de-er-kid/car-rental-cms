<?php
class Cars_Why_Chose_Meta {
    private $post_type = 'cars';
    private $meta_box_id = 'why_chose_this_car';
    private $meta_box_title = 'Why Chose This Car';
    private $fields = [
        'why_title' => 'Why Chose Title',
        'why_content' => 'Content',
        'why_chose_image1' => 'Image 1',
        'why_chose_image2' => 'Image 2',
    ];

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta_box']);
    }

    public function add_meta_box() {
        add_meta_box(
            $this->meta_box_id,
            $this->meta_box_title,
            [$this, 'render_meta_box'],
            $this->post_type
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field($this->meta_box_id, $this->meta_box_id . '_nonce');
        $values = get_post_meta($post->ID);
        ?>
        <p>
            <label for="why_title"><?php echo $this->fields['why_title']; ?></label>
            <input type="text" name="why_title" id="why_title" class="widefat" value="<?php echo esc_attr($values['why_title'][0] ?? ''); ?>">
        </p>
        <p>
            <label for="why_content"><?php echo $this->fields['why_content']; ?></label>
            <?php
            $content = $values['why_content'][0] ?? '';
            wp_editor($content, 'why_content', [
                'textarea_name' => 'why_content',
                'textarea_rows' => 5,
            ]);
            ?>
        </p>
        <p>
            <label for="why_chose_image1"><?php echo $this->fields['why_chose_image1']; ?></label>
            <input type="hidden" name="why_chose_image1" id="why_chose_image1" class="widefat" value="<?php echo esc_url($values['why_chose_image1'][0] ?? ''); ?>">
            <button class="button upload_image_button">Upload</button>
            <img id="preview_image1" src="<?php echo esc_url($values['why_chose_image1'][0] ?? ''); ?>" style="max-width: 300px; margin-top: 10px; display: <?php echo isset($values['why_chose_image1'][0]) ? 'block' : 'none'; ?>;">
        </p>
        <p>
            <label for="why_chose_image2"><?php echo $this->fields['why_chose_image2']; ?></label>
            <input type="hidden" name="why_chose_image2" id="why_chose_image2" class="widefat" value="<?php echo esc_url($values['why_chose_image2'][0] ?? ''); ?>">
            <button class="button upload_image_button">Upload</button>
            <img id="preview_image2" src="<?php echo esc_url($values['why_chose_image2'][0] ?? ''); ?>" style="max-width: 300px; margin-top: 10px; display: <?php echo isset($values['why_chose_image2'][0]) ? 'block' : 'none'; ?>;">
        </p>
        <script>
            jQuery(document).ready(function($) {
                $('.upload_image_button').click(function(e) {
                    e.preventDefault();
                    let button = $(this);
                    let input = button.prev('input');
                    let preview = button.next('img');
                    let custom_uploader = wp.media({
                        title: 'Select Image',
                        button: {
                            text: 'Use This Image'
                        },
                        multiple: false
                    }).on('select', function() {
                        let attachment = custom_uploader.state().get('selection').first().toJSON();
                        input.val(attachment.url);
                        preview.attr('src', attachment.url).show();
                    }).open();
                });
            });
        </script>
        <?php
    }

    public function save_meta_box($post_id) {
        if (!isset($_POST[$this->meta_box_id . '_nonce']) || !wp_verify_nonce($_POST[$this->meta_box_id . '_nonce'], $this->meta_box_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        foreach ($this->fields as $field => $label) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            } elseif ($field === 'why_content') {
                update_post_meta($post_id, $field, wp_kses_post($_POST[$field]));
            }
        }
    }
}
