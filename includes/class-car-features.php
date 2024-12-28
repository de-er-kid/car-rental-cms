<?php
class Car_Features_Taxonomy {
    public function __construct() {
        add_action('init', array($this, 'register_brands_taxonomy')); 
    }

    public function register_brands_taxonomy() {
        $labels = array(
            'name' => __('Features', 'car-rental-cmc'),
            'singular_name' => __('Feature', 'car-rental-cmc'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
        );

        register_taxonomy('features', 'cars', $args);
    }
    
}
