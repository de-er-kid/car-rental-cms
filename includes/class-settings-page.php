<?php

class Settings_Page {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=cars',
            __('Car Rental Enquiry Settings', 'car-rental-cmc'),
            __('Settings', 'car-rental-cmc'),
            'manage_options',
            'car_rental_enquiry_settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Car Rental Enquiry Settings', 'car-rental-cmc'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('car_rental_enquiry_settings_group');
                do_settings_sections('car_rental_enquiry_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('car_rental_enquiry_settings_group', 'car_rental_enquiry_settings');

        add_settings_section(
            'car_rental_enquiry_settings_section',
            __('Rent Enquiry Settings', 'car-rental-cmc'),
            null,
            'car_rental_enquiry_settings'
        );

        add_settings_field(
            'car_rental_whatsapp_number',
            __('WhatsApp Number', 'car-rental-cmc') . $this->get_tooltip(__('WhatsApp number no <pre>+</pre>, with country code <pre>971123456789</pre>.', 'car-rental-cmc')),
            array($this, 'render_whatsapp_field'),
            'car_rental_enquiry_settings',
            'car_rental_enquiry_settings_section'
        );

        add_settings_field(
            'car_rental_email_address',
            __('Email Address', 'car-rental-cmc') . $this->get_tooltip(__('Enter the email address for enquiries.', 'car-rental-cmc')),
            array($this, 'render_email_field'),
            'car_rental_enquiry_settings',
            'car_rental_enquiry_settings_section'
        );

        add_settings_field(
            'car_rental_phone_number',
            __('Phone Number', 'car-rental-cmc') . $this->get_tooltip(__('Enter the phone number for enquiries.', 'car-rental-cmc')),
            array($this, 'render_phone_field'),
            'car_rental_enquiry_settings',
            'car_rental_enquiry_settings_section'
        );
    }

    public function get_tooltip($text) {
        return sprintf(
            ' <span class="dashicons dashicons-info" title="%s" style="vertical-align: middle; cursor: help;"></span>',
            esc_attr($text)
        );
    }

    public function render_whatsapp_field() {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <style>
            .notice.e-notice.e-notice--dismissible.e-notice--extended {
                display: none !important;
            }
        </style>
        <input type="tel" name="car_rental_enquiry_settings[whatsapp_number]" placeholder="971123456789" value="<?php echo esc_attr($settings['whatsapp_number'] ?? ''); ?>" />
        <?php
    }

    public function render_email_field() {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="email" name="car_rental_enquiry_settings[email_address]" placeholder="inquiry@example.com" value="<?php echo esc_attr($settings['email_address'] ?? ''); ?>" />
        <?php
    }

    public function render_phone_field() {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="tel" name="car_rental_enquiry_settings[phone_number]" placeholder="+971123456789" value="<?php echo esc_attr($settings['phone_number'] ?? ''); ?>" />
        <?php
    }
}
