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

        if ( function_exists( 'oxygen_can_activate_builder_compression' ) ) {
            if (is_string($text) && json_decode('{' . $text . '}')) {
                return '{' . $text . '}';
            }
        }

        $text  = Location_Domination_Spinner::spin( $text[ 2 ] );
        $parts = explode( '|', $text );

        return $parts[ array_rand( $parts ) ];
    }

}
