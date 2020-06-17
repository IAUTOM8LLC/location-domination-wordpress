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
class Action_Process_Indexing implements Action_Interface {

    static $LOCATION_DOMINATION_PROGRESS_KEY = 'location-domination-indexing-progress';

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_process_indexing';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        $start = microtime( true );
        $template_id = (int) $_POST[ 'template' ];

        $option = get_transient( Action_Process_Indexing::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        if ( ! $option ) {
            return wp_send_json( [ 'success' => false, 'message' => 'You have no active jobs running.' ] );
        }

        /**
         * Checks to see whether we have any jobs in progress and
         * have not timed-out.
         */
        if ( isset( $option->job_in_progress, $option->last_job_started_at ) && $option->job_in_progress && $option->last_job_started_at > strtotime( '-3 minutes' ) ) {
            return wp_send_json( [ 'success' => false, 'in_progress' => true, 'progress' => $option->progress ] );
        }

        if ( isset( $option->progress ) && $option->progress >= 100 ) {
            return wp_send_json( [ 'success' => true, 'completed' => true, 'progress' => 100 ] );
        }

        global $wpdb;

        $post_type = get_post_meta( $template_id, '_uuid', true );

        $query = new \WP_Query([
            'post_type' => $post_type,
            'posts_per_page' => 75,
            'paged' => $option->batches->completed,
        ]);

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                var_dump(get_post_meta( get_the_ID() ) );
            }
        }
        exit;

        $template    = get_post( $template_id, 'ARRAY_A' );

        return wp_send_json( [ 'success' => false ] );
    }

}