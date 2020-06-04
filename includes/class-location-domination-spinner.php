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
    static function spin( $content ) {
        return preg_replace_callback( self::REGEX_PATTERN, [
            Location_Domination_Spinner::class,
            'replace'
        ], $content );
    }

    /**
     * @param $text
     *
     * @return mixed|string
     * @since 2.0.0
     */
    static function replace( $text ) {
        $text = Location_Domination_Spinner::spin( $text[ 1 ] );
        $parts = explode( '|', $text );

        return $parts[ array_rand( $parts ) ];
    }

}