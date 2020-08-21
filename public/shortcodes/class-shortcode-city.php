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
class Shortcode_City implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'city';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $neighborhood = get_post_meta( get_the_ID(), '_neighborhood', true );

        if ( $neighborhood && $neighborhood->neighborhood ) {
            return $neighborhood->neighborhood;
        }

        return get_post_meta( get_the_ID(), '_city', true );
    }

}
