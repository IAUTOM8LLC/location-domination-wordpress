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
class Action_Preview_Request implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_preview_request';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        // print_r($_POST);exit;
        if ( wp_verify_nonce( $_REQUEST[ '_nonce' ], 'location-domination-start-queue' ) === false ) {
            return wp_send_json( [ 'success' => false, 'message' => 'Your session has expired.' ] );
        }

        global $wpdb;

        $option = get_option( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY );

        if ( $option ) {
            return wp_send_json( [ 'success' => false, 'message' => 'You already have a job started.' ] );
        }

        $templateId = (int) $_REQUEST[ 'templateId' ];

        // Start queue
        $response = wp_remote_post( trim( MAIN_URL, '/' ) . '/api/post-requests-local?preview=1', [
            'body' => $_POST,
            'timeout' => 300
        ] );

        if ( is_wp_error( $response ) || $response[ 'response' ][ 'code' ] !== 200 ) {
            return wp_send_json( [
                'success' => false,
                'message' => 'There was an issue communicating with the server.',
            ] );
        }

        // Remove all existing posts
        $job = json_decode( $response[ 'body' ] );
        return wp_send_json( [
            'success' => true,
            'posts'   => $job->count,
        ] );
    }

}