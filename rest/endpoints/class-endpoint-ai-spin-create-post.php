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
class Endpoint_AI_Spin_Create_Post {

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
        global $wpdb;
        $payload = $request->get_params();
        $arguments = $payload['arguments'];

        $post = $arguments['post'];
        $meta = $arguments['meta'];
        $filters = $arguments['filters'];
        $ld_activator = $arguments['LD_activator'];

        $post_name_filter = $filters['post_name'];

        apply_filters($post_name_filter['hook_name'], $post_name_filter['value'], $post_name_filter['args']);

        $new_post_id = wp_insert_post( $post );

        foreach($meta as $key => $value){
            if($key === 'meta') continue;
            if($key === 'meta_title'){
                $spin_meta_title = $meta['meta_title'];
                add_post_meta( $new_post_id, '_yoast_wpseo_title', $spin_meta_title );
                add_post_meta( $new_post_id, '_aioseo_title', $spin_meta_title );
                add_post_meta( $new_post_id, '_aioseo_og_title', $spin_meta_title );
                add_post_meta( $new_post_id, '_aioseo_twitter_title', $spin_meta_title );
                continue;
            }
            if($key === 'meta_description') {
                $spin_meta_description = $meta['meta_description'];
                add_post_meta( $new_post_id, '_yoast_wpseo_metadesc', $spin_meta_description );
                add_post_meta( $new_post_id, '_aioseo_description', $spin_meta_description );
                add_post_meta( $new_post_id, '_aioseo_og_description', $spin_meta_description );
                add_post_meta( $new_post_id, '_aioseo_twitter_description', $spin_meta_description );
                continue;
            }
            add_post_meta( $new_post_id, $key, $value);
        }

        activate_location_domination();
        $ld_activator['post_id'] = $new_post_id;
        $wpdb->insert( Location_Domination_Activator::getTableName(), $ld_activator );

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
