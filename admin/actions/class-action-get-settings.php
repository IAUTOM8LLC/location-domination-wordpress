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
class Action_Get_Settings implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_get_settings';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( isset( $_REQUEST[ 'post' ] ) ) {
            if ( isset( $_REQUEST[ 'uuid' ] ) ) {
                echo get_post_meta( $_REQUEST[ 'post' ], '_uuid', true );
                exit;
            }
        }

        return wp_send_json( [
            'apiKey'       => get_option( LOCATION_DOMINATION_API_OPTION_KEY ),
            'locationType' => get_option( LOCATION_DOMINATION_LOCATION_TYPE_OPTION ),
            'connected' => get_option( LOCATION_DOMINATION_API_CONNECTED_OPTION_KEY ) === 'connected',
        ] );
    }

}