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
class Endpoint_Ping implements Endpoint_Interface {

    /**
     * Always return true as we do not require verification.
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
     * working correctly. No authentication is required for this
     * verification.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @since 2.0.0
     */
    public function handle(WP_REST_Request $request) {
        $response = rest_ensure_response( [
            'version' => LOCATION_DOMINATION_VERSION,
        ] );

        return $response;
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