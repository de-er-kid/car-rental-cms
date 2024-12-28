<?php
class Car_Features_List_Shortcode
{
    public static function init()
    {
        add_shortcode('car_features_list', [self::class, 'render_car_features_list']); // Register shortcode
    }

    public static function render_car_features_list($atts)
    {
        $post_id = get_the_ID();

        $terms = get_the_terms($post_id, 'features');

        if ($terms && !is_wp_error($terms)) {
            $term_list = '<ul class="car-features-list">';

            foreach ($terms as $term) {
                $term_list .= '<li class="car-feature-item">
                    <span class="feature-icon"><svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.5825 0.300888C11.0786 -0.145383 10.317 -0.0888233 9.88142 0.427953L3.60312 7.87365L2.11852 6.11338C1.68305 5.5966 0.921205 5.53993 0.417481 5.98621C-0.0864583 6.43259 -0.141971 7.21301 0.293495 7.72945L2.6905 10.5722C2.91957 10.8438 3.25265 11 3.60312 11C3.95348 11 4.28656 10.8438 4.51564 10.5722L11.7066 2.04414C12.142 1.52758 12.0864 0.74716 11.5825 0.300888Z" fill="#7BCC3D"/></svg></span> ' . esc_html($term->name) . '
                </li>';
            }

            $term_list .= '</ul>';
            return $term_list;
        }

        return '<p></p>';
    }
}

Car_Features_List_Shortcode::init();
