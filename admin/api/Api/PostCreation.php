<?php

namespace App\Api;

use bjoernffm\Spintax\Parser;
use MadeITBelgium\Spintax\Spintax;
use MadeITBelgium\Spintax\SpintaxFacade;
use WP_REST_Controller;

/**
 * REST_API Handler
 */
class PostCreation extends WP_REST_Controller {

    /**
     * @var string
     */
    protected $namespace = "location-domination/v1";

    protected $spintax;

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
            '/insert-indexes',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'insert_indexes' ),
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
            $response = new \WP_REST_Response([
                'success' => true,
            ]);
        } else {
            $response = new \WP_REST_Response([
                'success' => false,
                'message' => 'API Key was incorrect.',
            ]);
        }

        $response = rest_ensure_response( $response );

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

    function preg_errtxt($errcode)
    {
        static $errtext;

        if (!isset($errtxt))
        {
            $errtext = array();
            $constants = get_defined_constants(true);
            foreach ($constants['pcre'] as $c => $n) if (preg_match('/_ERROR$/', $c)) $errtext[$n] = $c;
        }

        return array_key_exists($errcode, $errtext)? $errtext[$errcode] : NULL;
    }

    public function meta_array_crawler($i) {
        $spinner = $this->spintax;

        if ( is_int( $i ) || is_float( $i ) || is_bool( $i ) || strlen($i) < 10 ) {
            return $i;
        }

        // Check for serilizations
        if ( is_serialized( $i ) ) {
            $unserialized = unserialize($i);

            if ( is_array( $unserialized ) || is_object( $unserialized ) ) {
                return array_map_recursive( (array) $unserialized, function($item) {
                    return $this->meta_array_crawler($item);
                });
            }

            return $unserialized;
        }

        if ( is_json( $i ) ) {
            $unserialized = json_decode($i, true);

            if ( is_array( $unserialized ) || is_object( $unserialized ) ) {
                return array_map_recursive( $unserialized, function ( $item ) {
                    return $this->meta_array_crawler( $item );
                } );
            }

            return $unserialized;
        }

        return $spinner->process($i);
    }

    public function meta_spinner($meta, $post_ID) {
        global $wpdb;

        foreach ( $meta as $key => $value ) {
            if( is_array($value) && count($value) > 0 ) {
                foreach( $value as $i=>$v ) {
                    if ( is_string( $v ) ) {
                        // Check for serilizations
                        $value = $this->meta_array_crawler($v);
                        $prepped_value = null;

                        if ( is_serialized($v)) {
                           $prepped_value = serialize($value);
                        }

                        if ( is_json($v)) {
                           $prepped_value = json_encode($value);
                        }

                        $wpdb->insert( $wpdb->prefix.'postmeta', array(
                            'post_id' => $post_ID,
                            'meta_key' => $key,
                            'meta_value' => $prepped_value ?: $value,
                        ));
                    }
                }
            }
        }
    }

    /**
     * @param $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function insert_posts( $request ) {
        global $wpdb;

        ini_set("pcre.jit", "0");

        if ( trim( get_option( 'mpb_api_key' ) ) === trim( $request->get_param( 'api_key' ) ) ) {
            remove_filter('content_save_pre', 'wp_filter_post_kses');
            remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');

            ini_set( "memory_limit", - 1 );
            set_time_limit( 0 );
            ignore_user_abort( true );

            $template_slug = $request->get_param( 'template-slug' ) ?: sanitize_title_with_dashes($request->get_param('template'));

            if ( $template_slug ) {
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

                    wp_update_post( [
                        'id' => $my_posts[0]->ID,
                        'post_type'    => 'mptemplates',
                        'post_title'   => $request->get_param( 'template' ),
                        'post_name'    => $request->get_param( 'template-slug' ),
                        'post_content' => $request->get_param( 'content' ),
                        'post_status' => 'publish',
                    ]);
                }
            }
            $content = $request->get_param('content');
            $content = str_ireplace('<p>{</p>', '{', $content);
            $content = str_ireplace('<p>}</p>', '}', $content);

            $this->spintax = new \Spintax();

            $_meta = $request->get_param('meta');
            $taxonomies = $request->get_param('taxonomies');
            $full_post = $request->get_param('full_post');

            if( !is_array( $_meta ) ) {
                $_meta = unserialize( base64_decode( $_meta ) );
            }

            if( !is_array( $taxonomies ) ) {
                $taxonomies = unserialize( base64_decode( $taxonomies ) );
            }

            foreach ( $request->get_param( 'records' ) as $record ) {
                $city   = $record[ 'city' ];
                $county = $record[ 'county' ];
                $state  = $record[ 'state' ];
                $zips  = $record[ 'zips' ];
                $region = isset($record['region']) ? $record['region'] : '';
                $country = $record['country'];
                $meta = $_meta;

                $title = $request->get_param( 'title' );

                $title = str_ireplace( '[city]', $city, $title );
                $title = str_ireplace( '[county]', $county, $title );
                $title = str_ireplace( '[state]', $state, $title );
                $title = str_ireplace( '[zips]', $zips, $title );
                $title = str_ireplace( '[region]', $region, $title );
                $title = str_ireplace( '[country]', $country, $title );


                $post = [
                    'post_type'    => $request->get_param( 'template-uuid' ),
                    'post_title'   => $this->spintax->process($title),
                    'post_content' => $this->spintax->process( $request->get_param('content') ),
                    'post_status'  => 'publish'
                ];

                if ( $slug = $request->get_param( 'slug' ) ) {
                    $slug = str_ireplace( '[city]', $city, $slug );
                    $slug = str_ireplace( '[county]', $county, $slug );
                    $slug = str_ireplace( '[state]', $state, $slug );
                    $slug = str_ireplace( '[zips]', $zips, $slug );
                    $slug = str_ireplace( '[region]', $region, $slug );
                    $slug = str_ireplace( '[country]', $country, $slug );
                    $slug = str_ireplace( ' ', '-', $slug );

                    $post[ 'post_name' ] = strtolower($slug);
                }

                if ( $meta_title = $request->get_param( 'meta_title' ) ) {
                    $meta_title = str_ireplace( '[city]', $city, $meta_title );
                    $meta_title = str_ireplace( '[county]', $county, $meta_title );
                    $meta_title = str_ireplace( '[state]', $state, $meta_title );
                    $meta_title = str_ireplace( '[zips]', $zips, $meta_title );
                    $meta_title = str_ireplace( '[region]', $region, $meta_title );
                    $meta_title = str_ireplace( '[country]', $country, $meta_title );
                }

                if ( $meta_description = $request->get_param( 'meta_description' ) ) {
                    $meta_description = str_ireplace( '[city]', $city, $meta_description );
                    $meta_description = str_ireplace( '[county]', $county, $meta_description );
                    $meta_description = str_ireplace( '[state]', $state, $meta_description );
                    $meta_description = str_ireplace( '[zips]', $zips, $meta_description );
                    $meta_description = str_ireplace( '[country]', $country, $meta_description );
                    $meta_description = str_ireplace( '[region]', $region, $meta_description );
                }

                if ( $job_title = $request->get_param( 'job_title' ) ) {
                    $job_title = str_ireplace( '[city]', $city, $job_title );
                    $job_title = str_ireplace( '[county]', $county, $job_title );
                    $job_title = str_ireplace( '[state]', $state, $job_title );
                    $job_title = str_ireplace( '[zips]', $zips, $job_title );
                    $job_title = str_ireplace( '[region]', $region, $job_title );
                    $job_title = str_ireplace( '[country]', $country, $job_title );
                }

                if ( $job_description = $request->get_param( 'job_description' ) ) {
                    $job_description = str_ireplace( '[city]', $city, $job_description );
                    $job_description = str_ireplace( '[county]', $county, $job_description );
                    $job_description = str_ireplace( '[state]', $state, $job_description );
                    $job_description = str_ireplace( '[zips]', $zips, $job_description );
                    $job_description = str_ireplace( '[region]', $region, $job_description );
                    $job_description = str_ireplace( '[country]', $country, $job_description );
                }

                if ( $schema = $request->get_param( 'schema' ) ) {
                    $schema = str_ireplace( '[city]', $city, $schema );
                    $schema = str_ireplace( '[county]', $county, $schema );
                    $schema = str_ireplace( '[state]', $state, $schema );
                    $schema = str_ireplace( '[zips]', $zips, $schema );
                    $schema = str_ireplace( '[country]', $country, $schema );
                    $schema = str_ireplace( '[region]', $region, $schema );
                }

                // Check if page already exists
                $args = array(
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

                $post = get_post($post_ID, 'ARRAY');

                if ( 0 !== $post_ID ) {
                    // Delete all existing meta for page
                    $wpdb->delete( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID
                    ]);

                    $this->meta_spinner($meta, $post_ID);

                    if ( $taxonomies ) {
                        foreach ( $taxonomies as $taxonomy => $terms ) {
                            wp_set_object_terms( $post_ID, $terms, $taxonomy );
                        }
                    }

                    $wpdb->insert( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID,
                        'meta_key' => '_city',
                        'meta_value' => $city,
                    ]);

                    $wpdb->insert( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID,
                        'meta_key' => '_state',
                        'meta_value' => $state,
                    ]);

                    $wpdb->insert( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID,
                        'meta_key' => '_county',
                        'meta_value' => $county,
                    ]);

                    $wpdb->insert( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID,
                        'meta_key' => '_zips',
                        'meta_value' => $zips,
                    ]);

                    $wpdb->insert( $wpdb->prefix.'postmeta', [
                        'post_id' => $post_ID,
                        'meta_key' => '_region',
                        'meta_value' => $region,
                    ]);

                    if ( isset( $schema ) && $schema ) {
                        $wpdb->insert( $wpdb->prefix.'postmeta', [
                            'post_id' => $post_ID,
                            'meta_key' => '_ld_schema',
                            'meta_value' => $schema,
                        ]);
                    }

                    if ( isset( $meta_title ) && $meta_title ) {
                        $wpdb->insert( $wpdb->prefix.'postmeta', [
                            'post_id' => $post_ID,
                            'meta_key' => '_yoast_wpseo_title',
                            'meta_value' => $this->spintax->process($meta_title),
                        ]);
                    }

                    if ( isset( $meta_description ) && $meta_description ) {
                        $wpdb->insert( $wpdb->prefix.'postmeta', [
                            'post_id' => $post_ID,
                            'meta_key' => '_yoast_wpseo_metadesc',
                            'meta_value' =>  $this->spintax->process($meta_description),
                        ]);
                    }

                    if ( isset( $job_title ) && $job_title ) {
                        $wpdb->insert( $wpdb->prefix.'postmeta', [
                            'post_id' => $post_ID,
                            'meta_key' => '_ld_job_title',
                            'meta_value' => $job_title,
                        ]);
                    }

                    if ( isset( $job_description ) && $job_description ) {
                        $wpdb->insert( $wpdb->prefix.'postmeta', [
                            'post_id' => $post_ID,
                            'meta_key' => '_ld_job_description',
                            'meta_value' => $job_description,
                        ]);
                    }
//                update_post_meta( $post_ID, 'spun_content',  );
                }
            }

            add_filter('content_save_pre', 'wp_filter_post_kses');
            add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
        }

        $response = rest_ensure_response( [ 'success' => true ] );

        return $response;
    }

    /**
     * @param $request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function insert_indexes( $request ) {
        ini_set("pcre.jit", "0");

        if ( trim( get_option( 'mpb_api_key' ) ) === trim( $request->get_param( 'api_key' ) ) ) {
            ini_set( "memory_limit", - 1 );
            set_time_limit( 0 );
            ignore_user_abort( true );

            foreach ( $request->get_param('records') as $record) {
                $args = array(
                    'post_type'   => $request->get_param( 'template-uuid' ),
                    'post_title' => $record['region'],
                    'post_status' => 'publish',
                    'numberposts' => 1,
                );

                $matched_post = get_posts( $args );

                if ( !$matched_post ) {
                    $post_ID = wp_insert_post([
                        'post_type' => $request->get_param( 'template-uuid' ),
                        'post_title' => $record['region'],
                        'post_status' => 'publish',
                        'post_content' => sprintf('[internal_links region="%s"]', $record['region']),
                    ]);

                    update_post_meta( $post_ID, '_region_index', $record['country']);
                }
            }

            // for country
            $args = array(
                'post_type'   => $request->get_param( 'template-uuid' ),
                'post_title' => $record['country'],
                'post_status' => 'publish',
                'numberposts' => 1,
            );

            $matched_post = get_posts( $args );

            if ( !$matched_post ) {
                wp_insert_post( [
                    'post_type'    => $request->get_param( 'template-uuid' ),
                    'post_title'   => $record[ 'country' ],
                    'post_status'  => 'publish',
                    'post_content' => sprintf( '[internal_links country="%s"]', $record[ 'country' ] ),
                ] );
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
