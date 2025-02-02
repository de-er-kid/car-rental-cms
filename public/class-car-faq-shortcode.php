<?php
class Car_FAQ_Shortcode {
    public function __construct() {
        add_shortcode("car_faq", array($this, "render_car_faq"));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_faq_assets'));
    }

    public function enqueue_faq_assets() {
        // Register and enqueue the styles
        wp_register_style('car-faq-styles', false); // Register virtual CSS file
        wp_enqueue_style('car-faq-styles');
        wp_add_inline_style('car-faq-styles', $this->get_faq_styles());

        // Register and enqueue the scripts
        wp_register_script('car-faq-script', '', array(), '', true); // Register virtual JS file
        wp_enqueue_script('car-faq-script');
        wp_add_inline_script('car-faq-script', $this->get_faq_script());
    }

    private function get_faq_styles() {
        return "
            .car-faq-section {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .faq-item {
                border: 1px solid rgba(0, 0, 0, 0.1);
                padding: 20px 30px;
                border-radius: 24px;
                transition: all 0.3s ease;
            }

            .faq-item:hover {
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }

            .faq-item h3 {
                margin: 0;
                cursor: pointer;
                position: relative;
                font-family: Figtree;
                font-size: 18px;
                font-weight: 500;
                letter-spacing: -0.02em;
                padding-right: 20px;
                transition: color 0.3s ease;
            }

            .faq-item h3:hover {
                color: #444;
            }

            .faq-item h3::after {
                content: '+';
                position: absolute;
                right: -10px;
                top: 50%;
                transform: translateY(-50%);
                transition: transform 0.3s ease, opacity 0.2s ease;
                font-size: 24px;
                opacity: 0.7;
            }

            .faq-item.active h3::after {
                transform: translateY(-50%) rotate(45deg);
            }

            .faq-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.5s ease-in-out, opacity 0.3s ease-in-out, margin 0.3s ease;
                opacity: 0;
                margin-top: 0;
                color: #000000;
                font-family: 'Figtree', Sans-serif;
                font-size: 14px;
                font-weight: 400;
            }

            .faq-item.active .faq-content {
                max-height: 300px;
                opacity: 1;
                margin-top: 15px;
            }

            @media (max-width: 1024px) {
                .faq-item {
                    padding: 15px 20px;
                    border-radius: 12px;
                }
                
                .faq-item h3 {
                    font-size: 13px;
                }
            }
        ";
    }

    private function get_faq_script() {
        return "
            document.addEventListener('DOMContentLoaded', function() {
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const header = item.querySelector('h3');
                    
                    header.addEventListener('click', () => {
                        const isActive = item.classList.contains('active');
                        
                        // Close all items first
                        faqItems.forEach(otherItem => {
                            otherItem.classList.remove('active');
                        });
                        
                        // If the clicked item wasn't active, open it
                        if (!isActive) {
                            item.classList.add('active');
                        }
                    });
                });
            });
        ";
    }

    public function render_car_faq() {
        $faqs = get_post_meta(get_the_ID(), '_car_faqs', true);
        
        ob_start();
        
        // Main FAQ container
        echo '<div class="car-faq-section">';
        
        if (!empty($faqs)) {
            foreach ($faqs as $faq) {
                echo '<div class="faq-item">';
                echo '<h3>' . esc_html($faq['question']) . '</h3>';
                echo '<div class="faq-content">' . wpautop($faq['answer']) . '</div>';
                echo '</div>';
            }
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }
}
new Car_FAQ_Shortcode();