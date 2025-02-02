<?php
class Taxonomy_Loop_Shortcode {
    public function __construct() {
        add_shortcode('taxonomy_loop', array($this, 'render_taxonomy_loop'));
    }

    public function render_taxonomy_loop($atts) {
        $atts = shortcode_atts(
            array(
                'taxonomy' => 'category',
            ), 
            $atts, 
            'taxonomy_loop'
        );

        $taxonomy = $atts['taxonomy'];

        $args = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        );

        // Exclude the default category if applicable
        $default_category = get_option('default_category');
        if ($taxonomy === 'category' && !empty($default_category)) {
            $args['exclude'] = array($default_category);
        }

        // Ordering logic
        if (in_array($taxonomy, array('category', 'brands'))) {
            $args['meta_key'] = 'term_order';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
        } else {
            $args['orderby'] = 'id';
            $args['order'] = 'ASC';
        }

        $terms = get_terms($args);

        if (empty($terms) || is_wp_error($terms)) {
            return '<p>No terms found.</p>';
        }

        $output = '<div class="taxonomy-loop">';

        foreach ($terms as $term) {
            // Generalize meta key for image (supports more taxonomies if needed)
            $meta_key = ($taxonomy === 'brands') ? 'brands_logo' : 'category_image';
            $image_id = get_term_meta($term->term_id, $meta_key, true);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : esc_url('https://via.placeholder.com/300x300?text=No+Image');

            $term_link = get_term_link($term);

            $output .= '<div class="taxonomy-term">';
            $output .= '<a href="' . esc_url($term_link) . '" class="taxonomy-term-link">';
            $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term->name) . '" class="taxonomy-term-image" />';
            $output .= '<h3 class="taxonomy-term-title">' . esc_html($term->name) . '</h3>';
            $output .= '</a>';
            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }
}

new Taxonomy_Loop_Shortcode();
