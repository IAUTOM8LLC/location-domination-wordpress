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
class Shortcode_Zips implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return [ 'zips', 'zip_codes' ];
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $attributes = shortcode_atts([
            'show_first' => false,
        ], $attributes );

        $zips = get_post_meta( get_the_ID(), '_zips', true );

        if ( $attributes['show_first'] ) {
            $zips_array = explode(',', $zips );

            if ( isset( $zips_array[0] ) ) {
                return $zips_array[0];
            }
        }

        return '<p style="overflow-wrap: break-word;">' . str_replace( ',', ', ', $zips ) . '</p>';
    }

}
