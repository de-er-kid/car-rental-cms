<?php
class Car_Custom_Image_Shortcode
{
    public function __construct() {
        add_shortcode("custom_image", array( $this,"render_custom_image") );
    }

    public function render_custom_image( $atts ) {
        $atts = shortcode_atts([
            'key' => ''
        ], $atts);

        if (empty($atts['key'])) {
            return '';
        }

        $benefits_image = get_post_meta(get_the_ID(), $atts['key'], true);
        ob_start();
        ?>
        <img class="<?php echo esc_attr($atts['key']); ?>" src="<?php echo esc_url($benefits_image); ?>" />
        <?php
        return ob_get_clean();
    }

}

new Car_Custom_Image_Shortcode();