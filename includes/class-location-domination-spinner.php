<?php

/**
 * The spinner class.
 *
 *
 * @since      1.0.0
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination_Spinner {

    /**
     * The regex pattern used for grabbing spintaxs
     */
    /*        const REGEX_PATTERN = '/\{(((?>[^\{\}]+)|(?R))*?)\}/xu';*/
    const REGEX_PATTERN = '/(__|_\[)?\{(((?>[^\{\}]+)|(?R))*?)\}(__|\]_)?/xu';

    /**
     * @param $content
     *
     * @return string|string[]|null
     * @since 2.0.0
     */
    static function spin( $content, $seed = null ) {
        if ( $seed ) {
            $integer_seed = crc32( $seed );

            mt_srand( $integer_seed );
        }

        return preg_replace_callback( self::REGEX_PATTERN, [
            Location_Domination_Spinner::class,
            'replace',
        ], $content );
    }

    static function conntact_ai_spinner($body, $target){
        $api_key = trim( get_option( 'mpb_api_key' ) );
        $rest_url = sprintf( '%s/api/ai-spin/%s', trim( MAIN_URL, '/' ), $target );
        $body['api_key'] = $api_key;
        $ai_response = wp_remote_post( $rest_url, [
            'body' => $body,
        ] );
        return json_decode($ai_response['body']);
    }

    static function is_ai_spin($fields){
        return isset($fields['use_ai_spin']) && $fields['use_ai_spin'] == 1;
    }

    static function spin_title_content($title, $fields, $base_template, $shortcode_bindings){
        if (isset($fields['use_ai_spin']) && $fields['use_ai_spin'] == 1) {
            $ai_spin = self::conntact_ai_spinner([
                'post_title' =>  $title,
                'post_content' => $base_template[ 'post_content' ],
                'context' => $shortcode_bindings
            ], 'title_content');

            return [
                'post_title' => $ai_spin->post->title,
                'post_content' => $ai_spin->post->content
            ];
        } else {
            return [
                'post_title' => Location_Domination_Spinner::spin( $title ),
                'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] )
            ];
        }
    }

    static function spin_meta_title($meta_title, $fields, $shortcode_bindings){
        if ( self::is_ai_spin($fields) ) {
            $ai_spin = self::conntact_ai_spinner([
                'meta_title' =>  $meta_title,
                'context' => $shortcode_bindings
            ], 'meta_title');

            return [
                'meta_title' => $ai_spin->post->meta_title
            ];
        } else {
            return [
                'meta_title' => Location_Domination_Spinner::spin( $meta_title ),
            ];
        }
    }

    static function spin_meta_description($meta_description, $fields, $shortcode_bindings){
        if ( self::is_ai_spin($fields) ) {
            $ai_spin = self::conntact_ai_spinner([
                'meta_description' =>  $meta_description,
                'context' => $shortcode_bindings
            ], 'meta_description');

            return [
                'meta_description' => $ai_spin->post->meta_description
            ];
        } else {
            return [
                'meta_description' => Location_Domination_Spinner::spin( $meta_description ),
            ];
        }
    }

    /**
     * @param $text
     *
     * @return mixed|string
     * @since 2.0.0
     */
    static function replace( $text ) {
        $thrive_regex_pattern = '/(([_\[]){(.*)}([_\]]))/m';

        if ( preg_match( $thrive_regex_pattern, $text[ 0 ] ) ) {
            return $text[ 0 ];
        }

        $text  = Location_Domination_Spinner::spin( $text[ 2 ] );
        $parts = explode( '|', $text );

        if ( is_string( $text ) && json_decode( '{' . $text . '}' ) ) {
            return '{' . $text . '}';
        }

        return $parts[ array_rand( $parts ) ];
    }

}
