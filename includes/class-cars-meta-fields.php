<?php

class Cars_Meta_Fields
{

    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_car_meta_boxes'));
        add_action('save_post', array($this, 'save_car_meta_fields'));
    }

    public function add_car_meta_boxes()
    {
        add_meta_box(
            'car_details',
            __('Car Details', 'car-rental-cmc'),
            array($this, 'render_car_meta_box'),
            'cars',
            'normal',
            'high'
        );
    }

    public function render_car_meta_box($post)
    {
        $terms = wp_get_post_terms($post->ID, 'brands');
        $car_make = !empty($terms) ? $terms[0]->term_id : '';
        $car_model = get_post_meta($post->ID, '_car_model', true);
        $security_deposit = get_post_meta($post->ID, '_security_deposit', true);
        $daily_rent = get_post_meta($post->ID, '_daily_rent', true);
        $monthly_rent = get_post_meta($post->ID, '_monthly_rent', true);
        $speed = get_post_meta($post->ID, '_speed', true);
        $gear = get_post_meta($post->ID, '_gear', true);
        $seats = get_post_meta($post->ID, '_seats', true);
        $fuel = get_post_meta($post->ID, '_fuel', true);
		$doors = get_post_meta($post->ID, '_doors', true);

        echo '<style>
                #car_details {.inside {
                    display: grid;
                    grid-template-columns: 1fr 1fr; /* 2 columns */
                    grid-template-rows: auto; /* Rows adjust to content */
                    grid-gap: 10px; /* Add space between grid items */
                }

                .inside label {
                    grid-column: span 1; /* Labels occupy 1 column */
                }

                .inside input {
                    grid-column: span 1; /* Inputs occupy 1 column */
                }

                .inside label:nth-child(odd) {
                    grid-row: auto;
                }

                .inside input:nth-child(even) {
                    grid-row: auto;
                }

                .inside label:nth-child(1) { grid-column: 1; }
                .inside input:nth-child(2) { grid-column: 2; }
                .inside label:nth-child(3) { grid-column: 1; }
                .inside input:nth-child(4) { grid-column: 2; }
                .inside label:nth-child(5) { grid-column: 1; }
                .inside input:nth-child(6) { grid-column: 2; }
                .inside label:nth-child(7) { grid-column: 1; }
                .inside input:nth-child(8) { grid-column: 2; }
                .inside label:nth-child(9) { grid-column: 1; }
                .inside input:nth-child(10) { grid-column: 2; }
                .inside label:nth-child(11) { grid-column: 1; }
                .inside input:nth-child(12) { grid-column: 2; }
                }
            </style>';

        wp_nonce_field('save_car_meta_fields', 'car_meta_nonce');

        $brands = get_terms(array(
            'taxonomy' => 'brands',
            'hide_empty' => false,
        ));
        
        echo '<label for="car_make">' . __('Brand', 'car-rental-cmc') . '</label>';
        echo '<select id="car_make" name="car_make" class="widefat">';
        echo '<option value="">' . __('Select a brand', 'car-rental-cmc') . '</option>';
        
        foreach ($brands as $brand) {
            echo '<option value="' . esc_attr($brand->term_id) . '" ' . selected($car_make, $brand->term_id, false) . '>';
            echo esc_html($brand->name);
            echo '</option>';
        }
        
        echo '</select>';

        echo '<label for="car_model">' . __('Model', 'car-rental-cmc') . '</label>';
        echo '<input type="text" id="car_model" name="car_model" value="' . esc_attr($car_model) . '" class="widefat" />';

        
        echo '<label for="security_deposit">' . __('Security Deposit', 'car-rental-cmc') . '</label>';
        echo '<input type="number" id="security_deposit" name="security_deposit" value="' . esc_attr($security_deposit) . '" class="widefat" />';
        

        
        echo '<label for="daily_rent">' . __('Daily Rent', 'car-rental-cmc') . '</label>';
        echo '<input type="number" id="daily_rent" name="daily_rent" value="' . esc_attr($daily_rent) . '" class="widefat" />';
        

        
        echo '<label for="monthly_rent">' . __('Monthly Rent', 'car-rental-cmc') . '</label>';
        echo '<input type="number" id="monthly_rent" name="monthly_rent" value="' . esc_attr($monthly_rent) . '" class="widefat" />';
        

        
        echo '<label for="speed">' . __('Speed', 'car-rental-cmc') . '</label>';
        echo '<input type="text" id="speed" name="speed" value="' . esc_attr($speed) . '" class="widefat" />';
        

        
        echo '<label for="gear">' . __('Gear', 'car-rental-cmc') . '</label>';
        echo '<input type="text" id="gear" name="gear" value="' . esc_attr($gear) . '" class="widefat" />';
        

        
        echo '<label for="seats">' . __('Seats', 'car-rental-cmc') . '</label>';
        echo '<input type="text" id="seats" name="seats" value="' . esc_attr($seats) . '" class="widefat" />';
        

        
        echo '<label for="fuel">' . __('Fuel', 'car-rental-cmc') . '</label>';
        echo '<input type="text" id="fuel" name="fuel" value="' . esc_attr($fuel) . '" class="widefat" />';
		
		echo '<label for="doors">' . __('Doors', 'car-rental-cmc') . '</label>';
        echo '<input type="number" id="doors" name="doors" value="' . esc_attr($doors) . '" class="widefat" />';
        
    }

    public function save_car_meta_fields($post_id)
    {
        if (!isset($_POST['car_meta_nonce']) || !wp_verify_nonce($_POST['car_meta_nonce'], 'save_car_meta_fields')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['car_make']) && !empty($_POST['car_make'])) {
            $brand_term_id = absint($_POST['car_make']);
            wp_set_object_terms($post_id, $brand_term_id, 'brands');
        }
        if (isset($_POST['car_model'])) {
            update_post_meta($post_id, '_car_model', sanitize_text_field($_POST['car_model']));
        }
        if (isset($_POST['security_deposit'])) {
            update_post_meta($post_id, '_security_deposit', floatval($_POST['security_deposit']));
        }
        if (isset($_POST['daily_rent'])) {
            update_post_meta($post_id, '_daily_rent', floatval($_POST['daily_rent']));
        }
        if (isset($_POST['monthly_rent'])) {
            update_post_meta($post_id, '_monthly_rent', floatval($_POST['monthly_rent']));
        }
        if (isset($_POST['speed'])) {
            update_post_meta($post_id, '_speed', sanitize_text_field($_POST['speed']));
        }
        if (isset($_POST['gear'])) {
            update_post_meta($post_id, '_gear', sanitize_text_field($_POST['gear']));
        }
        if (isset($_POST['seats'])) {
            update_post_meta($post_id, '_seats', sanitize_text_field($_POST['seats']));
        }
        if (isset($_POST['fuel'])) {
            update_post_meta($post_id, '_fuel', sanitize_text_field($_POST['fuel']));
        }
		if (isset($_POST['doors'])) {
            update_post_meta($post_id, '_doors', floatval($_POST['doors']));
        }
    }
}
