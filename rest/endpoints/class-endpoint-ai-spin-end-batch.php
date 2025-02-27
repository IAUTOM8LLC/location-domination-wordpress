<?php

/**
 * Validator for pinging the plugin.
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/rest
 */
class Endpoint_AI_Spin_End_Batch {

    protected $meta = [];

    protected $placeholders = [];

    /**
     * Always return true as we want to be able to
     * detect whether or not the plugin is active and
     * working.
     *
     * @param \WP_REST_Request $request
     *
     * @return boolean
     * @since 2.0.0
     */
    public function authorize( WP_REST_Request $request ) {
        return trim( get_option( LOCATION_DOMINATION_API_OPTION_KEY ) ) === trim( $request->get_param( 'api_key' ) );
    }

    function write_log( $log ) {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }

    /**
     * Responsible for showing that the plugin is active and
     * working correctly. Authentication is required for this
     * endpoint and it is used to create posts.
     *
     * NOTE: We have to disable PCRE Just-In-Time so that we
     * don't run into regex issues on large post requests where
     * there are thousands of instances of spinable content.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @since 2.0.0
     */
    public function handle( WP_REST_Request $request ) {
        $payload = $request->get_params();
        $template_id = $payload['template_id'];

        $option = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        // Build index pages
        $create_indexes = get_field( 'create_index_pages', $template_id );

        if ( $create_indexes ) {
            $indexer = new Action_Start_Indexing();
            $indexer->setRequest( $option->request );
            $indexer->handle();
        }

        Location_Domination_Admin::clear_permalinks_queued();

        if ( class_exists( 'Elementor\\Plugin' ) ) {
            // If using Elementor, re-generate CSS
            \Elementor\Plugin::$instance->files_manager->clear_cache();
        }

        // Delete queue transient
        delete_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Return an empty collection.
     *
     * @return mixed|void
     * @since 2.0.0
     */
    public function validate() {
        return [];
    }

}
