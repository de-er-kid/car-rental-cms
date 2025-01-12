<?php
class Term_Order_Metabox {
    public function __construct() {
        add_action('init', array($this, 'register_term_order_metafield'));
        add_action('edited_term', array($this, 'save_term_order_metafield'), 10, 1);  // Changed to 1 parameter
        add_action('create_term', array($this, 'save_term_order_metafield'), 10, 1);  // Changed to 1 parameter
        
        // Add hooks for displaying the field in different forms
        add_action('category_add_form_fields', array($this, 'add_term_order_field'));
        add_action('category_edit_form_fields', array($this, 'edit_term_order_field'));
        
        // Quick edit hooks
        add_action('manage_category_custom_column', array($this, 'add_term_order_column_content'), 10, 3);
        add_action('manage_edit-category_columns', array($this, 'add_term_order_column'));
        add_action('quick_edit_custom_box', array($this, 'quick_edit_term_order_field'), 10, 3);
        
        // Add script for quick edit
        add_action('admin_footer', array($this, 'quick_edit_javascript'));
    }

    public function register_term_order_metafield() {
        register_meta('term', 'term_order', array(
            'type' => 'number',
            'description' => 'Order of the term',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'absint',  // Changed from intval to absint
            'auth_callback' => function() {
                return current_user_can('manage_categories');
            }
        ));
    }

    public function save_term_order_metafield($term_id) {
        if (isset($_POST['term_order'])) {
            $order = absint($_POST['term_order']);  // Using absint for sanitization
            update_term_meta($term_id, 'term_order', $order);
        }
    }

    // Add term field in Add Category form
    public function add_term_order_field() {
        ?>
        <div class="form-field term-order-wrap">
            <label for="term_order"><?php _e('Order'); ?></label>
            <input type="number" name="term_order" id="term_order" value="0" />
            <p class="description"><?php _e('Enter the order number (0 = default)'); ?></p>
        </div>
        <?php
    }

    // Add term field in Edit Category form
    public function edit_term_order_field($term) {
        $term_order = get_term_meta($term->term_id, 'term_order', true);
        $term_order = !empty($term_order) ? absint($term_order) : 0;
        ?>
        <tr class="form-field term-order-wrap">
            <th scope="row"><label for="term_order"><?php _e('Order'); ?></label></th>
            <td>
                <input type="number" name="term_order" id="term_order" value="<?php echo esc_attr($term_order); ?>" />
                <p class="description"><?php _e('Enter the order number (0 = default)'); ?></p>
            </td>
        </tr>
        <?php
    }

    // Add custom column to category table
    public function add_term_order_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'name') {
                $new_columns['term_order'] = __('Order');
            }
        }
        return $new_columns;
    }

    // Add content to custom column
    public function add_term_order_column_content($content, $column_name, $term_id) {
        if ($column_name === 'term_order') {
            $term_order = get_term_meta($term_id, 'term_order', true);
            $content = '<div class="term-order-value">' . (empty($term_order) ? '0' : esc_html($term_order)) . '</div>';
        }
        return $content;
    }

    // Add term field to Quick Edit
    public function quick_edit_term_order_field($column_name, $screen, $name) {
        if ($column_name !== 'term_order') return;
        ?>
        <fieldset>
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e('Order'); ?></span>
                    <span class="input-text-wrap">
                        <input type="number" name="term_order" class="ptitle" value="" />
                    </span>
                </label>
            </div>
        </fieldset>
        <?php
    }

    // Add JavaScript for Quick Edit
    public function quick_edit_javascript() {
        $screen = get_current_screen();
        if ($screen->id !== 'edit-category') return;
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $(document).on('click', '.editinline', function() {
                var tag_id = $(this).closest('tr').attr('id');
                var term_id = tag_id.replace('tag-', '');
                var order_value = $('#tag-' + term_id + ' .term-order-value').text();
                
                // Populate the quick edit field
                $('input[name="term_order"]').val(order_value);
            });
        });
        </script>
        <?php
    }

    public function get_terms_ordered($taxonomy) {
        $args = array(
            'taxonomy' => $taxonomy,
            'meta_key' => 'term_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'hide_empty' => false,
        );
        return get_terms($args);
    }
}