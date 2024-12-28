<?php

class Car_FAQ_Shortcode {
    public function __construct() {
        add_shortcode("car_faq", array( $this,"render_car_faq") );
    }

    public function render_car_faq()
    {
        $faqs = get_post_meta(get_the_ID(), '_car_faqs', true);
    
        $output = '<div class="car-faq-section">';
    
        if (!empty($faqs)) {
            foreach ($faqs as $faq) {
                $output .= '<div class="faq-item">';
                $output .= '<h3>' . esc_html($faq['question']) . '</h3>';
                $output .= '<div class="faq-content">' . wpautop($faq['answer']) . '</div>';
                $output .= '</div>';
            }
        }
    
        $output .= '</div>';
    
        return $output;
    }
}

new Car_FAQ_Shortcode();