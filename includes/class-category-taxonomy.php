<?php

class Category_Taxonomy {

public function __construct() {
    add_action('init', array($this, 'register_category_taxonomy'));
    add_action('category_add_form_fields', array($this, 'add_category_image_field'));
    add_action('category_edit_form_fields', array($this, 'edit_category_image_field'));
    add_action('edited_category', array($this, 'save_category_image_field'));
    add_action('create_category', array($this, 'save_category_image_field'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
    add_shortcode('category_image', array($this, 'display_category_image'));
//     add_filter('term_link', array($this, 'modify_category_link'), 10, 3);
	add_action('pre_get_posts', array($this, 'filter_category_archive_query'));
}

public function register_category_taxonomy() {
    $labels = array(
        'name' => __('Categories', 'car-rental-cmc'),
        'singular_name' => __('Category', 'car-rental-cmc'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'show_in_rest' => true,
    );

    register_taxonomy('category', 'cars', $args);
}

public function enqueue_media_uploader($hook) {
    if (in_array($hook, array('edit-tags.php', 'term.php'))) {
        wp_enqueue_media();
        wp_localize_script('category-image-upload', 'categoryImageUploader', array(
            'title' => __('Select or Upload Category Image', 'car-rental-cmc'),
            'button' => __('Use this image', 'car-rental-cmc')
        ));
    }
}

public function add_category_image_field($taxonomy) {
    wp_nonce_field('category_image_nonce', 'category_image_nonce');
    ?>
    <div class="form-field term-group">
        <label for="category_image"><?php _e('Category Image', 'car-rental-cmc'); ?></label>
        <input type="hidden" name="category_image" id="category_image" value="" />
        <div id="category_image_preview" style="max-width: 300px; margin-top: 10px;"></div>
        <button type="button" class="button" id="upload_category_image_button">
            <?php _e('Upload Image', 'car-rental-cmc'); ?>
        </button>
        <button type="button" class="button" id="remove_category_image_button" style="display:none;">
            <?php _e('Remove Image', 'car-rental-cmc'); ?>
        </button>
        <p class="description"><?php _e('Upload an image for this category.', 'car-rental-cmc'); ?></p>
    </div>
    <?php
}

public function edit_category_image_field($term) {
    wp_nonce_field('category_image_nonce', 'category_image_nonce');
    $image_id = get_term_meta($term->term_id, 'category_image', true);
    $image_url = $image_id ? wp_get_attachment_image_src($image_id, 'medium') : false;
    ?>
    <tr class="form-field term-group">
        <th scope="row"><label for="category_image"><?php _e('Category Image', 'car-rental-cmc'); ?></label></th>
        <td>
            <input type="hidden" name="category_image" id="category_image" value="<?php echo esc_attr($image_id); ?>" />
            <div id="category_image_preview" style="max-width: 300px; margin-top: 10px;">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url[0]); ?>" style="max-width: 100%;" />
                <?php endif; ?>
            </div>
            <button type="button" class="button" id="upload_category_image_button">
                <?php _e('Upload Image', 'car-rental-cmc'); ?>
            </button>
            <button type="button" class="button" id="remove_category_image_button" style="display:<?php echo $image_url ? 'inline-block' : 'none'; ?>;">
                <?php _e('Remove Image', 'car-rental-cmc'); ?>
            </button>
            <p class="description"><?php _e('Upload or change the image for this category.', 'car-rental-cmc'); ?></p>
        </td>
    </tr>
    <?php
}

public function save_category_image_field($term_id) {
    if (!isset($_POST['category_image_nonce']) || !wp_verify_nonce($_POST['category_image_nonce'], 'category_image_nonce')) {
        return;
    }

    if (!current_user_can('manage_categories')) {
        return;
    }

    if (isset($_POST['category_image'])) {
        $image_id = intval($_POST['category_image']);
        
        if ($image_id > 0) {
            update_term_meta($term_id, 'category_image', $image_id);
        } else {
            delete_term_meta($term_id, 'category_image');
        }
    }
}

public function display_category_image($atts) {
    $atts = shortcode_atts(
        array(
            'id' => '',
        ), 
        $atts, 
        'category_image'
    );

    $placeholder_url = 'https://via.placeholder.com/300x300?text=No+Image'; // You can change this to your desired placeholder image URL.

    if (empty($atts['id'])) {
        return 'Please provide a valid Category ID.';
    }

    $category_image_url = get_term_meta($atts['id'], '_category_image', true);
    if ($category_image_url) {
        return '<div class="category-image"><img src="' . esc_url($category_image_url) . '" alt="Category Image" /></div>';
    }

    return '<div class="category-image" style="opacity: 0.2;"><img src="' . esc_url($placeholder_url) . '" alt="No Image" /></div>';
}


public function modify_category_link($url, $term, $taxonomy) {
    if ($taxonomy === 'category') {
        $url = home_url('car-list/?car_category=' . $term->slug);
    }
    return $url;
}
	
public function filter_category_archive_query($query) {
    if (!is_admin() && $query->is_main_query() && is_category()) {
        $query->set('post_type', 'cars');
    }
}


}
