<?php


class mpbuilder_admin {

    /**
     * Maintains the current version of the plugin so that we can use it throughout
     * the plugin.
     *
     * @access private
     * @var    string $version The current version of the plugin.
     */

    private $version;

    public function __construct( $version ) {
        $this->version = $version;
        $this->api     = new LocationDominationAPI();


    }


    public function enqueue_admin_styles() {

        wp_enqueue_style(
            'mpbuilder-admin-style',
            plugin_dir_url( __FILE__ ) . 'css/admin.css',
            array(),
            $this->version,
            false
        );


        wp_enqueue_script(
            'mpbuilder-admin-js',
            plugin_dir_url( __FILE__ ) . 'js/admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

    }

    function mpbuilder_admin_page() {
        add_menu_page( 'Location Domination'
            , 'Location Domination',
            'manage_options',
            'location-domination-page.php',
            array( $this, 'render_admin_page' ),
            'dashicons-admin-generic',
            6 );
        add_submenu_page( null,
            'Location Domination Setup',
            'Setup',
            'manage_options',
            'mpbuilder-setup',
            array( $this, 'render_setup_page' ) );

    }

    function register_settings_options() {
        if ( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }

        //register our settings
        register_setting( 'mpb-settings-group', 'mpb_api_secret' );
        register_setting( 'mpb-settings-group', 'mpb_api_duration' );
        register_setting( 'mpb-settings-group', 'mpb_api_key' );
        register_setting( 'mpb-locations-group', 'mpb_location_type' );
    }

    public function render_admin_page() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/location-domination-page.php';
    }

    public function render_setup_page() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/pages/location-domination-setup.php';
    }

    public function mpbuilder_set_loc() {


        $results = $this->api->get_all_states();
        ?>
        <fieldset>
            <label for="selected_states">Select a State</label>
            <select name="selected_states" class="state_select" id="selected_states">
                <option value="0">Select a State</option>
                <?php
                foreach ( $results as $result ) {
                    echo '<option value="' . $result->id . '">' . $result->state . '</option>';
                }
                ?>
            </select>
        </fieldset>
        <?php
        die();
    }

    public function ld_update_apikey() {
        check_ajax_referrer( 'ld_nonce_verifier', '_nonce' );
    }

    public function mpbuilder_set_county() {
        $state_id = $_REQUEST[ "state_id" ];

        $results = $this->api->get_counties( $state_id );
        ?>
        <fieldset>
            <label for="selected_counties">Select Counties</label>
            <select name="selected_counties[]" class="county_select" id="selected_counties" multiple>

                <?php
                foreach ( $results as $result ) {
                    echo '<option value="' . $result->id . '">' . $result->county . '</option>';
                }
                ?>
            </select>
        </fieldset>
        <p>Note: A Page for Each City in Each County You Select Will Be Created On Submit. You May Repeat This Process
            as Many Times As Necessary.</p>
        <?php
        die();
    }

    public function mpbuilder_set_city() {
        $county = $_REQUEST[ "county_id" ];
        global $wpdb;
        $results = $wpdb->get_results( "SELECT name, id FROM cities where county_id = $county order by name asc", OBJECT );
        ?>
        <fieldset>
            <select name="selected_cities[]" class="city_select" multiple id="selected_cities">
                <?php
                foreach ( $results as $result ) {
                    echo '<option value="' . $result->id . '">' . $result->name . '</option>';
                }
                ?>
            </select>
        </fieldset>
        <?php
        die();
    }


    public function cpt_save_postdata( $post_ID ) {
        if ( wp_is_post_autosave( $post_ID ) ) {
            return;
        }
        if ( wp_is_post_revision( $post_ID ) ) {
            return;
        }
        if ( array_key_exists( 'by_option', $_POST ) ) {
            update_post_meta(
                $post_ID,
                '_by_option',
                $_POST[ 'by_option' ]
            );
        }
        if ( array_key_exists( 'selected_counties', $_POST ) ) {
            update_post_meta(
                $post_ID,
                '_selected_counties',
                $_POST[ 'selected_counties' ]
            );
        }
        if ( array_key_exists( 'selected_states', $_POST ) ) {
            update_post_meta(
                $post_ID,
                '_selected_states',
                $_POST[ 'selected_states' ]
            );
        }
        if ( array_key_exists( 'selected_cities', $_POST ) ) {

            update_post_meta(
                $post_ID,
                '_selected_cities',
                $_POST[ 'selected_cities' ]
            );
        }
        if ( array_key_exists( '_service_type', $_POST ) ) {

            update_post_meta(
                $post_ID,
                '_service_type',
                $_POST[ '_service_type' ]
            );
        }
        if ( array_key_exists( '_service_title', $_POST ) ) {

            update_post_meta(
                $post_ID,
                '_service_title',
                $_POST[ '_service_title' ]
            );
        }
        if ( array_key_exists( '_service_description', $_POST ) ) {

            update_post_meta(
                $post_ID,
                '_service_description',
                $_POST[ '_service_description' ]
            );
        }
    }

    public function add_upgrade_message() {
        if ( LOCATION_DOMINATION_VER === 1.56 ) {
            echo '<div class="notice notice-warning is-dismissible">
               <h3>Location Domination Upgrade Notice</h3>
             <p>We have recently made several updates to our plugin (and continue to!) In order to make sure that your post requests will continue to work, please head to the "Mass Page Templates" and click "Send To Location Domination" before you create a new post request or re-build an existing one.</p>
         </div>';
        }
    }

    public function add_mass_pages( $location ) {
        global $post;
        $title = get_the_title( $post->ID );
        if ( isset( $_POST[ 'save' ] ) || isset( $_POST[ 'publish' ] ) ) { //Redirect to Setup page for Mass Creation of
            // Pages
            // add check for LD template
            if ( get_post_type() == 'mptemplates' ) {
                $uuid = get_post_meta( $post->ID, '_uuid', true );

                if ( ! $uuid ) {
                    flush_rewrite_rules();
                    $url = '?page=mpbuilder-setup&title=' . $title . '&id=' . $post->ID;
                    wp_safe_redirect( $url );
                } else {
                    wp_safe_redirect( $location );
                }
            } else {
                wp_safe_redirect( $location );
            }
        }
    }

    /**
     * Hook into options page after save.
     */
    public function check_the_option() {
        if ( get_option( 'mpb_location_type' ) == 1 ) {
            if ( ! get_option( 'mpb_saved_cities' ) ) {
                $query = new create_tables();
                $query->create_all_cities_table();
                $api = new mpb_queries();
                $api->insert_cities();
            }
        }
    }

    public function beaverbuilder_save_template( $post_id, $publish, $data, $settings ) {
        $this->update_locationdomination_template( $post_id, [
            '_fl_builder_data'          => $data,
            '_fl_builder_data_settings' => $settings
        ] );
    }

    public function elementor_save_template_content( $post_id ) {
        $this->update_locationdomination_template( $post_id );
    }

    public function save_template_content( $post_id, \WP_Post $post ) {
        if ( isset( $_REQUEST[ 'api_key' ] ) ) {
            // don't trigger if we're calling from LocationDomination
            return;
        }

        // Do not update if it is an elementor page... need to use correct hook
        if ( class_exists( 'Elementor\\Plugin' ) ) {
            if ( \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
                return;
            }
        }

        if ( class_exists( 'FLBuilderModel' ) ) {
            if ( \FLBuilderModel::is_builder_enabled( $post_id ) ) {
                return;
            }
        }

        if ( $post->post_status == 'auto-draft' ) {
            return;
        }

        $this->update_locationdomination_template( $post_id );
    }

    public function modify_list_row_actions( $actions, $post ) {
        if( isset( $_GET[ 'send_to_ld'] ) ) {
            $post_id = filter_var( $_GET['send_to_ld']);

            $this->update_locationdomination_template($post_id);
        }
        // Check for your post type.
        if ( $post->post_type == "mptemplates" ) {

            // Build your links URL.
            $url = admin_url( 'edit.php?post_type=mptemplates&send_to_ld=' . $post->ID );

            /*
             * You can reset the default $actions with your own array, or simply merge them
             * here I want to rewrite my Edit link, remove the Quick-link, and introduce a
             * new link 'Copy'
             */
            $actions['send_to_ld'] = sprintf( '<a href="%1$s">%2$s</a>',
                esc_url( $url ),
                'Send To Location Domination'
            );
        }

        return $actions;
    }

    private function update_locationdomination_template( $post_id, $_meta = [] ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $uuid   = get_post_meta( $post_id, '_uuid', true );
        $apiKey = trim( get_option( 'mpb_api_key' ) );
        $post   = get_post( $post_id );

        if ( ! $uuid ) {
            $uuid = substr( sha1( $post->post_title . time() ), 0, 19 );

            update_post_meta( $post_id, '_uuid', $uuid );
        }

        if ( $uuid ) {
            $restUrl = 'https://locationdomination.net/api/website/' . $apiKey . '/template/' . $uuid;

            $duplicate = get_post( $post_id, 'ARRAY_A' );

            unset( $duplicate[ 'ID' ] );
            unset( $duplicate[ 'guid' ] );
            unset( $duplicate[ 'comment_count' ] );

            $terms = [];
            $meta  = [];

            // taxonomies
            $taxonomies = get_object_taxonomies( $duplicate[ 'post_type' ] );

            foreach ( $taxonomies as $taxonomy ) {
                $terms[$taxonomy] = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'names' ] );
            }

            // Custom fields
            $custom_fields = get_post_custom( $post_id );

            $r = wp_remote_post( $restUrl, [
                'body' => [
                    'title'      => $post->post_title,
                    'slug'       => $post->post_name,
                    'content'    => $post->post_content,
                    'meta'       => base64_encode( serialize( $custom_fields ) ),
                    'taxonomies' => base64_encode( serialize( $terms ) ),
                    'full_post'  => base64_encode( serialize( $duplicate ) ),
                ],
            ] );
        }
    }

    public function write_log( $log ) {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }

    public function _do_batch_query() {
        parse_str( $_POST[ 'params' ], $params );
        $params = (array) $params;

        $postType = str_replace( ' ', '-', $params[ 'title' ] );
        $offset   = absint( $_POST[ 'offset' ] );
        if ( get_option( 'mpb_location_type' ) == 2 ) {
            $increment = 100; // You can set your increment value here
        } else {
            $increment = 1000;
        }
        $percent      = absint( $_POST[ 'percentage' ] );
        $post_ID      = $params[ 'post_ID' ];
        $schemaType   = get_post_meta( $post_ID, '_service_type', true );
        $schemaTitle  = get_post_meta( $post_ID, '_service_title', true );
        $schemaDesc   = get_post_meta( $post_ID, '_service_description', true );
        $template_cpt = get_the_title( $post_ID );

        if ( $offset == 0 ) {
            delete_transient( '_step' );
            delete_transient( '_total_records' );
            if ( get_option( 'mpb_location_type' ) == 2 ) {
                $api   = new LocationDominationAPI();
                $query = $api->get_cities_count( $post_ID );

                $total_records = $query[ 0 ]->total;

                $step = round( ( $increment / $total_records ) * 100 );

                set_transient( '_total_records', $total_records, 12 * HOUR_IN_SECONDS );
                set_transient( '_step', $step, 12 * HOUR_IN_SECONDS );

            } else {
                $total_records = 18908;
                $step          = round( ( $increment / $total_records ) * 100 );
                set_transient( '_total_records', $total_records, 12 * HOUR_IN_SECONDS );
                set_transient( '_step', $step, 12 * HOUR_IN_SECONDS );
            }
        }

        if ( $offset > get_transient( '_total_records' ) ) {
            $offset = 'done';
            echo json_encode( array( 'offset' => $offset, 'totalrecords' => get_transient( '_total_records' ) ) );


        } else {
            global $wpdb;

            ini_set( "memory_limit", - 1 );
            set_time_limit( 0 );
            ignore_user_abort( true );

            if ( get_option( 'mpb_location_type' ) == 2 ) {
                /**
                 * Variables
                 */
                $counties = get_post_meta( $post_ID, '_selected_counties', true );
                foreach ( $counties as $county ) {
                    $county_ids[] = $county;
                }

                $data = wp_remote_get( 'https://locationdomination.net//api/cities?offset=' . $offset . '&limit=' . $increment . '&filter=' . implode( ",", $county_ids ) );
//                $data = wp_remote_get( 'https://masspage.aen.technology/wp-json/mpbuilder/v1/get_selected_cities?offset='.$offset.'&limit='.$increment.'&filter='. implode( ",", $county_ids) );
                //error_log('https://masspage.aen.technology/wp-json/mpbuilder/v1/get_selected_cities?offset='.$offset.'&limit='.$increment.'&filter='. implode( ",", $county_ids));
                $body    = wp_remote_retrieve_body( $data );
                $results = json_decode( $body );
            } else {
                $queries = new mpb_queries();
                $results = $queries->query_all( $increment, $offset );
            }
            remove_action( 'do_pings', 'do_all_pings', 10, 1 );
            wp_defer_term_counting( true );
            wp_defer_comment_counting( true );
            if ( is_wp_error( $results ) ) {

                $offset = 'error';
                echo json_encode( array( 'offset' => $offset ) );
            } else {
                $spintax = new \Spintax();

                foreach ( $results as $result ) {
                    $my_post = array(
                        'post_title'   => $result->city . ', ' . $result->state,
                        'post_content' => $spintax->process( $params[ 'post_content' ] ),
                        'post_status'  => 'publish',
                        'post_type'    => $postType,
                        'meta_input'   => array(
                            '_state'        => $result->state,
                            '_city'         => $result->city,
                            '_zip'          => $result->code,
                            '_lat'          => $result->lat,
                            '_lon'          => $result->lon,
                            '_county'       => $result->county,
                            '_schema_desc'  => $schemaDesc,
                            '_schema_title' => $schemaTitle,
                            '_schema_type'  => $schemaType,
                        )
                    );
                    wp_insert_post( $my_post );
                    $wpdb->flush();
                }
            }

            $step = get_transient( '_step' );
            wp_defer_term_counting( false );
            wp_defer_comment_counting( false );

            $offset  += $increment; // You can set your increment value here
            $percent += $step;
            echo json_encode( array( 'offset' => $offset, 'percentage' => $percent ) );
        }

        die();
    }
}