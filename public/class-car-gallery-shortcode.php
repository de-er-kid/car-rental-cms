<?php
/**
 * Class Car_Gallery_Shortcode
 *
 * Generates a gallery carousel for car listings with Flickity and Fancybox.
 */
class Car_Gallery_Shortcode
{
    /**
     * Initializes the shortcode.
     */
    public static function init()
    {
        add_shortcode('car_gallery', [self::class, 'render_gallery']);
    }

    /**
     * Retrieves gallery images with their alt texts.
     *
     * @return array
     */
    public static function get_gallery_images()
    {
        $gallery_ids = get_post_meta(get_the_ID(), '_cars_gallery_images', true);
        if (!is_array($gallery_ids)) {
            $gallery_ids = [];
        }

        $gallery_images = [];
        $image_count = 1;
        $post_title = get_the_title();

        foreach ($gallery_ids as $attachment_id) {
            $image_url = wp_get_attachment_url($attachment_id);
            if ($image_url) {
                $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                $gallery_images[] = [
                    'url' => $image_url,
                    'alt' => $alt_text ?: $post_title . ' Image ' . $image_count
                ];
                $image_count++;
            }
        }

        return $gallery_images;
    }

    /**
     * Renders the gallery HTML with Flickity carousel and Fancybox lightbox.
     *
     * @return string
     */
    public static function render_gallery()
    {
        $gallery_images = self::get_gallery_images();
        if (empty($gallery_images)) {
            return '';
        }

        ob_start();
        ?>
        <div class="container">
            <!-- Flickity HTML init -->
            <div class="carousel carousel-main" data-flickity='{"pageDots": false, "wrapAround": true}'>
                <?php foreach ($gallery_images as $index => $image): ?>
                    <div class="carousel-cell">
                        <div class="image-wrapper">
                            <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="gallery" data-caption="<?php echo esc_attr($image['alt']); ?>">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="carousel carousel-nav"
                data-flickity='{"asNavFor": ".carousel-main", "contain": true, "pageDots": false}'>
                <?php foreach ($gallery_images as $image): ?>
                    <div class="carousel-cell">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <style>
            .carousel-main .image-wrapper {
                width: 100%;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .carousel-main .image-wrapper img {
                max-width: 100%;
                height: auto;
            }
        </style>
        <script>
            jQuery(document).ready(function($) {
                $('[data-fancybox="gallery"]').fancybox({
                    loop: true,
                    buttons: [
                        "zoom",
                        "close"
                    ]
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
Car_Gallery_Shortcode::init();