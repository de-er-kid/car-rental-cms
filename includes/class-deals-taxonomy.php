<?php
class Deals_Taxonomy {
    public function __construct() {
        add_action('init', array($this, 'register_deals_taxonomy'));
        add_action('deals_add_form_fields', array($this, 'color_meta_field'), 10, 2);
        add_action('created_deals', array($this, 'save_color_meta_field'), 10, 2);
        add_action('deals_edit_form_fields', array($this, 'edit_color_meta_field'), 10, 2);
        add_action('edited_deals', array($this, 'update_color_meta_field'), 10, 2);
        
        // Add scripts and styles for admin
        add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker'));
    }

    public function register_deals_taxonomy() {
        $labels = array(
            'name' => __('Deals', 'car-rental-cmc'),
            'singular_name' => __('Deal', 'car-rental-cmc'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'show_in_rest' => true,
        );

        register_taxonomy('deals', 'cars', $args);
    }

    public function enqueue_color_picker() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        // wp_enqueue_script(
        //     'deals-color-picker',
        //     plugins_url('js/deals-color-picker.js', __FILE__),
        //     array('wp-color-picker'),
        //     false,
        //     true
        // );
    }

    public function color_meta_field($taxonomy) {
        ?>
        <div class="form-field term-color-wrap">
            <label for="term-color"><?php _e('Color', 'car-rental-cmc'); ?></label>
            <input type="text" name="term-color" id="term-color" value="#000000" class="deals-color-picker" />
            <p><?php _e('Select a color for this deal.', 'car-rental-cmc'); ?></p>
        </div>
        <?php
    }

    public function save_color_meta_field($term_id) {
        if (isset($_POST['term-color']) && '' !== $_POST['term-color']) {
            $color = sanitize_hex_color($_POST['term-color']);
            add_term_meta($term_id, 'term-color', $color, true);
        }
    }

    public function edit_color_meta_field($term, $taxonomy) {
        $color = get_term_meta($term->term_id, 'term-color', true);
        ?>
        <tr class="form-field term-color-wrap">
            <th scope="row"><label for="term-color"><?php _e('Color', 'car-rental-cmc'); ?></label></th>
            <td>
                <input type="text" name="term-color" id="term-color" 
                    value="<?php echo esc_attr($color) ? esc_attr($color) : '#000000'; ?>" 
                    class="deals-color-picker" />
                <p class="description"><?php _e('Select a color for this deal.', 'car-rental-cmc'); ?></p>
            </td>
        </tr>
        <?php
    }

    public function update_color_meta_field($term_id) {
        if (isset($_POST['term-color']) && '' !== $_POST['term-color']) {
            $color = sanitize_hex_color($_POST['term-color']);
            update_term_meta($term_id, 'term-color', $color);
        } else {
            delete_term_meta($term_id, 'term-color');
        }
    }
}