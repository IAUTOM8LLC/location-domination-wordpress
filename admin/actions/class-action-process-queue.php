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
class Action_Process_Queue implements Action_Interface {

    static $LOCATION_DOMINATION_PROGRESS_KEY = 'location-domination-progress';

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_process_queue';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        $start = microtime( true );

        $option = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY );

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

        global $wpdb;

        $template_id = (int) $_POST[ 'template' ];
        $template    = get_post( $template_id, 'ARRAY_A' );

        // Start batching responses
        $last_post_request_id = (int) $option->request_id;
        $url                  = sprintf( '%s/api/post-requests/%d/workload?batch=%d', trim( MAIN_URL, '/' ), $last_post_request_id, $option->batches->completed );

        $response = wp_remote_get( $url );

        if ( ! is_wp_error( $response ) ) {
            $json_response = json_decode( $response[ 'body' ] );

            $option->job_in_progress     = true;
            $option->last_job_started_at = time();

            set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY, $option, 0 );

            $mutated_post = $template;
            unset( $mutated_post[ 'ID' ] );
            unset( $mutated_post[ 'guid' ] );
            unset( $mutated_post[ 'comment_count' ] );

            $meta = get_post_custom( $template_id );

            foreach ( $json_response->cities as $record ) {
                $shortcode_bindings = [
                    '[city]'    => isset( $record->city ) ? $record->city : '',
                    '[county]'  => isset( $record->county ) ? $record->county : '',
                    '[state]'   => isset( $record->state ) ? $record->state : '',
                    '[zips]'    => isset( $record->zips ) ? $record->zips : '',
                    '[region]'  => isset( $record->region ) ? $record->region : '',
                    '[country]' => isset( $record->country ) ? $record->country : '',
                ];

                $title = apply_filters( 'location_domination_shortcodes', $template[ 'post_title' ], $shortcode_bindings );
//                $slug  = trim( $this->get_post_slug( $request, $shortcode_bindings ) );

                $arguments = [
                    'post_type'    => get_post_meta( $template[ 'ID' ], '_uuid', true ),
//                    'post_name'    => $slug,
                    'post_title'   => Location_Domination_Spinner::spin( $title ),
                    'post_content' => Location_Domination_Spinner::spin( $template[ 'post_content' ] ),
                    'post_status'  => 'publish',
                ];

//                $meta_title       = $this->get_parameter_with_shortcodes( $request, 'meta_title', $shortcode_bindings );
//                $meta_description = $this->get_parameter_with_shortcodes( $request, 'meta_description', $shortcode_bindings );
//                $job_title        = $this->get_parameter_with_shortcodes( $request, 'job_title', $shortcode_bindings );
//                $job_description  = $this->get_parameter_with_shortcodes( $request, 'job_description', $shortcode_bindings );
//                $schema           = $this->get_parameter_with_shortcodes( $request, 'schema', $shortcode_bindings );

                $post_ID = wp_insert_post( $arguments );

                $arguments[ 'ID' ] = $post_ID;

//                $wpdb->query( 'SET autocommit = 0;' );

                $wpdb->delete( $wpdb->prefix . 'postmeta', [
                    'post_id' => $arguments[ 'ID' ],
                ] );

                Endpoint_Create_Posts::meta_spinner( $meta, $arguments[ 'ID' ], $shortcode_bindings );

                add_post_meta( $arguments[ 'ID' ], '_city', isset( $record->city ) ? $record->city : '' );
                add_post_meta( $arguments[ 'ID' ], '_state', isset( $record->state ) ? $record->state : '' );
                add_post_meta( $arguments[ 'ID' ], '_county', isset( $record->county ) ? $record->county : '' );
                add_post_meta( $arguments[ 'ID' ], '_zips', isset( $record->zips ) ? $record->zips : '' );
                add_post_meta( $arguments[ 'ID' ], '_region', isset( $record->region ) ? $record->region : '' );
                add_post_meta( $arguments[ 'ID' ], '_country', isset( $record->country ) ? $record->country : '' );

//                if ( isset( $schema ) && $schema ) {
//                    add_post_meta( $arguments[ 'ID' ], '_ld_schema', $schema );
//                }
//
//                $wpdb->query( 'COMMIT;' );
            }

            // Update transient to tell the next job it can continue
            $option->job_in_progress     = false;
            $option->last_job_started_at = false;
            $option->batches->completed ++;
            $option->progress = round( ( $option->batches->completed / $option->batches->needed ) * 100 );

            set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY, $option, 0 );

            $execution_time           = microtime( true ) - $start;
            $batches_remaining        = $option->batches->needed - $option->batches->completed;
            $estimated_time_remaining = ( $batches_remaining + 10 ) * $execution_time;

            return wp_send_json( [
                'success'                  => true,
                'execution_time'           => ceil( $execution_time ),
                'progress'                 => $option->progress,
                'batches_left'             => $batches_remaining,
                'batches_remaining'        => $batches_remaining,
                'estimated_time_remaining' => ceil( $estimated_time_remaining ),
            ] );
        }

        return wp_send_json( [ 'success' => false ] );
    }

}