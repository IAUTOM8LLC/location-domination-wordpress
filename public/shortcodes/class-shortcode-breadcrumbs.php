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
class Shortcode_Breadcrumbs implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'breadcrumb';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $state  = get_post_meta( get_the_ID(), '_state', true );
        $county = get_post_meta( get_the_ID(), '_county', true );
        $city   = get_post_meta( get_the_ID(), '_city', true );
        $region   = get_post_meta( get_the_ID(), '_region', true );

        if ( $city && $state && $county ) {
            return $state . ' >> ' . $county . ' >> ' . $city;
        }

        return $region . ' >> ' . $city;
    }

}