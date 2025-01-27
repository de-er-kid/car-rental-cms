<?php

class Car_Custom_Image_Shortcode
{
    public function __construct() {
        add_shortcode("custom_image", array( $this, "render_custom_image") );
    }
    
    public function render_custom_image( $atts ) {
        $atts = shortcode_atts([
            'key' => ''
        ], $atts);
        
        if (empty($atts['key'])) {
            return '';
        }
        
        $benefits_image = get_post_meta(get_the_ID(), $atts['key'], true);
        
        $attachment_id = $this->get_attachment_id_by_url($benefits_image);
        
        $alt_text = $attachment_id ? get_post_meta($attachment_id, '_wp_attachment_image_alt', true) : '';
        
        if (empty($alt_text)) {
            $alt_text = get_the_title();
        }
        
        ob_start();
        ?>
        <img 
            class="<?php echo esc_attr($atts['key']); ?>" 
            src="<?php echo esc_url($benefits_image); ?>" 
            alt="<?php echo esc_attr($alt_text); ?>" 
        />
        <?php
        return ob_get_clean();
    }
    
    private function get_attachment_id_by_url($url) {
		global $wpdb;

		$parsed_url = wp_parse_url($url);
		$path = $parsed_url['path'];

		$attachment = $wpdb->get_col($wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta 
			 WHERE meta_key = '_wp_attached_file' 
			 AND meta_value LIKE %s", 
			'%' . $wpdb->esc_like(basename($path)) . '%'
		));

		if (empty($attachment)) {
			$attachment = $wpdb->get_col($wpdb->prepare(
				"SELECT post_id FROM $wpdb->posts 
				 WHERE post_type = 'attachment' 
				 AND guid LIKE %s", 
				'%' . $wpdb->esc_like(basename($path)) . '%'
			));
		}

		return !empty($attachment) ? $attachment[0] : false;
	}
}
new Car_Custom_Image_Shortcode();