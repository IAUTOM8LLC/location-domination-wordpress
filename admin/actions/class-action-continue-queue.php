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
class Action_Continue_Queue implements Action_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_continue_queue';
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

        $templateId   = (int) $_REQUEST[ 'templateId' ];
        $templateUuid = get_post_meta( $templateId, '_uuid', true );
        $transient    = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $templateId );

        if ( ! $transient || ! is_object( $transient ) ) {
            return wp_send_json( [ 'success' => false, 'message' => 'You have no job started.' ] );
        }

        // Roll back batches number in case they didn't fully process the last batch
        if ( isset( $transient->batches, $transient->batches->completed ) && $transient->batches->completed > 0 ) {
            $total_posts             = (int) wp_count_posts( $templateUuid )->publish;
            $posts_that_should_exist = (int) $transient->batches->payload_size * $transient->batches->completed;

            if ( $total_posts > $posts_that_should_exist ) {
                $post_ids = get_posts( [
                    'numberposts' => $total_posts - $posts_that_should_exist,
                    'post_type'   => $templateUuid,
                    'fields'      => 'ids',
                ] );

                foreach ( $post_ids as $post_id ) {
                    if ( ! $post_id ) {
                        continue;
                    }

                    $post_type = get_post_type( $post_id );

                    if ( $post_type !== $templateUuid ) {
                        continue;
                    }

                    wp_delete_post( $post_id );
                }
            }
        }

        set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY, $transient, 0 );

        return wp_send_json( [
            'success' => true,
            'progress'       => $transient->progress,
            'batches_needed' => $transient->batches->needed
        ] );
    }

}