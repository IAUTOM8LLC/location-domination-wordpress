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
class Endpoint_Create_Posts {

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

        ini_set( 'pcre.jit', false );
        ini_set( 'memory_limit', - 1 );
        set_time_limit( 150 );

        // Remove filters to allow iframes and other needed elements
        remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
        remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
        remove_action( 'do_pings', 'do_all_pings' );
        remove_action( 'transition_post_status', '_update_term_count_on_transition_post_status', 10 );
        remove_action( 'transition_post_status', '_update_posts_count_on_transition_post_status', 10 );
        remove_action( 'post_updated', 'wp_save_post_revision', 10 );

        define( 'WP_IMPORTING', true );

        if ( ! defined( 'SAVEQUERIES' ) ) {
            define( 'SAVEQUERIES', false );
        }

        wp_defer_term_counting( true );
        wp_defer_comment_counting( true );
        wp_suspend_cache_addition( true );

        remove_action( 'do_pings', 'do_all_pings', 10, 1 );

        $slug = $this->get_template_slug( $request );

        // Check to see if template already exists...
        $template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $request->get_param( 'template-uuid' ) );

        if ( $template ) {
            wp_update_post( [
                'id'           => $template->ID,
                'post_type'    => LOCATION_DOMINATION_TEMPLATE_CPT,
                'post_name'    => $slug,
                'post_title'   => $request->get_param( 'template' ),
                'post_content' => $request->get_param( 'content' ),
                'post_status'  => 'publish',
            ] );
        } else {
            // Create a new template entry
            $template_ID = wp_insert_post( [
                'post_type'    => LOCATION_DOMINATION_TEMPLATE_CPT,
                'post_title'   => $request->get_param( 'template' ),
                'post_name'    => $slug,
                'post_content' => $request->get_param( 'content' ),
                'post_status'  => 'publish',
            ] );

            $template = get_post( $template_ID );

            update_post_meta( $template->ID, '_uuid', $request->get_param( 'template-uuid' ) );
        }

        // Clear existing posts
        $wpdb->delete( $wpdb->prefix . 'posts', [ 'post_type' => $request->get_param( 'template-slug' ) ] );

        // Update template meta
        update_post_meta( $template->ID, '_service_type', '2' );
        update_post_meta( $template->ID, '_service_title', $request->get_param( 'job_title' ) );
        update_post_meta( $template->ID, '_service_description', $request->get_param( 'job_description' ) );

        $post_content = apply_filters( 'location_domination_content_pre_spin', $request->get_param( 'content' ) );

        $meta = $request->get_param( 'meta' );

        if ( ! is_array( $meta ) ) {
            $meta = unserialize( base64_decode( $meta ) );
        }

        foreach ( $request->get_param( 'records' ) as $record ) {
            $shortcode_bindings = [
                '[city]'      => isset( $record[ 'city' ] ) ? $record[ 'city' ] : '',
                '[county]'    => isset( $record[ 'county' ] ) ? $record[ 'county' ] : '',
                '[state]'     => isset( $record[ 'state' ] ) ? $record[ 'state' ] : '',
                '[zips]'      => isset( $record[ 'zips' ] ) ? $record[ 'zips' ] : '',
                '[zip_codes]' => isset( $record[ 'zips' ] ) ? $record[ 'zips' ] : '',
                '[region]'    => isset( $record[ 'region' ] ) ? $record[ 'region' ] : '',
                '[country]'   => isset( $record[ 'country' ] ) ? $record[ 'country' ] : '',
            ];

            $title = apply_filters( 'location_domination_shortcodes', $request->get_param( 'title' ), $shortcode_bindings );
            $slug  = trim( $this->get_post_slug( $request, $shortcode_bindings ) );

            $arguments = [
                'post_type'    => $request->get_param( 'template-uuid' ),
                'post_name'    => $slug,
                'post_title'   => Location_Domination_Spinner::spin( $title ),
                'post_content' => Location_Domination_Spinner::spin( $post_content ),
                'post_status'  => 'publish',
            ];

            $meta_title       = $this->get_parameter_with_shortcodes( $request, 'meta_title', $shortcode_bindings );
            $meta_description = $this->get_parameter_with_shortcodes( $request, 'meta_description', $shortcode_bindings );
            $job_title        = $this->get_parameter_with_shortcodes( $request, 'job_title', $shortcode_bindings );
            $job_description  = $this->get_parameter_with_shortcodes( $request, 'job_description', $shortcode_bindings );
            $schema           = $this->get_parameter_with_shortcodes( $request, 'schema', $shortcode_bindings );

//            $post = $this->find_post( $request, $record );

//            if ( $post ) {
//                $arguments[ 'ID' ] = $post->ID;
//
//                wp_update_post( $arguments );
//            } else {
            $post_ID = wp_insert_post( $arguments );

            $arguments[ 'ID' ] = $post_ID;
//            }

            $wpdb->query( 'SET autocommit = 0;' );

            $wpdb->delete( $wpdb->prefix . 'postmeta', [
                'post_id' => $arguments[ 'ID' ],
            ] );

            // GMB Vault integration
            if ( isset( $meta[ '_gmbvault_business_listing' ] ) && isset( $meta[ '_gmbvault_business_listing' ][0] ) ) {
                $meta[ '_gmbvault_business_listing' ][0] = (int) $meta[ '_gmbvault_business_listing' ][0];
            }

            Endpoint_Create_Posts::meta_spinner( $meta, $arguments[ 'ID' ] );

            add_post_meta( $arguments[ 'ID' ], '_city', isset( $record[ 'city' ] ) ? $record[ 'city' ] : '' );
            add_post_meta( $arguments[ 'ID' ], '_state', isset( $record[ 'state' ] ) ? $record[ 'state' ] : '' );
            add_post_meta( $arguments[ 'ID' ], '_county', isset( $record[ 'county' ] ) ? $record[ 'county' ] : '' );
            add_post_meta( $arguments[ 'ID' ], '_zips', isset( $record[ 'zips' ] ) ? $record[ 'zips' ] : '' );
            add_post_meta( $arguments[ 'ID' ], '_region', isset( $record[ 'region' ] ) ? $record[ 'region' ] : '' );
            add_post_meta( $arguments[ 'ID' ], '_country', isset( $record[ 'country' ] ) ? $record[ 'country' ] : '' );

            if ( isset( $schema ) && $schema ) {
                add_post_meta( $arguments[ 'ID' ], '_ld_schema', $schema );
            }

            if ( isset( $meta_title ) && $meta_title ) {
                add_post_meta( $arguments[ 'ID' ], '_yoast_wpseo_title', Location_Domination_Spinner::spin( $meta_title ) );
            }

            if ( isset( $meta_description ) && $meta_description ) {
                add_post_meta( $arguments[ 'ID' ], '_yoast_wpseo_metadesc', Location_Domination_Spinner::spin( $meta_description ) );
            }

            if ( isset( $job_title ) && $job_title ) {
                add_post_meta( $arguments[ 'ID' ], '_ld_job_title', $job_title );
            }

            if ( isset( $job_description ) && $job_description ) {
                add_post_meta( $arguments[ 'ID' ], '_ld_job_description', $job_description );
            }

            $wpdb->query( 'COMMIT;' );
        }

        // Add back the filters for any further processing
        add_filter( 'content_save_pre', 'wp_filter_post_kses' );
        add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );

        wp_defer_term_counting( false );
        wp_defer_comment_counting( false );
        wp_suspend_cache_addition( false );

//        Location_Domination_Admin::clear_permalinks();

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

    /**
     * Check the meta to type-detect and handle appropriately.
     *
     * @param $i
     *
     * @return array|mixed|object|string|string[]|null
     * @since 2.0.0
     */
    public static function meta_array_crawler( $i, $bindings = [] ) {
        if ( is_int( $i ) || is_float( $i ) || is_bool( $i ) || ( is_string( $i ) && strlen( $i ) < 10 && ! is_serialized( $i ) ) ) {
            return $i;
        }

        // Check for serializations
        if ( is_string( $i ) && is_serialized( $i ) ) {
            $unserialized = unserialize( $i );

            if ( is_array( $unserialized ) || is_object( $unserialized ) ) {
                return array_map_recursive( (array) $unserialized, function ( $item ) use ( $bindings ) {
                    return self::meta_array_crawler( $item, $bindings );
                } );
            }

            return $unserialized;
        }

        if ( is_string( $i ) && is_json( $i ) ) {
            $unserialized = json_decode( $i, true );

            if ( is_array( $unserialized ) || is_object( $unserialized ) ) {
                return array_map_recursive( $unserialized, function ( $item ) use ( $bindings ) {
                    return self::meta_array_crawler( $item, $bindings );
                } );
            }

            return $unserialized;
        }

        if ( is_object( $i ) ) {
            $object = (object) [];

            foreach ( get_object_vars( $i ) as $key => $item ) {
                $object->$key = self::meta_array_crawler( $item, $bindings );
            }

            return $object;
        }

        if ( is_array( $i ) ) {
            $unserialized = (array) $i;

            if ( is_array( $unserialized ) || is_object( $unserialized ) ) {
                return array_map_recursive( $unserialized, function ( $item ) use ( $bindings ) {
                    return self::meta_array_crawler( $item, $bindings );
                } );
            }

            return $unserialized;
        }

        return apply_filters( 'location_domination_shortcodes', Location_Domination_Spinner::spin( $i ), $bindings );
    }

    /**
     * Recursively spins the meta data and imports
     * them into the database.
     *
     * @param $meta
     * @param $post_ID
     *
     * @since 2.0.0
     */
    public static function meta_spinner( $meta, $post_ID, $bindings = [] ) {
        global $wpdb;

        foreach ( $meta as $key => $value ) {
            if ( is_array( $value ) && count( $value ) > 0 ) {
                foreach ( $value as $i => $v ) {
                    if ( is_string( $v ) || is_int( $v ) ) {
                        // Check for serializations
                        $value         = self::meta_array_crawler( $v, $bindings );
                        $prepped_value = null;

                        // Return to previous states
                        if ( is_serialized( $v ) ) {
                            $prepped_value = serialize( $value );
                        }

                        if ( is_json( $v ) ) {
                            $prepped_value = json_encode( $value );
                        }

                        if ( is_beaverbuilder_installed() ) {
                            if ( preg_match( '/"\d+"/s', $v ) !== false ) {
                                $prepped_value = (int) trim( $v, '"' );
                            }

                            if ( strpos( $key, '_css' ) !== false ) {
                                $prepped_value = $v;
                            }

                            if ( is_array( $value ) && ! $prepped_value ) {
                                Endpoint_Create_Posts::meta_spinner( $value, $post_ID, $bindings );
                                continue;
                            }
                        }

                        // Does Oxygen builder exist?
                        if ( function_exists( 'oxygen_can_activate_builder_compression' ) ) {
                            if ( strpos( $key, 'ct_builder_shortcodes' ) !== false ) {
                                $prepped_value = $value;

                                foreach ( $bindings as $_key => $replacement ) {
                                    $_key = str_replace( '[', '_OXY_OPENING_BRACKET_', $_key );
                                    $_key = str_replace( ']', '_OXY_CLOSING_BRACKET_', $_key );

                                    $prepped_value = str_replace( $_key, $replacement, $prepped_value );
                                }
                            }
                        }

                        $wpdb->insert( $wpdb->prefix . 'postmeta', array(
                            'post_id'    => $post_ID,
                            'meta_key'   => $key,
                            'meta_value' => $prepped_value ? : $value,
                        ) );
                    }
                }
            }
        }
    }

    /**
     * Use the specified slug, or generate one using the title.
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|string|null
     * @since 2.0.0
     */
    protected function get_template_slug( WP_REST_Request $request ) {
        $title = $request->get_param( 'template' );

        return $request->get_param( 'template_slug' ) ? : sanitize_title_with_dashes( $title );
    }

    /**
     * Use the specified slug, or generate one using the title.
     *
     * @param \WP_REST_Request $request
     * @param array            $bindings
     *
     * @return mixed|string|null
     * @since 2.0.0
     */
    protected function get_post_slug( WP_REST_Request $request, $bindings = [] ) {
        $title = $request->get_param( 'title' );
        $slug  = $request->get_param( 'slug' ) ? : sanitize_title_with_dashes( $title );
        $slug  = str_replace( ' ', '-', $slug );

        return strtolower( apply_filters( 'location_domination_shortcodes', $slug, $bindings ) );
    }

    /**
     * Get a parameter and replace with the shortcode bindings.
     *
     * @param \WP_REST_Request $request
     * @param string           $key
     * @param array            $bindings
     *
     * @return mixed|string|null
     * @since 2.0.0
     */
    protected function get_parameter_with_shortcodes( WP_REST_Request $request, $key, $bindings = [] ) {
        $value = $request->get_param( $key );

        return apply_filters( 'location_domination_shortcodes', $value, $bindings );
    }

    /**
     * Find a post if it exists.
     *
     * @param \WP_REST_Request $request
     * @param array            $record
     *
     * @return int|\WP_Post|null
     * @since 2.0.0
     */
    protected function find_post( WP_REST_Request $request, $record = [] ) {
        $arguments = [
            'post_type'   => $request->get_param( 'template-uuid' ),
            'post_status' => 'publish',
            'numberposts' => 1,
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'key'     => '_city',
                    'value'   => $record[ 'city' ],
                    'compare' => '=',
                ),
                array(
                    'key'     => '_state',
                    'value'   => $record[ 'state' ],
                    'compare' => '=',
                ),
                array(
                    'key'     => '_county',
                    'value'   => $record[ 'county' ],
                    'compare' => '=',
                ),
            ),
        ];

        $posts = get_posts( $arguments );

        return isset( $posts[ 0 ] ) ? $posts[ 0 ] : null;
    }
}
