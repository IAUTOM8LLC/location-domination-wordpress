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

        if ( isset( $option->progress ) && $option->progress >= 100 ) {
            return wp_send_json([ 'success' => true, 'completed' => true, 'progress' => 100 ] );
        }

        global $wpdb;

        $template_id = (int) $_POST[ 'template' ];
        $template    = get_post( $template_id, 'ARRAY_A' );

        $fields = get_fields( $template_id );

        $enabled_templates = array_filter( $fields[ 'spin_templates' ], function ( $item ) {
            return $item[ 'enabled' ];
        } );

        $enabled_templates_ids = array_map( function ( $item ) {
            return $item[ 'template' ];
        }, $enabled_templates );

        $sub_template_spinning = count( $enabled_templates_ids ) > 0;

        // Start batching responses
        $last_post_request_id = (int) $option->request_id;
        $url                  = sprintf( '%s/api/post-requests/%d/workload?batch=%d', trim( MAIN_URL, '/' ), $last_post_request_id, $option->batches->completed );

        $response = wp_remote_get( $url );

        if ( ! is_wp_error( $response ) ) {
            $json_response = json_decode( $response[ 'body' ] );

            $option->job_in_progress     = true;
            $option->last_job_started_at = time();

            set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY, $option, 0 );

            $page_title = isset( $fields['page_title'] ) ? $fields['page_title'] : null;
            $page_slug = isset( $fields['page_slug'] ) ? $fields['page_slug'] : null;

            foreach ( $json_response->cities as $record ) {
                $random_template_key = array_rand( $enabled_templates_ids );
                $base_template_id    = $sub_template_spinning ? $enabled_templates_ids[ $random_template_key ] : $template_id;
                $base_template       = get_post( $base_template_id, 'ARRAY_A' );
                $base_template_settings = $enabled_templates[ $random_template_key ];

                $meta = get_post_custom( $base_template_id );

                $shortcode_bindings = [
                    '[city]'    => isset( $record->city ) ? $record->city : '',
                    '[county]'  => isset( $record->county ) ? $record->county : '',
                    '[state]'   => isset( $record->state ) ? $record->state : '',
                    '[zips]'    => isset( $record->zips ) ? $record->zips : '',
                    '[region]'  => isset( $record->region ) ? $record->region : '',
                    '[country]' => isset( $record->country ) ? $record->country : '',
                ];

                $title = apply_filters( 'location_domination_shortcodes', ( $sub_template_spinning ? $base_template_settings['post_name'] : $base_template[ 'post_title' ] ), $shortcode_bindings );
                $uuid = get_post_meta( $template_id, '_uuid', true );

                if ( ! $sub_template_spinning && $page_title ) {
                    $title = apply_filters( 'location_domination_shortcodes', $page_title, $shortcode_bindings );
                }

                $arguments = [
                    'post_type'    => get_post_meta( $template_id, '_uuid', true ),
                    'post_title'   => Location_Domination_Spinner::spin( $title ),
                    'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] ),
                    'post_status'  => 'publish',
                ];

                if ( ! $sub_template_spinning && $page_title ) {
                    $arguments['post_name'] = apply_filters( 'location_domination_shortcodes', $page_slug   , $shortcode_bindings );
                }

//                $meta_title       = $this->get_parameter_with_shortcodes( $request, 'meta_title', $shortcode_bindings );
//                $meta_description = $this->get_parameter_with_shortcodes( $request, 'meta_description', $shortcode_bindings );
//                $job_title        = $this->get_parameter_with_shortcodes( $request, 'job_title', $shortcode_bindings );
//                $job_description  = $this->get_parameter_with_shortcodes( $request, 'job_description', $shortcode_bindings );
//                $schema           = $this->get_parameter_with_shortcodes( $request, 'schema', $shortcode_bindings );

                $new_post_id = wp_insert_post( $arguments );

                Endpoint_Create_Posts::meta_spinner( $meta, $new_post_id, $shortcode_bindings );

                add_post_meta( $new_post_id, '_city', isset( $record->city ) ? $record->city : '' );
                add_post_meta( $new_post_id, '_state', isset( $record->state ) ? $record->state : '' );
                add_post_meta( $new_post_id, '_county', isset( $record->county ) ? $record->county : '' );
                add_post_meta( $new_post_id, '_zips', isset( $record->zips ) ? $record->zips : '' );
                add_post_meta( $new_post_id, '_region', isset( $record->region ) ? $record->region : '' );
                add_post_meta( $new_post_id, '_country', isset( $record->country ) ? $record->country : '' );
                update_post_meta( $new_post_id, '_uuid', $uuid );
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