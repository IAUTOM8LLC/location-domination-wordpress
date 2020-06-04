<?php

/**
 * Interface for endpoints.
 *
 * @link       https://i-autom8.com
 * @since      2.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/rest
 */
interface Endpoint_Interface {

    /**
     * @param \WP_REST_Request $request
     *
     * @return boolean
     */
    public function authorize(WP_REST_Request $request);

    /**
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function handle(WP_REST_Request $request);

    /**
     * @return mixed
     *
     * @since 2.0.0
     */
    public function validate();

}