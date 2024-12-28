<?php


/**
 * Class Car_FAQ_Metabox
 *
 * Adds a metabox for managing FAQ content.
 */

class Car_FAQ_Metabox
{

    /**
     * Add meta box for FAQ section
     * Contains repeatable field set with:
     * - Title field
     * - Description field (WYSIWYG editor)
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('add_meta_boxes', [$this, 'add_faq_meta_box']);
        add_action('save_post', [$this, 'save_faq_meta']);
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script('car-faq-metabox', plugins_url('car-faq-metabox.js', __FILE__), ['jquery'], null, true);
    }

    public function add_faq_meta_box()
    {
        add_meta_box(
            'car_faq_meta_box',
            __('Car FAQ Section', 'car-rental-cms'),
            [$this, 'render_faq_meta_box'],
            'cars',
            'normal',
            'high'
        );
    }

    public function render_faq_meta_box($post)
    {
        wp_nonce_field('car_faq_nonce', 'car_faq_nonce');
        $faqs = get_post_meta($post->ID, '_car_faqs', true);
        ?>
        <style>
            #car_faqs_container {
                display: flex;
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
            }

            .faq-group {
                width: calc(50% - 42px);
                border: 1px solid #ebebeb;
                padding: 15px;
            }

            #add_faq {
                margin-top: 20px;
                width: 100%;
                padding: 1px;
                /* background-color: rebeccapurple; */
            }

            .faq-group textarea {
                display: block;
                width: 100%;
                min-height: 582px;
                height: 100%;
                border: 1px solid #dddddf;
            }

            .notice.e-notice.e-notice--dismissible.e-notice--extended {
                display: none;
            }
        </style>
        <div id="car_faqs_container">
            <?php if ($faqs):
                foreach ($faqs as $index => $faq): ?>
                    <div class="faq-group">
                        <p>
                            <label><?php _e('Question', 'car-rental-cms'); ?></label>
                            <input type="text" class="widefat" name="car_faq[question][]"
                                value="<?php echo esc_attr($faq['question']); ?>" />
                        </p>
                        <p>
                            <label><?php _e('Answer', 'car-rental-cms'); ?></label>
                            <?php wp_editor($faq['answer'], "car_faq_answer_{$index}", ['textarea_name' => 'car_faq[answer][]']); ?>
                        </p>
                        <button type="button"
                            class="remove-faq button button-primary button-large"><?php _e('Remove', 'car-rental-cms'); ?></button>
                    </div>
                <?php endforeach; endif; ?>
        </div>
        <button type="button" id="add_faq"
            class="button button-primary button-large"><?php _e('Add FAQ', 'car-rental-cms'); ?></button>
        <!-- add js here for repeater -->

        <script>
            jQuery(document).ready(function ($) {
                let faqIndex = $('.faq-group').length;

                $('#add_faq').on('click', function () {
                    const newIndex = faqIndex++;
                    const $template = $(`
                                        <div class="faq-group">
                                            <p>
                                                <label><?php _e('Question', 'car-rental-cms'); ?></label>
                                                <input type="text" class="widefat" name="car_faq[question][]" value="" />
                                            </p>
                                            <p>
                                                <label><?php _e('Answer', 'car-rental-cms'); ?></label>
                                                <textarea id="car_faq_answer_${newIndex}" name="car_faq[answer][]"></textarea>
                                            </p>
                                            <button type="button" class="remove-faq button button-primary button-large">
                                                <?php _e('Remove', 'car-rental-cms'); ?>
                                            </button>
                                        </div>
                                    `);

                    $('#car_faqs_container').append($template);

                    wp.editor.initialize(`car_faq_answer_${newIndex}`, {
                        tinymce: true,
                        quicktags: true,
                        mediaButtons: true
                    });
                });


                $(document).on('click', '.remove-faq', function () {

                    $(this).closest('.faq-group').remove();

                });

            });
        </script>

        <?php
    }

    public function save_faq_meta($post_id)
    {
        if (!isset($_POST['car_faq_nonce']) || !wp_verify_nonce($_POST['car_faq_nonce'], 'car_faq_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['car_faq'])) {
            $questions = $_POST['car_faq']['question'];
            $answers = $_POST['car_faq']['answer'];
            $faqs = [];

            for ($i = 0; $i < count($questions); $i++) {
                if (!empty($questions[$i])) {
                    $faqs[] = [
                        'question' => sanitize_text_field($questions[$i]),
                        'answer' => wp_kses_post($answers[$i])
                    ];
                }
            }
            update_post_meta($post_id, '_car_faqs', $faqs);
        }
    }

    // Shortcode to accordion output for above FAQ to print in single-cars template



}

// function car_faq_shortcode()
// {
//     $faqs = get_post_meta(get_the_ID(), '_car_faqs', true);

//     $output = '<div class="car-faq-section">';

//     if (!empty($faqs)) {
//         foreach ($faqs as $faq) {
//             $output .= '<div class="faq-item">';
//             $output .= '<h3>' . esc_html($faq['question']) . '</h3>';
//             $output .= '<div class="faq-content">' . wpautop($faq['answer']) . '</div>';
//             $output .= '</div>';
//         }
//     }

//     $output .= '</div>';

//     return $output;
// }
// add_shortcode('car_faq', 'car_faq_shortcode');
