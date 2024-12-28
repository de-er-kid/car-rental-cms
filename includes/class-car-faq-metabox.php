<?php


/**
 * Class Car_Gallery_Metabox
 *
 * Adds a metabox for managing FAQ content.
 */

class Car_FAQ_Metabox
{

    public function __construct() {}

    /**
     * Add meta box for FAQ, repeater field set: title, description(WYSIWYG)
     */

    public function add_faq_meta_box() {
        add_meta_box("", __("",""), array( $this,""),
    }
}
