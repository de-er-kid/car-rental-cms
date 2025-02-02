<?php
class Car_Loop_Gallery
{
    public function __construct()
    {
        add_shortcode("car_loop_gallery", array($this, "render_car_loop_gallery"));
    }

    public function render_car_loop_gallery()
    {
        $gallery_ids = get_post_meta(get_the_ID(), '_cars_gallery_images', true);
        if (!is_array($gallery_ids)) {
            $gallery_ids = [];
        }

        $gallery_images = array_filter(array_map('wp_get_attachment_url', $gallery_ids));
        if (empty($gallery_images)) {
            return '';
        }

        $post_title = get_the_title(); // Get the current post title

        ob_start();
        ?>
        <div class="car-loop-gallery">
            <div class="car-images-slider">
                <?php foreach ($gallery_images as $index => $image_url): 
                    // Get attachment ID for the image URL
                    $attachment_id = $this->get_attachment_id_by_url($image_url);

                    // Get alt text from attachment
                    $alt_text = $attachment_id ? get_post_meta($attachment_id, '_wp_attachment_image_alt', true) : '';

                    // Fallback to post title + 'image' + count if no alt text found
                    if (empty($alt_text)) {
                        $alt_text = $post_title . ' image ' . ($index + 1); // Post title + 'image' + count
                    }
                ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt_text); ?>">
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    // Helper function to get attachment ID by image URL
    private function get_attachment_id_by_url($url)
    {
        global $wpdb;

        // Remove domain from URL if present
        $parsed_url = wp_parse_url($url);
        $path = $parsed_url['path'];

        // Use LIKE with wildcards to match the file name more flexibly
        $attachment = $wpdb->get_col($wpdb->prepare(
            "SELECT post_id FROM $wpdb->postmeta 
             WHERE meta_key = '_wp_attached_file' 
             AND meta_value LIKE %s", 
            '%' . $wpdb->esc_like(basename($path)) . '%'
        ));

        // If not found, try another method
        if (empty($attachment)) {
            $attachment = $wpdb->get_col($wpdb->prepare(
                "SELECT post_id FROM $wpdb->posts 
                 WHERE post_type = 'attachment' 
                 AND guid LIKE %s", 
                '%' . $wpdb->esc_like(basename($path)) . '%'
            ));
        }

        return !empty($attachment) ? $attachment[0] : false;
    }
}

new Car_Loop_Gallery();