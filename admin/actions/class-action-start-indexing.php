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
class Action_Start_Indexing implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_start_indexing';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        if ( wp_verify_nonce( $_REQUEST[ '_nonce' ], 'location-domination-start-indexing' ) === false ) {
            return wp_send_json( [ 'success' => false, 'message' => 'Your session has expired.' ] );
        }

        global $wpdb;

        $templateId = (int) $_REQUEST[ 'templateId' ];

        // Remove all existing posts
        $templateUuid = get_post_meta( $templateId, '_uuid', true );

        if ( $templateUuid ) {
            $wpdb->delete( $wpdb->prefix . LOCATION_DOMINATION_INDEX_DB_TABLE, [
                'post_type' => $templateUuid,
            ] );
        }

        $post_count = wp_count_posts( $templateUuid );
        $payload_size = 75;
        $batches_needed = ceil( $post_count / $payload_size );

        $option = (object) [
            'template_id'         => $templateId,
            'template_uuid'       => $templateUuid,
            'progress'            => 0,
            'job_in_progress'     => false,
            'last_job_started_at' => false,
            'requested_at'        => date('Y-m-d h:i:s' ),
            'total_pages'         => $post_count,
            'batches'             => (object) [
                'needed'       => $batches_needed,
                'completed'    => 0,
                'payload_size' => $payload_size,
            ]
        ];

        set_transient( Action_Process_Indexing::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $templateId, $option, 0 );

        return wp_send_json( [
            'success' => true,
        ] );
    }

}