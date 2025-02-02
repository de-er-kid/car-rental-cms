<?php
/**
 * Plugin Name: Car Rental CMS
 * Description: Car rental content management system for WordPress.
 * Version: 2.0
 * Author: Sinan
 * Author URI: https://github.com/de-er-kid/
 * Text Domain: car-rental-cmc
 */

if (!defined('ABSPATH'))
    exit;

// Define paths
define('CAR_RENTAL_CMS_PATH', plugin_dir_path(__FILE__));
define('CAR_RENTAL_CMS_URL', plugin_dir_url(__FILE__));

// Autoload required classes
foreach (glob(CAR_RENTAL_CMS_PATH . 'includes/*.php') as $file) {
    require_once $file;
}
foreach (glob(CAR_RENTAL_CMS_PATH . 'public/*.php') as $file) {
    require_once $file;
}

// Initialize Plugin
function car_rental_cms_init()
{
    (new Car_Rental())->init();
    new Settings_Page();
    new Brands_Taxonomy();
    new Category_Taxonomy();
    new Cars_Meta_Fields();
    new Car_Gallery_Metabox();
    new Car_Features_Taxonomy();
    new Car_FAQ_Metabox();
    new Car_Benifits_Metabox();
    new Cars_Why_Chose_Meta();
    new Car_Subheading_Metabox();
    new Deals_Taxonomy();
    new Term_Order_Metabox();
}
add_action('plugins_loaded', 'car_rental_cms_init');

// Enqueue Admin Assets
function enqueue_admin_assets()
{
    wp_enqueue_media();
    wp_enqueue_script('car-rental-cms-js', CAR_RENTAL_CMS_URL . 'assets/js/media-upload.js', ['jquery'], null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_admin_assets');

// Enqueue Frontend Assets
function enqueue_frontend_assets()
{
    if (is_singular('cars')) {
        wp_enqueue_script('flickity', CAR_RENTAL_CMS_URL . 'assets/js/flickity.pkgd.min.js', [], null, true);
        wp_enqueue_style('flickity-style', CAR_RENTAL_CMS_URL . 'assets/css/flickity.min.css');

        wp_enqueue_script('fancybox', CAR_RENTAL_CMS_URL . 'assets/vendor/@fancyapps/fancybox/dist/fancybox.umd.js', ['jquery'], null, true);
        wp_enqueue_style('fancybox', CAR_RENTAL_CMS_URL . 'assets/vendor/@fancyapps/fancybox/dist/fancybox.css');


    }

    wp_enqueue_style('tiny-slider-css', CAR_RENTAL_CMS_URL . 'assets/vendor/tiny-slider.css', [], '2.9.4');
    wp_enqueue_script('tiny-slider-js', CAR_RENTAL_CMS_URL . 'assets/vendor/tiny-slider.js', [], '2.9.4', true);
    wp_enqueue_script('car-rental-cms-frontend-js', CAR_RENTAL_CMS_URL . 'assets/js/frontend.js', ['jquery'], null, true);

    wp_enqueue_style('intl-tel-input-css', CAR_RENTAL_CMS_URL . 'assets/vendor/intlTelInput.min.css', [], null);
    wp_enqueue_script('intl-tel-input-js', CAR_RENTAL_CMS_URL . 'assets/vendor/intlTelInput.min.js', ['jquery'], null, true);
    wp_enqueue_script('intl-tel-input-init-js', CAR_RENTAL_CMS_URL . 'assets/js/intl-tel-input-init.js', ['intl-tel-input-js'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_frontend_assets');

// Hide Car Info for Specific Category
function hide_car_info_for_specific_category()
{
    if (is_singular('cars') && has_term(43, 'category')) {
        echo '<style>.car-info { display: none; }</style>';
    }
}
add_action('wp_head', 'hide_car_info_for_specific_category');
