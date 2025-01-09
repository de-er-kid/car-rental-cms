<?php
class Brands_Taxonomy {
    public function __construct() {
        add_action('init', array($this, 'register_brands_taxonomy'));
        add_action('brands_add_form_fields', array($this, 'add_brands_logo_field'));
        add_action('brands_edit_form_fields', array($this, 'edit_brands_logo_field'));
        add_action('edited_brands', array($this, 'save_brands_logo_field'));
        add_action('create_brands', array($this, 'save_brands_logo_field'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
        add_shortcode('brand_logo', array($this, 'display_brand_logo'));
		add_filter('term_link', array($this, 'modify_brand_link'), 10, 3);
    }

    public function register_brands_taxonomy() {
        $labels = array(
            'name' => __('Brands', 'car-rental-cmc'),
            'singular_name' => __('Brand', 'car-rental-cmc'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
        );

        register_taxonomy('brands', 'cars', $args);
    }

    public function enqueue_media_uploader($hook) {
        // Enqueue WordPress media uploader scripts
        if (in_array($hook, array('edit-tags.php', 'term.php'))) {
            wp_enqueue_media();
            wp_localize_script('brands-logo-upload', 'brandsLogoUploader', array(
                'title' => __('Select or Upload Brand Logo', 'car-rental-cmc'),
                'button' => __('Use this logo', 'car-rental-cmc')
            ));
        }
    }

    public function add_brands_logo_field($taxonomy) {
        wp_nonce_field('brands_logo_nonce', 'brands_logo_nonce');
        ?>
        <div class="form-field term-logo-wrap">
            <label for="brands_logo"><?php _e('Brand Logo', 'car-rental-cmc'); ?></label>
            <input type="hidden" name="brands_logo" id="brands_logo" value="" />
            <div id="brands_logo_preview" style="max-width: 300px; margin-top: 10px;"></div>
            <button type="button" class="button" id="upload_brands_logo_button">
                <?php _e('Upload Logo', 'car-rental-cmc'); ?>
            </button>
            <button type="button" class="button" id="remove_brands_logo_button" style="display:none;">
                <?php _e('Remove Logo', 'car-rental-cmc'); ?>
            </button>
            <p class="description"><?php _e('Upload a logo for this brand.', 'car-rental-cmc'); ?></p>
        </div>
        <?php
    }

    public function edit_brands_logo_field($term) {
        wp_nonce_field('brands_logo_nonce', 'brands_logo_nonce');
        $logo_id = get_term_meta($term->term_id, 'brands_logo', true);
        $logo_url = $logo_id ? wp_get_attachment_image_src($logo_id, 'medium') : false;
        ?>
        <tr class="form-field term-logo-wrap">
            <th scope="row"><label for="brands_logo"><?php _e('Brand Logo', 'car-rental-cmc'); ?></label></th>
            <td>
                <input type="hidden" name="brands_logo" id="brands_logo" value="<?php echo esc_attr($logo_id); ?>" />
                <div id="brands_logo_preview" style="max-width: 300px; margin-top: 10px;">
                    <?php if ($logo_url) : ?>
                        <img src="<?php echo esc_url($logo_url[0]); ?>" style="max-width: 100%;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="upload_brands_logo_button">
                    <?php _e('Upload Logo', 'car-rental-cmc'); ?>
                </button>
                <button type="button" class="button" id="remove_brands_logo_button" style="display:<?php echo $logo_url ? 'inline-block' : 'none'; ?>;">
                    <?php _e('Remove Logo', 'car-rental-cmc'); ?>
                </button>
                <p class="description"><?php _e('Upload or change the logo for this brand.', 'car-rental-cmc'); ?></p>
            </td>
        </tr>
        <?php
    }

    public function save_brands_logo_field($term_id) {
        // Check nonce for security
        if (!isset($_POST['brands_logo_nonce']) || !wp_verify_nonce($_POST['brands_logo_nonce'], 'brands_logo_nonce')) {
            return;
        }
    
        // Check user capabilities
        if (!current_user_can('manage_categories')) {
            return;
        }
    
        // Sanitize and save logo
        if (isset($_POST['brands_logo'])) {
            $logo_id = intval($_POST['brands_logo']);
            
            if ($logo_id > 0) {
                update_term_meta($term_id, 'brands_logo', $logo_id);
            } else {
                delete_term_meta($term_id, 'brands_logo');
            }
        }
    }

    public function display_brand_logo($atts) {
        // Define default attributes
        $atts = shortcode_atts(
            array(
                'id' => '', // Term ID for brand
            ), 
            $atts, 
            'brand_logo'
        );
    
        // Placeholder image URL
        $placeholder_url = 'https://via.placeholder.com/300x300?text=No+Logo'; // You can change this to your desired placeholder image URL.
    
        // Check if ID is provided
        if (empty($atts['id'])) {
            return 'Please provide a valid Brand ID.';
        }
    
        // Fetch the Brand Logo
        $logo_id = get_term_meta($atts['id'], 'brands_logo', true);
        if ($logo_id) {
            $logo_url = wp_get_attachment_image_src($logo_id, 'full');
            if ($logo_url) {
                return '<div class="brand-logo"><img src="' . esc_url($logo_url[0]) . '" alt="Brand Logo" /></div>';
            }
        }
    
        // If no logo found, show placeholder with .2 opacity
        return '<div class="brand-logo" style="opacity: 0.2;"><img src="' . esc_url($placeholder_url) . '" alt="No Logo" /></div>';
    }
    
    public function modify_brand_link($url, $term, $taxonomy) {
		if ($taxonomy === 'brands') {
			$url = home_url('car-list/?car_brand=' . $term->slug);
		}
		return $url;
	}
    
    
}
