<?php

class Settings_Page {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=cars',
            __('Car Rental Settings', 'car-rental-cmc'),
            __('Settings', 'car-rental-cmc'),
            'manage_options',
            'car_rental_settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Car Rental Settings', 'car-rental-cmc'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('car_rental_settings_group');
                do_settings_sections('car_rental_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('car_rental_settings_group', 'car_rental_settings');

        add_settings_section(
            'car_rental_settings_section',
            __('General Settings', 'car-rental-cmc'),
            null,
            'car_rental_settings'
        );

        add_settings_field(
            'car_rental_whatsapp_number',
            __('WhatsApp Number', 'car-rental-cmc'),
            array($this, 'render_whatsapp_field'),
            'car_rental_settings',
            'car_rental_settings_section'
        );
    }

    public function render_whatsapp_field() {
        $settings = get_option('car_rental_settings');
        ?>
        <input type="tel" name="car_rental_settings[whatsapp_number]" value="<?php echo esc_attr($settings['whatsapp_number'] ?? ''); ?>" />
        <?php
    }
}
