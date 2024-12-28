<?php

class CarFiltersShortcode {
    public function __construct() {
        add_shortcode('car_filters', [$this, 'render_filters']);
        add_action('pre_get_posts', [$this, 'filter_cars_archive']);
    }

    public function render_filters($atts) {
        $current_category = isset($_GET['car_category']) ? sanitize_text_field($_GET['car_category']) : '';
        $current_brand = isset($_GET['car_brand']) ? sanitize_text_field($_GET['car_brand']) : '';

        ob_start();
        ?>

        <form method="GET" class="car-filters-form" action="">
            <h3>Filter by</h3>
            <div class="filter-group">
                <!-- <label for="car_category">Car Category</label> -->
                <select name="car_category" id="car_category">
                    <option value="">All Categories</option>
                    <?php
                    $categories = get_terms([
                        'taxonomy' => 'category',
                        'hide_empty' => false,
                    ]);

                    if (!is_wp_error($categories)) {
                        foreach ($categories as $category) {
                            echo '<option value="' . esc_attr($category->slug) . '" ' . selected($current_category, $category->slug, false) . '>' . esc_html($category->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="filter-group">
                <!-- <label for="car_brand">Car Brand</label> -->
                <select name="car_brand" id="car_brand">
                    <option value="">All Brands</option>
                    <?php
                    $brands = get_terms([
                        'taxonomy' => 'brands',
                        'hide_empty' => false,
                    ]);

                    if (!is_wp_error($brands)) {
                        foreach ($brands as $brand) {
                            echo '<option value="' . esc_attr($brand->slug) . '" ' . selected($current_brand, $brand->slug, false) . '>' . esc_html($brand->name) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="apply-filter">Apply Filter</button>
            <a href="<?php echo esc_url(get_post_type_archive_link('cars')); ?>" class="clear-filter">Clear Filter</a>
        </form>

        <?php
        return ob_get_clean();
    }

    public function filter_cars_archive($query) {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('cars')) {
            $tax_query = [];

            if (!empty($_GET['car_category'])) {
                $tax_query[] = [
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['car_category']),
                ];
            }

            if (!empty($_GET['car_brand'])) {
                $tax_query[] = [
                    'taxonomy' => 'brands',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['car_brand']),
                ];
            }

            if (!empty($tax_query)) {
                $query->set('tax_query', $tax_query);
            }
        }
    }
}

new CarFiltersShortcode();
