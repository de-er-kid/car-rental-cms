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

        ob_start();
        ?>
        <div class="car-loop-gallery">
            <div class="car-images-slider">
                <?php foreach ($gallery_images as $index => $image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="Car <?php echo esc_attr($index + 1); ?>">
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new Car_Loop_Gallery();
