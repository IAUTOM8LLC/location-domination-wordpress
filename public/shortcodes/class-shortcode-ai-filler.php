<?php

/**
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 */

/**
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Shortcode_AIFiller implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_key() {
        return 'AIFiller';
    }

    /**
     * The contents to replace the shortcode with.
     *
     * @param array|null $attributes The attributes passed to the shortcode.
     * @return string
     * @since 1.0.0
     */
    public function handle( $attributes = null ) {
        // Set default attributes and merge with provided attributes
        $atts = shortcode_atts(
            array(
                'prompt' => 'Default prompt text', // Default prompt if none is provided
            ),
            $attributes,
            $this->get_key()
        );

        // Sanitize the prompt attribute
        $prompt = sanitize_text_field($atts['prompt']);

        // Output the prompt in a formatted way
        return '<div class="ai-filler">' . esc_html($prompt) . '</div>';
    }

}
