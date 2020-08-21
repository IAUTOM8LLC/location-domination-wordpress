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
class Shortcode_Map implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return [ 'ld_map', 'ld-map' ];
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $query     = null;
        $post_meta = get_post_meta( get_the_ID() );

        // Is United States?
        if ( isset( $post_meta[ '_neighborhood' ], $post_meta[ '_county' ] ) ) {
            $neighborhood = unserialize( $post_meta[ '_neighborhood' ][0] );

            $query = sprintf( '%s, %s, United States', $neighborhood->neighborhood, $post_meta[ '_county' ][0] );
        } else if ( isset( $post_meta[ '_city' ], $post_meta[ '_county' ] ) ) {
            $query = sprintf( '%s, %s, United States', $post_meta[ '_city' ][0], $post_meta[ '_county' ][0] );
        } else if ( isset( $post_meta[ '_city' ], $post_meta[ '_region' ], $post_meta[ '_country' ] ) ) {
            $query = sprintf( '%s, %s, %s', $post_meta[ '_city' ][0], $post_meta[ '_region' ][0], $post_meta[ '_country' ][0] );
        } else if ( isset( $post_meta[ '_city' ], $post_meta[ '_region' ] ) ) {
            $query = sprintf( '%s, %s', $post_meta[ '_city' ][0], $post_meta[ '_region' ][0] );
        }

        if ( $query && trim( $query ) !== ',' ) {
            return sprintf( '<iframe width="400" height="300" src="https://maps.google.com/maps?width=400&height=300&hl=en&q=%s&ie=UTF8&t=&z=14&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>', urlencode( $query ) );
        }
    }

}
