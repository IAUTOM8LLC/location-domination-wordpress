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
class Action_Start_Queue implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_start_queue';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( wp_verify_nonce( $_REQUEST[ '_nonce' ], 'location-domination-start-queue' ) === false ) {
            return wp_send_json( [ 'success' => false, 'message' => 'Your session has expired.' ] );
        }

        require_once( __DIR__ . '/../../includes/class-location-domination-activator.php' );

        global $wpdb;

        $option = get_option( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY );

        if ( $option ) {
            return wp_send_json( [ 'success' => false, 'message' => 'You already have a job started.' ] );
        }

        if ( ! function_exists( 'maybe_create_table' ) ) {
            function maybe_create_table( $table_name, $create_ddl ) {
                global $wpdb;

                $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

                if ( $wpdb->get_var( $query ) == $table_name ) {
                    return true;
                }

                // Didn't find it, so try to create it.
                $wpdb->query( $create_ddl );

                // We cannot directly tell that whether this succeeded!
                if ( $wpdb->get_var( $query ) == $table_name ) {
                    return true;
                }

                return false;
            }
        }

        maybe_create_table( Location_Domination_Activator::getTableName(), Location_Domination_Activator::getTableSql() );

        $templateId   = (int) $_REQUEST[ 'templateId' ];
        $templateUuid = get_post_meta( $templateId, '_uuid', true );

        $wpdb->delete( Location_Domination_Activator::getTableName(), [
            'post_type' => $templateUuid,
            'locked'    => 0,
        ] );

        // Start queue
        $response = wp_remote_post( trim( MAIN_URL, '/' ) . '/api/post-requests-local', [
            'body' => $_POST,
        ] );
        // Save post request
        $last_post_request = get_post_meta( $templateId, 'location_domination_post_request', true );

        if ( ! $last_post_request ) {
            $last_post_request = [];
        }

        update_post_meta( $templateId, 'location_domination_post_request', array_merge( $last_post_request, $_POST ) );

        if ( is_wp_error( $response ) || $response[ 'response' ][ 'code' ] !== 200 ) {
            return wp_send_json( [
                'success' => false,
                'message' => 'There was an issue communicating with the server.',
            ] );
        }

        // Remove all existing posts
        if ( $templateUuid ) {
            $wpdb->delete( $wpdb->prefix . 'posts', [
                'post_type'  => $templateUuid,
                'menu_order' => 0,
            ] );
        }

        $job = json_decode( $response[ 'body' ] );

        // Record progress
        if ( ! $option ) {
            $option = (object) [
                'request_id'          => $job->post_request,
                'template_id'         => $templateId,
                'template_uuid'       => $templateUuid,
                'progress'            => 0,
                'job_in_progress'     => false,
                'last_job_started_at' => false,
                'request'             => $_POST,
                'requested_at'        => $job->data->requested_at,
                'total_pages'         => $job->data->total_requested_pages,
                'batches'             => (object) [
                    'needed'       => $job->data->batches_needed,
                    'completed'    => $job->data->batches_completed,
                    'payload_size' => $job->data->pages_per_batch,
                ]
            ];
        }

        set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $templateId, $option, 0 );

        return wp_send_json( [
            'success'        => true,
            'progress'       => $option->progress,
            'batches_needed' => $option->batches->needed
        ] );
    }

}
