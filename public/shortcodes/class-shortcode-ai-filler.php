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
class Shortcode_AIFiller implements Shortcode_Interface
{

    /**
     * The shortcode name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_key()
    {
        return 'AIFiller';
    }

    /**
     * The contents to replace the shortcode with.
     *
     * @param array|null $attributes The attributes passed to the shortcode.
     * @return string
     * @since 1.0.0
     */
    public function handle($attributes = null)
    {
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
        // Retrieve the current post ID
        $post_id = get_the_ID();

        // Get the 'aifiller' meta value for the current post
        $aifiller_meta = get_post_meta($post_id, 'aifiller', true);

        // Use the meta value if it exists, otherwise fallback to the prompt attribute
        $shortcodes = !empty($aifiller_meta) ? $aifiller_meta : $atts['prompt'];

        if (!is_array($shortcodes)) return '<div class="ai-filler">' . esc_html($prompt) . '</div>';

        // Output the prompt in a formatted way
        foreach ($shortcodes as $shortcode) {
            if (
                isset($shortcode['attributes']['prompt']) &&
                $shortcode['attributes']['prompt'] === $prompt
            ) {
                $text = $shortcode['generated_text'];

                $text = str_replace(
                    ['“', '”', '‘', '’', '„', '‟', '‹', '›'],
                    ['"', '"', "'", "'", '"', '"', "'", "'"],
                    $text
                );

                $text = preg_replace([
                    '/```[a-z]*\n?/', // remove opening code block
                    '/```/',          // remove closing code block
                    '/`/',            // remove inline code ticks
                    '/\*\*(.*?)\*\*/',// remove bold markdown
                    '/\*(.*?)\*/',    // remove italic markdown
                    '/_(.*?)_/',      // remove underscores italic markdown
                ], '$1', $text);

                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim($text);
                return '<div class="ai-filler">' . $text . '</div>';
            }
        }
        return '<div class="ai-filler">' . esc_html($prompt) . '</div>';
    }
}

// For some reason, some themes lowercase the shortcode name, so this deals with both lowercase or uppercase
function register_aifiller_shortcode()
{
    if (class_exists('Shortcode_AIFiller')) {
        $shortcode = new Shortcode_AIFiller();
        add_shortcode($shortcode->get_key(), [$shortcode, 'handle']);   // Uppercase
        add_shortcode(strtolower($shortcode->get_key()), [$shortcode, 'handle']); // Lowercase
    }
}
add_action('init', 'register_aifiller_shortcode');
