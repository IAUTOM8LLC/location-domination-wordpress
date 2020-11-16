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
    const REGEX_PATTERN = '/\{(((?>[^\{\}]+)|(?R))*?)\}/xu';

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

        return preg_replace_callback( self::get_regex_pattern(), [
            Location_Domination_Spinner::class,
            'replace',
        ], $content );
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

        return $parts[ array_rand( $parts ) ];
    }

    static function get_regex_pattern() {
        if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_enabled() ) {
            return '/(__|_\[)?\{(((?>[^\{\}]+)|(?R))*?)\}(__|\]_)?/xu';
        }

        return '/\{(((?>[^\{\}]+)|(?R))*?)\}/xu';
    }

}
