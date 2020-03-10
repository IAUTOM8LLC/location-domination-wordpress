<?php

namespace App\Api;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class PostCreation extends WP_REST_Controller {

    /**
     * @var string
     */
    protected $namespace = "location-domination/v1";

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/ping',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'no_auth_ping' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );

        register_rest_route(
            $this->namespace,
            '/auth-ping',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );

        register_rest_route(
            $this->namespace,
            '/insert-posts',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'insert_posts' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );

        register_rest_route(
            $this->namespace,
            '/destruction',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'destruction' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function no_auth_ping( $request ) {
        $response = rest_ensure_response( [
            'version' => LOCATION_DOMINATION_VER,
        ] );

        return $response;
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items( $request ) {
        if ( trim( get_option( 'mpb_api_key' ) ) === trim( $request->get_param( 'api_key' ) ) ) {
            $items = [
                'success' => true
            ];
        } else {
            $items = [
                'success' => false,
                'message' => 'API key was incorrect',
            ];
        }

        $response = rest_ensure_response( $items );

        return $response;
    }

    /**
     * @return bool
     */
    public function is_performance_muplugin_installed() {
        $mu_dir    = ( defined( 'WPMU_PLUGIN_DIR' ) && defined( 'WPMU_PLUGIN_URL' ) ) ? WPMU_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins';
        $mu_dir    = untrailingslashit( $mu_dir );
        $mu_plugin = $mu_dir . '/ld-redirects.php';

        return file_exists( $mu_plugin );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function destruction( $request ) {
        if ( trim( get_option( 'mpb_api_key' ) ) === trim( $request->get_param( 'api_key' ) ) ) {
            $items = [
                'success' => true
            ];

            // destruct
            $post_type_query = new \WP_Query(
                array(
                    'post_type'      => 'mptemplates',
                    'posts_per_page' => - 1
                )
            );

            $posts_array      = $post_type_query->posts;
            $post_title_array = wp_list_pluck( $posts_array, 'post_name', 'ID' );

            update_option( 'ld_redirect_posttypes', $post_title_array );

            $mu_dir = ( defined( 'WPMU_PLUGIN_DIR' ) && defined( 'WPMU_PLUGIN_URL' ) ) ? WPMU_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins';
            $mu_dir = untrailingslashit( $mu_dir );
            $source = LOCATION_DOMINATION_ROOT_DIR . '/mu-plugins/ld-redirects.php';
            $dest   = $mu_dir . '/ld-redirects.php';

            if ( ! $this->is_performance_muplugin_installed() ) {
                // INSTALL
                if ( ! wp_mkdir_p( $mu_dir ) ) {
                    $items = [ 'success' => false, 'message' => 'Could not create directory' ];
                }

                if ( ! copy( $source, $dest ) ) {
                    $items = [ 'success' => false, 'message' => 'Could not create directory' ];
                } else {
                    $items = [ 'success' => true ];
                }
            }

            if ( file_exists( WP_PLUGIN_DIR . '/location-domination/location-domination.php' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                delete_plugins( array( 'location-domination/location-domination.php' ) );
            }
        } else {
            $items = [
                'success' => false,
                'message' => 'API key was incorrect',
            ];
        }

        $response = rest_ensure_response( $items );

        return $response;
    }

    /**
     * @param $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function insert_posts( $request ) {
        if ( trim( get_option( 'mpb_api_key' ) ) === trim( $request->get_param( 'api_key' ) ) ) {
            ini_set( "memory_limit", - 1 );
            set_time_limit( 0 );
            ignore_user_abort( true );

            if ( $request->get_param( 'template-slug' ) ) {
                $args = array(
                    'name'        => $request->get_param( 'template-slug' ),
                    'post_type'   => 'mptemplates',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );

                $my_posts = get_posts( $args );

                if ( ! $my_posts ) {
                    // add in to existing templates
                    $post_ID = wp_insert_post( [
                        'post_type'    => 'mptemplates',
                        'post_title'   => $request->get_param( 'template' ),
                        'post_name'    => $request->get_param( 'template-slug' ),
                        'post_content' => $request->get_param( 'content' ),
                        'post_status'  => 'publish',
                    ] );

                    // flush permalinks
                    update_option( 'mpb_flush_permalinks', 1 );
                    update_post_meta( $post_ID, '_service_type', '2' );
                    update_post_meta( $post_ID, '_service_title', $request->get_param( 'job_title' ) );
                    update_post_meta( $post_ID, '_service_description', $request->get_param( 'job_description' ) );

                    // set UUID
                    update_post_meta( $post_ID, '_uuid', $request->get_param( 'template-uuid' ) );
                } else {
                    update_post_meta( $my_posts[0]->ID, '_service_type', '2' );
                    update_post_meta( $my_posts[0]->ID, '_service_title', $request->get_param( 'job_title' ) );
                    update_post_meta( $my_posts[0]->ID, '_service_description', $request->get_param( 'job_description' ) );
//                wp_update_post( [
//                    'id' => $my_posts[0]->ID,
//                    'post_type'    => 'mptemplates',
//                    'post_title'   => $request->get_param( 'template' ),
//                    'post_name'    => $request->get_param( 'template-slug' ),
//                    'post_content' => $request->get_param( 'content' ),
//                    'post_status' => 'publish',
//                ]);
                }
            }

            foreach ( $request->get_param( 'records' ) as $record ) {
                $city   = $record[ 'city' ];
                $county = $record[ 'county' ];
                $state  = $record[ 'state' ];

                $title = $request->get_param( 'title' );

                $title = str_replace( '[city]', $city, $title );
                $title = str_replace( '[county]', $county, $title );
                $title = str_replace( '[state]', $state, $title );

                $spintax = new \Spintax();

                $post = [
                    'post_type'    => $request->get_param( 'template-uuid' ),
                    'post_title'   => $title,
                    'post_content' => $spintax->process( $request->get_param( 'content' ) ),
                    'post_status'  => 'publish'
                ];

                if ( $slug = $request->get_param( 'slug' ) ) {
                    $slug = str_replace( '[city]', $city, $slug );
                    $slug = str_replace( '[county]', $county, $slug );
                    $slug = str_replace( '[state]', $state, $slug );
                    $slug = str_replace( ' ', '-', $slug );

                    $post[ 'post_name' ] = strtolower($slug);
                }

                if ( $meta_title = $request->get_param( 'meta_title' ) ) {
                    $meta_title = str_replace( '[city]', $city, $meta_title );
                    $meta_title = str_replace( '[county]', $county, $meta_title );
                    $meta_title = str_replace( '[state]', $state, $meta_title );
                }

                if ( $meta_description = $request->get_param( 'meta_description' ) ) {
                    $meta_description = str_replace( '[city]', $city, $meta_description );
                    $meta_description = str_replace( '[county]', $county, $meta_description );
                    $meta_description = str_replace( '[state]', $state, $meta_description );
                }

                if ( $job_title = $request->get_param( 'job_title' ) ) {
                    $job_title = str_replace( '[city]', $city, $job_title );
                    $job_title = str_replace( '[county]', $county, $job_title );
                    $job_title = str_replace( '[state]', $state, $job_title );
                }

                if ( $job_description = $request->get_param( 'job_description' ) ) {
                    $job_description = str_replace( '[city]', $city, $job_description );
                    $job_description = str_replace( '[county]', $county, $job_description );
                    $job_description = str_replace( '[state]', $state, $job_description );
                }

                if ( $schema = $request->get_param( 'schema' ) ) {
                    $schema = str_replace( '[city]', $city, $schema );
                    $schema = str_replace( '[county]', $county, $schema );
                    $schema = str_replace( '[state]', $state, $schema );
                }

                // Check if page already exists
                $args = array(
                    'name'        => isset( $slug ) && $slug ? $slug : sanitize_title( $title ),
                    'post_type'   => $request->get_param( 'template-uuid' ),
                    'post_status' => 'publish',
                    'numberposts' => 1,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => '_city',
                            'value'   => $city,
                            'compare' => '=',
                        ),
                        array(
                            'key'     => '_state',
                            'value'   => $state,
                            'compare' => '=',
                        ),
                        array(
                            'key'     => '_county',
                            'value'   => $county,
                            'compare' => '=',
                        ),
                    )
                );

                $matched_post = get_posts( $args );

                if ( $matched_post ) {
                    $post_ID = $matched_post[0]->ID;
                    $post[ 'ID' ] = $post_ID;

                    wp_update_post( $post );
                } else {
                    $post_ID = wp_insert_post( $post );
                }

                if ( 0 !== $post_ID ) {
                    update_post_meta( $post_ID, '_city', $city );
                    update_post_meta( $post_ID, '_state', $state );
                    update_post_meta( $post_ID, '_county', $county );

                    if ( isset( $schema ) && $schema ) {
                        update_post_meta( $post_ID, '_ld_schema', $schema );
                    }

                    if ( isset( $meta_title ) && $meta_title ) {
                        update_post_meta( $post_ID, '_yoast_wpseo_title', $meta_title );
                    }

                    if ( isset( $meta_description ) && $meta_description ) {
                        update_post_meta( $post_ID, '_yoast_wpseo_metadesc', $meta_description );
                    }

                    if ( isset( $job_title ) && $job_title ) {
                        update_post_meta( $post_ID, '_ld_job_title', $job_title );
                    }

                    if ( isset( $job_description ) && $job_description ) {
                        update_post_meta( $post_ID, '_ld_job_description', $job_description );
                    }
//                update_post_meta( $post_ID, 'spun_content',  );
                }
            }
        }

        $response = rest_ensure_response( [ 'success' => true ] );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
        return true;
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}
