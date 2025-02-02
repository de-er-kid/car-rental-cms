<?php

class Settings_Page
{
    private $settings_errors = array();

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_notices', array($this, 'show_settings_notice'));
    }

    public function show_settings_notice()
    {
        $screen = get_current_screen();
        if ($screen->id !== 'cars_page_car_rental_enquiry_settings') {
            return;
        }

        // Show success message if settings were updated without errors
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == true && !get_settings_errors('car_rental_enquiry_settings')) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e('Settings saved successfully!', 'car-rental-cmc'); ?></p>
            </div>
            <?php
        }

        // Show any error messages
        settings_errors('car_rental_enquiry_settings');
    }

    public function enqueue_admin_scripts($hook)
    {
        if ('cars_page_car_rental_enquiry_settings' !== $hook) {
            return;
        }
        wp_enqueue_editor();
    }

    public function add_settings_page()
    {
        add_submenu_page(
            'edit.php?post_type=cars',
            __('Car Rental Enquiry Settings', 'car-rental-cmc'),
            __('Settings', 'car-rental-cmc'),
            'manage_options',
            'car_rental_enquiry_settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page()
    {
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

    public function register_settings()
    {
        register_setting(
            'car_rental_enquiry_settings_group',
            'car_rental_enquiry_settings',
            array(
                'sanitize_callback' => array($this, 'sanitize_settings')
            )
        );

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
            'car_rental_whatsapp_message',
            __('WhatsApp Message Template', 'car-rental-cmc') . $this->get_tooltip(__('Message template for WhatsApp. Available macros: {post_title}, {post_link}, {post_id}', 'car-rental-cmc')),
            array($this, 'render_whatsapp_message_field'),
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
            'car_rental_cc_email_addresses',
            __('Email Addresses (Cc)', 'car-rental-cmc') . $this->get_tooltip(__('Enter the Cc email addresses for enquiries.', 'car-rental-cmc')),
            array($this, 'render_cc_email_field'),
            'car_rental_enquiry_settings',
            'car_rental_enquiry_settings_section'
        );

        add_settings_field(
            'car_rental_bcc_email_addresses',
            __('Email Addresses (Bcc)', 'car-rental-cmc') . $this->get_tooltip(__('Enter the Bcc email addresses for enquiries.', 'car-rental-cmc')),
            array($this, 'render_bcc_email_field'),
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

    public function sanitize_settings($input)
    {
        $sanitized = array();
        $valid = true;

        // WhatsApp number validation
        if (isset($input['whatsapp_number'])) {
            $whatsapp = preg_replace('/[^0-9]/', '', $input['whatsapp_number']);
            if (!empty($input['whatsapp_number'])) {
                if (empty($whatsapp)) {
                    add_settings_error(
                        'car_rental_enquiry_settings',
                        'invalid_whatsapp',
                        __('WhatsApp number must contain only numbers.', 'car-rental-cmc'),
                        'error'
                    );
                    $valid = false;
                } elseif (strlen($whatsapp) < 10) {
                    add_settings_error(
                        'car_rental_enquiry_settings',
                        'short_whatsapp',
                        __('WhatsApp number is too short. Please include country code.', 'car-rental-cmc'),
                        'error'
                    );
                    $valid = false;
                }
            }
            $sanitized['whatsapp_number'] = $whatsapp;
        }

        // WhatsApp message validation
        if (isset($input['whatsapp_message'])) {
            $message = wp_kses_post($input['whatsapp_message']);
            if (empty($message) && !empty($input['whatsapp_message'])) {
                add_settings_error(
                    'car_rental_enquiry_settings',
                    'invalid_message',
                    __('The WhatsApp message template contains invalid HTML.', 'car-rental-cmc'),
                    'error'
                );
                $valid = false;
            }
            $sanitized['whatsapp_message'] = $message;
        }

        // Email validation
        if (isset($input['email_address'])) {
            $email = sanitize_email($input['email_address']);
            if (!empty($input['email_address']) && !is_email($input['email_address'])) {
                add_settings_error(
                    'car_rental_enquiry_settings',
                    'invalid_email',
                    __('Please enter a valid email address.', 'car-rental-cmc'),
                    'error'
                );
                $valid = false;
            }
            $sanitized['email_address'] = $email;
        }

        // Validate Cc email addresses
        if (isset($input['cc_email_addresses'])) {
            $cc_emails = array_map('trim', explode(',', $input['cc_email_addresses']));
            foreach ($cc_emails as $email) {
                if (!empty($email) && !is_email($email)) {
                    add_settings_error(
                        'car_rental_enquiry_settings',
                        'invalid_cc_email',
                        __('One or more Cc email addresses are invalid.', 'car-rental-cmc'),
                        'error'
                    );
                    $valid = false;
                    break;
                }
            }
            $sanitized['cc_email_addresses'] = implode(',', $cc_emails);
        }

        // Validate Bcc email addresses
        if (isset($input['bcc_email_addresses'])) {
            $bcc_emails = array_map('trim', explode(',', $input['bcc_email_addresses']));
            foreach ($bcc_emails as $email) {
                if (!empty($email) && !is_email($email)) {
                    add_settings_error(
                        'car_rental_enquiry_settings',
                        'invalid_bcc_email',
                        __('One or more Bcc email addresses are invalid.', 'car-rental-cmc'),
                        'error'
                    );
                    $valid = false;
                    break;
                }
            }
            $sanitized['bcc_email_addresses'] = implode(',', $bcc_emails);
        }

        // Phone number validation
        if (isset($input['phone_number'])) {
            $phone = sanitize_text_field($input['phone_number']);
            if (!empty($input['phone_number'])) {
                // Simplified phone number validation
                $cleaned_phone = preg_replace('/[^0-9+]/', '', $phone);
                if (empty($cleaned_phone)) {
                    add_settings_error(
                        'car_rental_enquiry_settings',
                        'invalid_phone',
                        __('Please enter a valid phone number.', 'car-rental-cmc'),
                        'error'
                    );
                    $valid = false;
                }
            }
            $sanitized['phone_number'] = $phone;
        }

        // If validation failed, return old values
        if (!$valid) {
            $old_values = get_option('car_rental_enquiry_settings', array());
            return array_merge($old_values, $sanitized);
        }

        return $sanitized;
    }

    public function get_tooltip($text)
    {
        return sprintf(
            ' <span class="dashicons dashicons-info" title="%s" style="vertical-align: middle; cursor: help;"></span>',
            esc_attr($text)
        );
    }

    public function render_whatsapp_message_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        $default_message = "Hello! I'm interested in renting {post_title}. Could you provide more information?";
        $message = isset($settings['whatsapp_message']) ? $settings['whatsapp_message'] : $default_message;

        $editor_settings = array(
            'textarea_name' => 'car_rental_enquiry_settings[whatsapp_message]',
            'textarea_rows' => 5,
            'media_buttons' => false,
            'teeny' => true,
            'quicktags' => false
        );
        ?>
        <div class="whatsapp-message-editor">
            <div class="macro-help">
                <p><?php _e('Available macros:', 'car-rental-cmc'); ?></p>
                <ul>
                    <li><code>{post_title}</code> - <?php _e('Title of the car post', 'car-rental-cmc'); ?></li>
                    <li><code>{post_link}</code> - <?php _e('Link to the car post', 'car-rental-cmc'); ?></li>
                    <li><code>{post_id}</code> - <?php _e('ID of the car post', 'car-rental-cmc'); ?></li>
                </ul>
            </div>
            <?php wp_editor($message, 'car_rental_whatsapp_message', $editor_settings); ?>
        </div>
        <style>
            .whatsapp-message-editor {
                max-width: 600px;
            }

            .macro-help {
                margin-bottom: 10px;
                padding: 10px;
                background: #f8f9fa;
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .macro-help ul {
                margin: 5px 0 0 20px;
            }

            .macro-help code {
                background: #fff;
                padding: 2px 5px;
            }

            input {
                max-width: 600px;
            }
        </style>
        <?php
    }

    public function render_whatsapp_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="tel" name="car_rental_enquiry_settings[whatsapp_number]" placeholder="971123456789"
            value="<?php echo esc_attr(isset($settings['whatsapp_number']) ? $settings['whatsapp_number'] : ''); ?>" />
        <?php
    }

    public function render_email_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="email" name="car_rental_enquiry_settings[email_address]" placeholder="inquiry@example.com"
            value="<?php echo esc_attr(isset($settings['email_address']) ? $settings['email_address'] : ''); ?>" />
        <?php
    }

    public function render_cc_email_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="text" name="car_rental_enquiry_settings[cc_email_addresses]" placeholder="cc@example.com, cc2@example.com"
            value="<?php echo esc_attr(isset($settings['cc_email_addresses']) ? $settings['cc_email_addresses'] : ''); ?>"
            style="width: 100%;" />
        <p class="description"><?php _e('Enter comma-separated Cc email addresses.', 'car-rental-cmc'); ?></p>
        <?php
    }

    public function render_bcc_email_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="text" name="car_rental_enquiry_settings[bcc_email_addresses]"
            placeholder="bcc@example.com, bcc2@example.com"
            value="<?php echo esc_attr(isset($settings['bcc_email_addresses']) ? $settings['bcc_email_addresses'] : ''); ?>"
            style="width: 100%;" />
        <p class="description"><?php _e('Enter comma-separated Bcc email addresses.', 'car-rental-cmc'); ?></p>
        <?php
    }

    public function render_phone_field()
    {
        $settings = get_option('car_rental_enquiry_settings');
        ?>
        <input type="tel" name="car_rental_enquiry_settings[phone_number]" placeholder="+971123456789"
            value="<?php echo esc_attr(isset($settings['phone_number']) ? $settings['phone_number'] : ''); ?>" />
        <?php
    }
}
