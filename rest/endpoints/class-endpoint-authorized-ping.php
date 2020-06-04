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
class Endpoint_Authorized_Ping implements Endpoint_Interface {

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
    public function authorize(WP_REST_Request $request) {
        return true;
    }

    /**
     * Responsible for showing that the plugin is active and
     * working correctly. Authentication is required for this
     * endpoint and it is used to verify that the API key is
     * correct.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @since 2.0.0
     */
    public function handle( WP_REST_Request $request ) {
        $matched = trim( get_option( LOCATION_DOMINATION_API_OPTION_KEY ) ) === trim( $request->get_param( 'api_key' ) );

        if ( $matched ) {
            $response = [ 'success' => true ];
        } else {
            $response = [ 'success' => false, 'message' => 'API Key was incorrect.' ];
        }

        return rest_ensure_response( $response );
    }

    /**
     * Return an empty collection.
     *
     * @return mixed|void
     */
    public function validate() {
        return [];
    }

}