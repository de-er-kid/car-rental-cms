<?php

/**
 * Plugin Name: Car Rental CMS
 * Description: Car rental content management system for WordPress.
 * Version: 1.0
 * Author: Sinan
 * Text Domain: car-rental-cmc
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CAR_RENTAL_CMS_PATH', plugin_dir_path(__FILE__));
define('CAR_RENTAL_CMS_URL', plugin_dir_url(__FILE__));

/**
 * Include required files for the plugin modules.
 */
require_once CAR_RENTAL_CMS_PATH . 'includes/class-car-rental.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-settings-page.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-brands-taxonomy.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-category-taxonomy.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-car-features.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-cars-meta-fields.php';
require_once CAR_RENTAL_CMS_PATH . 'includes/class-car-gallery-metabox.php';

/**
 * Include shortcode files for frontend
 */
require_once CAR_RENTAL_CMS_PATH . 'public/class-car-gallery-shortcode.php';
require_once CAR_RENTAL_CMS_PATH . 'public/class-car-features-shortcode.php';
require_once CAR_RENTAL_CMS_PATH . 'public/class-cars-filters-shortcode.php';

/**
 * Initialize the Car Rental CMS.
 */
function car_rental_cms_init()
{
    $car_rental = new Car_Rental();
    $car_rental->init();

    // Initialize other modules
    new Settings_Page();
    new Brands_Taxonomy();
    new Category_Taxonomy();
    new Cars_Meta_Fields();
    new Car_Gallery_Metabox();
    new Car_Features_Taxonomy();
}
add_action('plugins_loaded', 'car_rental_cms_init');

function enqueue_admin_assets()
{
    wp_enqueue_media();
    wp_enqueue_script('car-rental-cms-js', plugin_dir_url(__FILE__) . 'assets/js/media-upload.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_admin_assets');

function enqueue_frontend_assets()
{

    if (is_singular('cars')) {
        wp_enqueue_script('flickity', CAR_RENTAL_CMS_URL . 'assets/js/flickity.pkgd.min.js', array(), null, true);
        wp_enqueue_style('flickity-style', CAR_RENTAL_CMS_URL . 'assets/css/flickity.min.css');
        // wp_enqueue_style('houseboat-styles', CAR_RENTAL_CMS_URL . 'assets/css/single-houseboat.css');

        wp_enqueue_script('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', ['jquery'], null, true);
        wp_enqueue_style('fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');
    }

    // Splide
    wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css', [], '4.0.0');
    wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js', [], '4.0.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_frontend_assets');
