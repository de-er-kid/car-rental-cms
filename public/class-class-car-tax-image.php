<?php

class Car_Tax_Image
{
    public function __construct()
    {
        add_shortcode('car_tax_image', array($this, 'render_car_tax_image'));
    }

    public function render_car_tax_image($atts)
    {
        $defaults = array(
            'tax' => 'brands',
            'key' => 'brands_logo'
        );
        $atts = shortcode_atts($defaults, $atts);

        $terms = get_the_terms(get_the_ID(), $atts['tax']);
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $image_id = get_term_meta($term->term_id, $atts['key'], true);
                if ($image_id) {
                    $image = wp_get_attachment_image($image_id, 'full');
                    return $image;
                }
            }
        }
        return '';
    }

}

new Car_Tax_Image();