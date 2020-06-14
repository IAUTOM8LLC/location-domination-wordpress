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
class Action_Cancel_Queue implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_cancel_queue';
    }

    /**
     * Remove the transient if they decide to abort the process.
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( wp_verify_nonce( $_REQUEST[ '_nonce' ], 'location-domination-cancel-queue' ) === false ) {
            return wp_send_json( [ 'success' => false, 'message' => 'Your session has expired.' ] );
        }

        $templateId = (int) $_REQUEST[ 'templateId' ];

        delete_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $templateId );

        return wp_send_json( [
            'success'        => true,
        ] );
    }

}