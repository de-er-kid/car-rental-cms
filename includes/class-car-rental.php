<?php

class Car_Rental {

    // public function __construct() {
    //     // Constructor logic if needed
	// 	add_shortcode('taxonomy_loop', array($this, 'taxonomy_loop_shortcode'));
    // }

    public function init() {
        add_action('init', array($this, 'register_cars_post_type'));
    }

    public function register_cars_post_type() {
        register_post_type( 'cars', array(
			'label'                 => __( 'Car', 'car-rental-cmc' ),
			'description'           => __( 'Car Description', 'car-rental-cmc' ),
			'labels'                => array(
				'name'                  => _x( 'Cars', 'Post Type General Name', 'car-rental-cmc' ),
				'singular_name'         => _x( 'Car', 'Post Type Singular Name', 'car-rental-cmc' ),
				'menu_name'             => __( 'Cars', 'car-rental-cmc' ),
				'name_admin_bar'        => __( 'Car', 'car-rental-cmc' ),
				'archives'              => __( 'Car Archives', 'car-rental-cmc' ),
				'attributes'            => __( 'Item Attributes', 'car-rental-cmc' ),
				'parent_item_colon'     => __( 'Parent Item:', 'car-rental-cmc' ),
				'all_items'             => __( 'All Items', 'car-rental-cmc' ),
				'add_new_item'          => __( 'Add New Item', 'car-rental-cmc' ),
				'add_new'               => __( 'Add New', 'car-rental-cmc' ),
				'new_item'              => __( 'New Item', 'car-rental-cmc' ),
				'edit_item'             => __( 'Edit Item', 'car-rental-cmc' ),
				'update_item'           => __( 'Update Item', 'car-rental-cmc' ),
				'view_item'             => __( 'View Item', 'car-rental-cmc' ),
				'view_items'            => __( 'View Items', 'car-rental-cmc' ),
				'search_items'          => __( 'Search Item', 'car-rental-cmc' ),
				'not_found'             => __( 'Not found', 'car-rental-cmc' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'car-rental-cmc' ),
				'featured_image'        => __( 'Featured Image', 'car-rental-cmc' ),
				'set_featured_image'    => __( 'Set featured image', 'car-rental-cmc' ),
				'remove_featured_image' => __( 'Remove featured image', 'car-rental-cmc' ),
				'use_featured_image'    => __( 'Use as featured image', 'car-rental-cmc' ),
				'insert_into_item'      => __( 'Insert into item', 'car-rental-cmc' ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', 'car-rental-cmc' ),
				'items_list'            => __( 'Items list', 'car-rental-cmc' ),
				'items_list_navigation' => __( 'Items list navigation', 'car-rental-cmc' ),
				'filter_items_list'     => __( 'Filter items list', 'car-rental-cmc' ),
			),
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'            => array( 'brands', 'category' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-car',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => 'car-list',
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => false,
		));	

        // $this->register_taxonomies();
    }

    // private function register_taxonomies() {
    //     new Brands_Taxonomy();

    //     new Category_Taxonomy();
    // }

	// public function taxonomy_loop_shortcode($atts) {
	// 	// Define default attributes
	// 	$atts = shortcode_atts(
	// 		array(
	// 			'taxonomy' => 'category', // Default taxonomy
	// 		), 
	// 		$atts, 
	// 		'taxonomy_loop'
	// 	);
	
	// 	// Get the taxonomy from the attributes
	// 	$taxonomy = $atts['taxonomy'];
	
	// 	// Fetch all terms from the specified taxonomy
	// 	$terms = get_terms(array(
	// 		'taxonomy' => $taxonomy,
	// 		'hide_empty' => false, // Show all terms, even empty ones
	// 		'exclude' => array(get_option('default_category')), // Exclude 'Uncategorized' (usually ID 1 for category)
	// 		'orderby' => 'id',
	// 		'order' => 'ASC',
	// 	));
	
	// 	// Check if there are any terms
	// 	if (empty($terms) || is_wp_error($terms)) {
	// 		return 'No terms found.';
	// 	}
	
	// 	// Start the output string
	// 	$output = '<div class="taxonomy-loop">';
	
	// 	// Loop through each term
	// 	foreach ($terms as $term) {
	// 		// Get the image URL (brand logo or category image)
	// 		$image_url = '';
	// 		if ($taxonomy === 'brands') {
	// 			$image_id = get_term_meta($term->term_id, 'brands_logo', true);
	// 			if ($image_id) {
	// 				$image_url = wp_get_attachment_image_url($image_id, 'full');
	// 			}
	// 		} else if ($taxonomy === 'category') {
	// 			$image_id = get_term_meta($term->term_id, 'category_image', true);
	// 			if ($image_id) {
	// 				$image_url = wp_get_attachment_image_url($image_id, 'full');
	// 			}
	// 		}
	
	// 		// Set the default placeholder if no image exists
	// 		if (!$image_url) {
	// 			$image_url = 'https://via.placeholder.com/300x300?text=No+Image';
	// 		}
	
	// 		// Construct the term's link
	// 		$term_link = get_term_link($term);
	
	// 		// Add each term's content to the output
	// 		$output .= '<div class="taxonomy-term">';
	// 		$output .= '<a href="' . esc_url($term_link) . '" class="taxonomy-term-link">';
	// 		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term->name) . '" class="taxonomy-term-image" />';
	// 		// $output .= ($taxonomy === 'category') ? '<h3 class="taxonomy-term-title">' . esc_html($term->name) . '</h3>' : '';
	// 		// now customer needs title for both category and brands
	// 		$output .= '<h3 class="taxonomy-term-title">' . esc_html($term->name) . '</h3>';
	// 		$output .= '</a>';
	// 		$output .= '</div>';
	// 	}
	
	// 	// Close the output string
	// 	$output .= '</div>';
	
	// 	return $output;
	// }
	
	
}

