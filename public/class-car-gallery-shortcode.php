<?php

/**
 * Class Car_Gallery_Shortcode
 *
 * Generates a gallery carousel for the single-houseboat template.
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
     * Renders the gallery HTML with Flickity carousel and Fancybox lightbox.
     *
     * @return string
     */
    public static function render_gallery()
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
        <div class="container">
            <!-- Flickity HTML init -->
            <div class="carousel carousel-main" data-flickity='{"pageDots": false, "wrapAround": true}'>
                <?php foreach ($gallery_images as $index => $image_url): ?>
                    <div class="carousel-cell">
                        <!-- Set an overlay wrapper to avoid breaking Flickity layout -->
                        <div class="image-wrapper">
                            <a href="<?php echo esc_url($image_url); ?>" data-fancybox="gallery" data-caption="Image <?php echo esc_attr($index + 1); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="Gallery Image <?php echo esc_attr($index + 1); ?>">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="carousel carousel-nav"
                data-flickity='{"asNavFor": ".carousel-main", "contain": true, "pageDots": false}'>
                <?php foreach ($gallery_images as $image_url): ?>
                    <div class="carousel-cell">
                        <img src="<?php echo esc_url($image_url); ?>" alt="">
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
