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
    public function authorize(WP_REST_Request $request)
    {
        return trim(get_option(LOCATION_DOMINATION_API_OPTION_KEY)) === trim($request->get_param('api_key'));
    }

    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
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
    public function handle(WP_REST_Request $request)
    {
        $payload = $request->get_params();
        $params = $payload['form_params'];
        $arguments = $params['arguments'];
        $ld_post = $arguments['post'];
        $post_id = $params['post_id'];

        // Get the post object
        $post = get_post($post_id);

        // Check if post exists
        if (!$post) {
            return rest_ensure_response(['success' => false, 'message' => 'Could not find post']);
        }
        add_post_meta($post_id, 'aifiller', $ld_post['generated_content']);

        $post_data = array(
            'ID'            => $post_id,
            'post_title'    => $ld_post['post_title'],
            'post_status'   => $post->post_status === 'draft' ? 'publish' : $post->post_status
        );

        // Update the post itself
        $result = wp_update_post($post_data);

        if (is_wp_error($result)) {
            return rest_ensure_response(['success' => false, 'message' => 'WP Error']);
        }

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
