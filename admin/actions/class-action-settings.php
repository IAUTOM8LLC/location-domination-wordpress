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
class Action_Settings implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_update_settings';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( wp_verify_nonce( $_POST[ '_nonce' ], 'location-domination-settings' ) === false ) {
            return wp_send_json([ 'success' => false, 'message' => 'Your session has expired.' ]);
        }

        update_option( LOCATION_DOMINATION_API_OPTION_KEY, sanitize_text_field( $_POST[ 'apiKey'] ) );
        update_option( LOCATION_DOMINATION_API_CONNECTED_OPTION_KEY, 'connected' );

        return wp_send_json([ 'success' => true ]);
    }

}