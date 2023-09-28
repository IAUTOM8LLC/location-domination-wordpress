<?php

/**
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 */

/**
 * @package    Location_Domination
 * @subpackage Location_Domination/admin
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Action_Process_Queue implements Action_Interface {

    static $LOCATION_DOMINATION_PROGRESS_KEY = 'location-domination-progress';

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'location_domination_process_queue';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle() {
        $start       = microtime( true );
        $template_id = (int) $_POST[ 'template' ];

        if ( get_field( 'disable_jit', $template_id ) ) {
            ini_set( 'pcre.jit', false );
        }

        $option = get_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id );

        if ( ! $option ) {
            return wp_send_json( [ 'success' => false, 'message' => 'You have no active jobs running.' ] );
        }

        /**
         * Checks to see whether we have any jobs in progress and
         * have not timed-out.
         */
        if ( isset( $option->job_in_progress, $option->last_job_started_at ) && $option->job_in_progress && $option->last_job_started_at > strtotime( '-3 minutes' ) ) {
            return wp_send_json( [ 'success' => false, 'in_progress' => true, 'progress' => $option->progress ] );
        }

        if ( isset( $option->progress ) && $option->progress >= 100 ) {
            return wp_send_json( [ 'success' => true, 'completed' => true, 'progress' => 100 ] );
        }

        global $wpdb;

        require_once( __DIR__ . '/../../includes/class-location-domination-activator.php' );

        $fields = get_fields( $template_id );

        $enabled_templates = array_filter( $fields[ 'spin_templates' ] ? : [], function ( $item ) {
            return $item[ 'enabled' ];
        } );

        $enabled_templates_ids = array_map( function ( $item ) {
            return $item[ 'template' ];
        }, $enabled_templates );

        $sub_template_spinning = count( $enabled_templates_ids ) > 0;

        // Start batching responses
        $last_post_request_id = (int) $option->request_id;
        $url                  = sprintf( '%s/api/post-requests/%d/workload?batch=%d', trim( MAIN_URL, '/' ), $last_post_request_id, $option->batches->completed );

        $response = wp_remote_get( $url,array(
            'timeout' => 1000, // Set timeout to 10 seconds
        ) );
        if ( ! is_wp_error( $response ) ) {
            $json_response = json_decode( $response[ 'body' ] );
        // print_r($json_response);
            $option->job_in_progress     = true;
            $option->last_job_started_at = time();

            set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id, $option, 0 );

            $page_title = isset( $fields[ 'page_title' ] ) ? $fields[ 'page_title' ] : null;
            $page_slug  = isset( $fields[ 'page_slug' ] ) ? $fields[ 'page_slug' ] : null;
            $page_title_sc = $this->extract_short_code_attr($page_title);
            $page_slug_sc = $this->extract_short_code_attr($page_slug);
            $template_post_type = get_post_meta( $template_id, '_uuid', true );

            foreach ( $json_response->cities as $record ) {
                if (isset($fields['create_base_on_population']) && $fields['create_base_on_population'] == 1) {
                    if (!isset($fields['population_range']) || ( $record->city_meta->population < $fields['population_range']) || $record->city_meta == null) {
                        continue(1);
                    }
                }
                $random_template_key    = $sub_template_spinning ? array_rand( $enabled_templates_ids ) : false;
                $base_template_id       = $sub_template_spinning && $random_template_key ? $enabled_templates_ids[ $random_template_key ] : $template_id;
                $base_template          = get_post( $base_template_id, 'ARRAY_A' );
                $base_template_settings = $sub_template_spinning ? $enabled_templates[ $random_template_key ] : false;
                $meta = get_post_custom( $base_template_id );

                $locked_query_args = [
                    'post_status' => 'publish',
                    'post_type' => $template_post_type,
                    'posts_per_page' => -1,
                    'meta_query' => [
                        'relation' => 'AND',
                        [
                            'key' => 'lock_page',
                            'value' => '1',
                            'compare' => '=',
                        ],
                        [
                            'key' => '_city',
                            'value' => isset( $record->city ) ? $record->city : '',
                            'compare' => '=',
                        ],
                        [
                            'key' => '_state',
                            'value' => isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : '' ),
                            'compare' => '=',
                        ],
                        [
                            'key' => '_county',
                            'value' => isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : '' ),
                            'compare' => '=',
                        ],
                        [
                            'key' => '_country',
                            'value' => isset( $record->country ) ? $record->country : '',
                            'compare' => '=',
                        ],
                    ]
                ];

                $locked_query = new \WP_Query( $locked_query_args );

                if ( $locked_query->post_count > 0 ) {
                    continue;
                }

                $region_abbr     = '';
                $region_abbr_key = 'region-abbr';

                if ( isset( $record->$region_abbr_key ) ) {
                    $region_abbr = str_replace( '-', '', $record->$region_abbr_key );
                }

                $shortcode_bindings = [
                    '[city]'       => isset( $record->city ) ? $record->city : '',
                    '[county]'     => isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : '' ),
                    '[state]'      => isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : '' ),
                    '[zips]'       => isset( $record->zips ) ? $record->zips : '',
                    '[zip_codes]'  => isset( $record->zips ) ? $record->zips : '',
                    '[region]'     => isset( $record->region ) ? $record->region : '',
                    '[country]'    => isset( $record->country ) ? $record->country : '',
                    '[state_abbr]' => isset( $record->state ) ? Shortcode_State_Abbreviation::lookup( $record->state ) : $region_abbr,
                    '[population]' => isset($record->city_meta->population) ? $record->city_meta->population : '',
                    '[lat]' => isset($record->city_meta->lat) ? $record->city_meta->lat : '',
                    '[lng]' => isset($record->city_meta->lng) ? $record->city_meta->lng : '',
                    '[density]' => isset($record->city_meta->density) ? $record->city_meta->density : '',
                    '[ranking]' => isset($record->city_meta->ranking) ? $record->city_meta->ranking : ''
                ];
                $shortcode_bindings_slug = $shortcode_bindings;
                $is_united_states = isset( $record->country ) ? $record->country === 'United States' : true;

                $shortcode_pattern = '/\[citymeta name="([^"]+)"\]/';

                $title = apply_filters( 'location_domination_shortcodes', ( $sub_template_spinning ? $base_template_settings[ 'post_name' ] : $base_template[ 'post_title' ] ), $shortcode_bindings );
                $uuid  = get_post_meta( $template_id, '_uuid', true );

                if ( ! $sub_template_spinning && $page_title ) {
                    $title = apply_filters( 'location_domination_shortcodes', $page_title, $shortcode_bindings );
                }
              if ($record->city_meta !== null) {
                    $dynamicValues = json_decode(json_encode($record->city_meta), true);
                } else {
                    // Handle the case where $record->city_meta->citymeta is null
                    $dynamicValues = array(); // Or any other default value or handling you prefer
                }
                if (isset($page_title_sc['citymeta']['name'])) {
                    // $dynamicValues = json_decode(json_encode($record->city_meta), true);;
                    $title = preg_replace_callback($shortcode_pattern, function($matches) use ($dynamicValues) {
                        $attrName = $matches[1];
                        if (array_key_exists($attrName, $dynamicValues)) {
                            return $dynamicValues[$attrName];
                        }
                        return $matches[0];
                    }, $title);

                }
                if (isset($page_slug_sc['citymeta']['name'])) {
                    // $dynamicValues = json_decode(json_encode($record->city_meta), true);;
                    $page_slug = preg_replace_callback($shortcode_pattern, function($matches) use ($dynamicValues) {
                        $attrName = $matches[1];
                        if (array_key_exists($attrName, $dynamicValues)) {
                            return $dynamicValues[$attrName];
                        }
                        return $matches[0];
                    }, $page_slug);
                }
                // print_r($page_slug);

                $arguments = [
                    'post_type'    => $template_post_type,
                    'post_title'   => Location_Domination_Spinner::spin( $title ),
                    'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] ),
                    'post_status'  => 'publish',
                ];

                if ( ! $sub_template_spinning && $page_title ) {
                    $arguments[ 'post_name' ] = apply_filters( 'location_domination_shortcodes', $page_slug, $shortcode_bindings );
                }

//                $meta_title       = $this->get_parameter_with_shortcodes( $request, 'meta_title', $shortcode_bindings );
//                $meta_description = $this->get_parameter_with_shortcodes( $request, 'meta_description', $shortcode_bindings );
//                $job_title        = $this->get_parameter_with_shortcodes( $request, 'job_title', $shortcode_bindings );
//                $job_description  = $this->get_parameter_with_shortcodes( $request, 'job_description', $shortcode_bindings );
//                $schema           = $this->get_parameter_with_shortcodes( $request, 'schema', $shortcode_bindings );


                $new_post_id = wp_insert_post( $arguments );

				// GMB Vault integration
	            if ( isset( $meta[ '_gmbvault_business_listing' ] ) && isset( $meta[ '_gmbvault_business_listing' ][0] ) ) {
		            $meta[ '_gmbvault_business_listing' ][0] = (int) $meta[ '_gmbvault_business_listing' ][0];
	            }

                Endpoint_Create_Posts::meta_spinner( $meta, $new_post_id, $shortcode_bindings );

                add_post_meta( $new_post_id, '_city', isset( $record->city ) ? $record->city : '' );
                add_post_meta( $new_post_id, '_state', isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : null ) );
                add_post_meta( $new_post_id, '_county', isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : null ) );
                add_post_meta( $new_post_id, '_zips', isset( $record->zips ) ? $record->zips : '' );
                add_post_meta( $new_post_id, '_region', isset( $record->region ) ? $record->region : '' );
                add_post_meta( $new_post_id, '_country', isset( $record->country ) ? $record->country : '' );
                add_post_meta( $new_post_id, '_population', isset($record->city_meta->population) ? $record->city_meta->population : '' );
                add_post_meta( $new_post_id, '_lat', isset($record->city_meta->lat) ? $record->city_meta->lat : '' );
                add_post_meta( $new_post_id, '_lng', isset($record->city_meta->lng) ? $record->city_meta->lng : '' );
                add_post_meta( $new_post_id, '_density', isset($record->city_meta->density) ? $record->city_meta->density : '' );
                add_post_meta( $new_post_id, '_ranking', isset($record->city_meta->ranking) ? $record->city_meta->ranking : '' );

                add_post_meta( $new_post_id, '_city_meta', isset($record->city_meta) ? json_encode($record->city_meta) : '' );
                if ( $region_abbr ) {
                    add_post_meta( $new_post_id, '_region_abbr', $region_abbr );
                }

                update_post_meta( $new_post_id, '_uuid', $uuid );

                if ( get_field( 'create_neighborhood_pages', $base_template_id ) ) {
                    if ( isset ( $record->neighborhoods ) ) {
                        foreach ( $record->neighborhoods as $neighborhood ) {
                            $neighborhood_shortcode_bindings             = $shortcode_bindings;
                            $neighborhood_shortcode_bindings[ '[city]' ] = $neighborhood->neighborhood;

                            $title = apply_filters( 'location_domination_shortcodes', ( $sub_template_spinning ? $base_template_settings[ 'post_name' ] : $base_template[ 'post_title' ] ), $neighborhood_shortcode_bindings );

                            if ( ! $sub_template_spinning && $page_title ) {
                                $title = apply_filters( 'location_domination_shortcodes', $page_title, $neighborhood_shortcode_bindings );
                            }

                            $arguments = [
                                'post_type'    => get_post_meta( $template_id, '_uuid', true ),
                                'post_title'   => Location_Domination_Spinner::spin( $title ),
                                'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] ),
                                'post_status'  => 'publish',
                                'post_parent'  => $new_post_id,
                            ];

                            if ( ! $sub_template_spinning && $page_title ) {
                                $arguments[ 'post_name' ] = apply_filters( 'location_domination_shortcodes', $page_slug, $neighborhood_shortcode_bindings );
                            }

                            $neighborhood_post_id = wp_insert_post( $arguments );

	                        // GMB Vault integration
	                        if ( isset( $meta[ '_gmbvault_business_listing' ] ) && isset( $meta[ '_gmbvault_business_listing' ][0] ) ) {
		                        $meta[ '_gmbvault_business_listing' ][0] = (int) $meta[ '_gmbvault_business_listing' ][0];
	                        }

                            Endpoint_Create_Posts::meta_spinner( $meta, $neighborhood_post_id, $neighborhood_shortcode_bindings );

                            add_post_meta( $neighborhood_post_id, '_neighborhood', $neighborhood );
                            add_post_meta( $neighborhood_post_id, '_city', isset( $record->city ) ? $record->city : '' );
                            add_post_meta( $neighborhood_post_id, '_state', isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : null ) );
                            add_post_meta( $neighborhood_post_id, '_county', isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : null ) );
                            add_post_meta( $neighborhood_post_id, '_zips', isset( $record->zips ) ? $record->zips : '' );
                            add_post_meta( $neighborhood_post_id, '_country', isset( $record->country ) ? $record->country : '' );
                            add_post_meta( $neighborhood_post_id, '_population', isset($record->city_meta->population) ? $record->city_meta->population : '' );
                            update_post_meta( $neighborhood_post_id, '_uuid', $uuid );
                        }
                    }
                } else {
                    if ( isset( $record->neighborhoods ) && ! empty( $record->neighborhoods ) ) {
                        $neighborhoods = array_map( function( $object ) {
                            return $object->neighborhood;
                        }, $record->neighborhoods );

                        add_post_meta( $new_post_id, "_neighborhoods", $neighborhoods );
                    }
                }
                // print_r($record);exit;
                if ( get_field( 'create_suburb_pages', $base_template_id ) ) {
                    if ( isset ( $record->suburbs ) ) {
                        foreach ( $record->suburbs as $suburb ) {
                            $suburb_shortcode_bindings             = $shortcode_bindings;
                            $suburb_shortcode_bindings[ '[city]' ] = $suburb->suburb;

                            $title = apply_filters( 'location_domination_shortcodes', ( $sub_template_spinning ? $base_template_settings[ 'post_name' ] : $base_template[ 'post_title' ] ), $suburb_shortcode_bindings );

                            if ( ! $sub_template_spinning && $page_title ) {
                                $title = apply_filters( 'location_domination_shortcodes', $page_title, $suburb_shortcode_bindings );
                            }

                            $arguments = [
                                'post_type'    => get_post_meta( $template_id, '_uuid', true ),
                                'post_title'   => Location_Domination_Spinner::spin( $title ),
                                'post_content' => Location_Domination_Spinner::spin( $base_template[ 'post_content' ] ),
                                'post_status'  => 'publish',
                                'post_parent'  => $new_post_id,
                            ];

                            if ( ! $sub_template_spinning && $page_title ) {
                                $arguments[ 'post_name' ] = apply_filters( 'location_domination_shortcodes', $page_slug, $suburb_shortcode_bindings );
                            }

                            $suburb_post_id = wp_insert_post( $arguments );

                        // GMB Vault integration
                            if ( isset( $meta[ '_gmbvault_business_listing' ] ) && isset( $meta[ '_gmbvault_business_listing' ][0] ) ) {
                                $meta[ '_gmbvault_business_listing' ][0] = (int) $meta[ '_gmbvault_business_listing' ][0];
                            }

                            Endpoint_Create_Posts::meta_spinner( $meta, $suburb_post_id, $suburb_shortcode_bindings );

                            add_post_meta( $suburb_post_id, '_suburb', $suburb );
                            add_post_meta( $suburb_post_id, '_city', isset( $record->city ) ? $record->city : '' );
                            add_post_meta( $suburb_post_id, '_state', isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : null ) );
                            add_post_meta( $suburb_post_id, '_county', isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : null ) );
                            add_post_meta( $suburb_post_id, '_zips', isset( $record->zips ) ? $record->zips : '' );
                            add_post_meta( $suburb_post_id, '_country', isset( $record->country ) ? $record->country : '' );
                            add_post_meta( $suburb_post_id, '_population', isset($record->city_meta->population) ? $record->city_meta->population : '' );
                            update_post_meta( $suburb_post_id, '_uuid', $uuid );
                        }
                    }
                } else {
                    if ( isset( $record->suburbs ) && ! empty( $record->suburbs ) ) {
                        $suburbs = array_map( function( $object ) {
                            return $object->suburb;
                        }, $record->suburbs );

                        add_post_meta( $new_post_id, "_suburbs", $suburbs );
                    }
                }
                $wpdb->insert( Location_Domination_Activator::getTableName(), [
                    'post_type' => $uuid,
                    'post_id'   => $new_post_id,
                    'country'   => isset( $record->country ) ? $record->country : null,
                    'state'     => isset( $record->state ) ? $record->state : ( isset( $record->region ) ? $record->region : null ),
                    'county'    => isset( $record->county ) ? $record->county : ( isset( $record->region ) ? $record->region : null ),
                    'region'    => isset( $record->region ) ? $record->region : ( isset( $record->county ) ? $record->county : null ),
                    'city'      => isset( $record->city ) ? $record->city : null,
                    'locked'    => 0,
                ] );

//                if ( isset( $schema ) && $schema ) {
//                    add_post_meta( $arguments[ 'ID' ], '_ld_schema', $schema );
//                }
//
//                $wpdb->query( 'COMMIT;' );
            }

            // Update transient to tell the next job it can continue
            $option->job_in_progress     = false;
            $option->last_job_started_at = false;
            $option->batches->completed ++;
            $option->progress = round( ( $option->batches->completed / $option->batches->needed ) * 100 );

            set_transient( Action_Process_Queue::$LOCATION_DOMINATION_PROGRESS_KEY . '_' . $template_id, $option, 0 );

            $execution_time           = microtime( true ) - $start;
            $batches_remaining        = $option->batches->needed - $option->batches->completed;
            $estimated_time_remaining = ( $batches_remaining + 10 ) * $execution_time;

            return wp_send_json( [
                'success'                  => true,
                'execution_time'           => ceil( $execution_time ),
                'progress'                 => $option->progress,
                'batches_left'             => $batches_remaining,
                'batches_remaining'        => $batches_remaining,
                'estimated_time_remaining' => ceil( $estimated_time_remaining ),
            ] );
        }

        return wp_send_json( [ 'success' => false ] );
    }

    public function extract_short_code_attr($string) {
        // $string = 'Manish [city]-[population]-[lat]-[density][citymeta name="lat"]';

        // Define the resulting array
        $result_array = array();

        // Define a regular expression pattern to match shortcodes and their attributes
        $pattern = '/\[(\w+)([^\]]*?)\]/';

        // Use preg_match_all to find all matches of the pattern in the string
        preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);

        // Iterate through the matches to extract shortcode information
        foreach ($matches as $match) {
            $shortcode_name = $match[1]; // Shortcode name
            $shortcode_attrs_str = $match[2]; // Shortcode attribute string (e.g., 'name="lat"')

            // Initialize the shortcode attributes array
            $shortcode_attrs = array();

            // Parse shortcode attributes into key-value pairs
            if (preg_match_all('/(\w+)="([^"]+)"/', $shortcode_attrs_str, $attr_matches, PREG_SET_ORDER)) {
                foreach ($attr_matches as $attr_match) {
                    $attr_key = $attr_match[1];
                    $attr_value = $attr_match[2];
                    $shortcode_attrs[$attr_key] = $attr_value;
                }
            }

            // Add the shortcode information to the result array, even if there are no attributes
            $result_array[$shortcode_name] = $shortcode_attrs;
        }

        // Print the resulting array
        return $result_array;

    }

}
