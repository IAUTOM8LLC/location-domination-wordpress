<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination_Rest {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The namespace in which all of our routes reside.
     *
     * @since 2.0.0
     */
    const REST_NAMESPACE = 'location-domination/v1';

    /**
     * An array representation of our routes.
     *
     * @since  2.0.0
     * @access protected
     * @var array[]
     */
    protected $routes = [
        'ping' => [
            'methods' => WP_REST_Server::READABLE,
            'class'   => Endpoint_Ping::class,
        ],

        'auth-ping' => [
            'methods' => WP_REST_Server::READABLE,
            'class'   => Endpoint_Authorized_Ping::class,
        ],

        'insert-posts' => [
            'methods' => WP_REST_Server::CREATABLE,
            'class'   => Endpoint_Create_Posts::class,
        ]
    ];

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register all of the routes that we need for the
     * plugin to work.
     *
     * @since 2.0.0
     */
    public function register_routes() {
        foreach ( $this->routes as $path => $route ) {
            $class = new $route[ 'class' ];

            register_rest_route( self::REST_NAMESPACE, $path, [
                [
                    'methods'             => $route[ 'methods' ],
                    'callback'            => [ $class, 'handle' ],
                    'permission_callback' => [ $class, 'authorize' ],
                    'args'                => $class->validate(),
                ]
            ] );
        }
    }

}
