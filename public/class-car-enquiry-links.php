<?php
declare(strict_types=1);

class ClassCarEnquiryLinks
{
    private string $whatsappNumber;
    private string $emailAddress;
    private string $phoneNumber;

    public function __construct()
    {
        $enquirySettings = get_option('car_rental_enquiry_settings');
        $this->whatsappNumber = $enquirySettings['whatsapp_number'] ?? '';
        $this->emailAddress = $enquirySettings['email_address'] ?? '';
        $this->phoneNumber = $enquirySettings['phone_number'] ?? '';
    
        add_shortcode('car_whatsapp_link', [$this, 'whatsapp_link_shortcode']);
        add_shortcode('car_phone_link', [$this, 'phone_link_shortcode']);
        add_shortcode('car_email_link', [$this, 'email_link_shortcode']);
    }

    public function get_whatsapp_url($post_id = null) {
        if (empty($this->whatsappNumber)) {
            return '#';
        }
        $title = $post_id ? get_the_title($post_id) : get_the_title();
        return 'https://wa.me/' . $this->whatsappNumber . 
               '?text=' . urlencode("I'm interested in Renting " . $title);
    }

    public function get_phone_url() {
        return empty($this->phoneNumber) ? '#' : 'tel:' . $this->phoneNumber;
    }

    public function get_email_url($post_id = null) {
        if (empty($this->emailAddress)) {
            return '#';
        }
        $title = $post_id ? get_the_title($post_id) : get_the_title();
        $subject = "Interested in renting " . $title;
        $body = "Hello there,\n\nI am interested in renting " . $title . ". Could you please provide more information?\n\nThank you.";
        
        return 'mailto:' . $this->emailAddress . 
               '?subject=' . urlencode($subject) . 
               '&body=' . urlencode($body);
    }

    public function whatsapp_link_shortcode($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : null;
        return $this->get_whatsapp_url($post_id);
    }

    public function phone_link_shortcode($atts) {
        return $this->get_phone_url();
    }

    public function email_link_shortcode($atts) {
        $post_id = isset($atts['post_id']) ? intval($atts['post_id']) : null;
        return $this->get_email_url($post_id);
    }
}

new ClassCarEnquiryLinks();