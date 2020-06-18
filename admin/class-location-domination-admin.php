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
class Location_Domination_Admin {

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
     * An array representation of the actions that we
     * provide.
     *
     * @var string[]
     * @since  2.0.0
     * @access protected
     */
    protected $actions = [
        Action_Settings::class,
        Action_Get_Settings::class,
        Action_Preview_Request::class,

        Action_Cancel_Queue::class,
        Action_Continue_Queue::class,
        Action_Start_Queue::class,
        Action_Process_Queue::class,
        Action_End_Queue::class,
    ];

    /**
     * An array of the loaded actions that make use of the
     * Action_Interface class.
     *
     * @var Action_Interface[] $loaded_actions
     * @since  2.0.0
     * @access protected
     */
    protected $loaded_actions = [];

    /**
     * An array representation of the metaboxes that we
     * provide.
     *
     * @var string[]
     * @since  2.0.0
     * @access protected
     */
    protected $metaboxes = [
        Metabox_Shortcodes::class,
        Metabox_Settings::class,
    ];

    /**
     * An array of the loaded metaboxes that make use of the
     * Metabox_Interface class.
     *
     * @var Metabox_Interface[] $loaded_metaboxes
     * @since  2.0.0
     * @access protected
     */
    protected $loaded_metaboxes = [];

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

        foreach ( $this->actions as $action ) {
            $this->loaded_actions[ $action ] = new $action();
        }

        foreach ( $this->metaboxes as $metabox ) {
            $this->loaded_metaboxes[ $metabox ] = new $metabox();
        }
    }

    /**
     * Register the admin pages to the WP menu.
     *
     * @return void
     * @since 2.0.0
     */
    public function register_admin_page() {
        global $submenu;

        $capability = 'manage_options';
        $slug       = 'location-domination';

        $hook = add_menu_page( __( 'Location Domination', 'textdomain' ), __( 'Location Domination', 'textdomain' ), $capability, $slug, [
            $this,
            'render_admin_page'
        ], 'dashicons-text' );

        if ( current_user_can( $capability ) ) {
            $submenu[ $slug ][] = array(
                __( 'Dashboard', 'textdomain' ),
                $capability,
                'admin.php?page=' . $slug . '#/'
            );

            $submenu[ $slug ][] = array(
                __( 'Account', 'textdomain' ),
                $capability,
                'admin.php?page=' . $slug . '#/account'
            );
        }

        add_action( 'load-' . $hook, [ $this, 'enqueue_scripts' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts_for_cpt' ] );
    }

    /**
     * Register the scripts for specific pages on WP admin.
     *
     * @param $hook
     *
     * @return void
     * @since  2.0.0
     * @access public
     */
    public function register_scripts_for_cpt( $hook ) {
        $screen = get_current_screen();

        if ( $hook == 'post.php' && $screen->post_type != LOCATION_DOMINATION_TEMPLATE_CPT ) {
            return;
        }

        $this->enqueue_scripts();
    }

    /**
     * Render the admin page.
     *
     * @return void
     * @since 2.0.0
     */
    public function render_admin_page() {
        include_once( __DIR__ . '/partials/location-domination-admin-display.php' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Location_Domination_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Location_Domination_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name . '_runtime', plugin_dir_url( __DIR__ ) . 'assets/js/runtime.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '_vendors', plugin_dir_url( __DIR__ ) . 'assets/js/vendors.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '_admin', plugin_dir_url( __DIR__ ) . 'assets/js/admin.js', array(
            'jquery',
            sprintf( '%s_vendors', $this->plugin_name )
        ), $this->version, true );
    }

    /**
     * Apply filters to the content before spinning.
     *
     * @param $content
     *
     * @return string
     * @since  2.0.0
     * @access public
     */
    public function location_domination_content_pre_spin( $content ) {
        $content = str_ireplace( '<p>{</p>', '{', $content );
        $content = str_ireplace( '<p>}</p>', '}', $content );

        return $content;
    }

    /**
     * Register all of our metaboxes/
     *
     * @return void
     * @since  2.0.0
     * @access public
     */
    public function register_metaboxes() {
        foreach ( $this->get_loaded_metaboxes() as $metabox ) {
            add_meta_box(
                $metabox->get_key(),
                $metabox->get_title(),
                [ $metabox, 'handle' ],
                $metabox->get_screen(),
                $metabox->get_context()
            );
        }
    }

    /**
     * Bind shortcodes to content using filters.
     *
     * @param       $content
     * @param array $bindings
     *
     * @return string|string[]
     * @since  2.0.0
     * @access public
     */
    public function shortcode_filters( $content, $bindings = [] ) {
        foreach ( $bindings as $lookup => $replace ) {
            $content = str_ireplace( $lookup, $replace, $content );
        }

        return $content;
    }

    /**
     * Get all of the loaded actions.
     *
     * @return \Action_Interface[]
     * @since  2.0.0
     * @access public
     */
    public function get_loaded_actions() {
        return $this->loaded_actions;
    }

    /**
     * Get all of the loaded metaboxes.
     *
     * @return \Metabox_Interface[]
     * @since  2.0.0
     * @access public
     */
    public function get_loaded_metaboxes() {
        return $this->loaded_metaboxes;
    }

    /**
     * Redirect user back to the previous page.
     *
     * @return void
     * @since 2.0.0
     */
    public function redirect_back_to_edit_page() {
        $this->start_session();

        if ( isset( $_GET[ 'location-domination' ] ) ) {
            $post_id = (int) $_GET[ 'location-domination' ];

            Location_Domination_Admin::send_template_to_location_domination( $post_id );

            wp_safe_redirect( wp_get_referer() );
            die;
        }
    }

    /**
     * Add an action to send to Location Domination.
     *
     * @param $actions
     * @param $post
     *
     * @return mixed
     * @since 2.0.0
     */
    public function add_send_to_location_domination_row_action( $actions, $post ) {
        if ( $post->post_type === 'mptemplates' ) {
            $url = admin_url( 'edit.php?post_type=mptemplates&location-domination=' . $post->ID );

            $actions[ 'location-domination' ] = sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url( $url ),
                'Send To Location Domination'
            );
        }

        return $actions;
    }

    /**
     * Display any errors as a notice.
     *
     * @return void
     * @since 2.0.0
     */
    public function show_error_notices() {
        if ( isset( $_SESSION[ 'location-domination-errors' ] ) ) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . $_SESSION[ 'location-domination-errors' ] . '</p>';
            echo '</div>';

            unset( $_SESSION[ 'location-domination-errors' ] );
        }

        if ( isset( $_SESSION[ 'location-domination-status' ] ) ) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . $_SESSION[ 'location-domination-status' ] . '</p>';
            echo '</div>';

            unset( $_SESSION[ 'location-domination-status' ] );
        }

        if ( isset( $_SESSION[ 'location-domination-clear-permalinks' ] ) ) {
            Location_Domination_Admin::clear_permalinks();

            unset( $_SESSION[ 'location-domination-clear-permalinks' ] );
        }
    }

    /**
     * Set the ACF asset url so that we are displaying
     * them correctly.
     *
     * @Since 2.0.0
     * @return string
     */
    public function set_acf_url() {
        return LOCATION_DOMINATION_ACF_URL;
    }

    /**
     * Disable the ACF admin page so that we are not
     * showing it on users websites.
     *
     * @return bool
     * @since 2.0.0
     */
    public function set_acf_admin_hidden() {
        return false;
    }

    /**
     * Check whether the permalink structure is acceptable
     * and if not throw an error to the user.
     *
     * @return void
     * @since 2.0.0
     */
    public function check_permalink_structure() {
        $structure = trim( get_option( 'permalink_structure' ), '/' );

        if ( $structure !== '%postname%' ) {
            Location_Domination_Admin::send_error_on_next_load(
                sprintf( 'Your permalink structure is using <strong>%s</strong>. In order for our plugin to work, you must use the "post name" permalink structure.', $structure )
            );
        }
    }

    /**
     * Process a bunch of actions after saving the template.
     * Most commonly used for syncing to Location Domination and
     * clearing the permalinks.
     *
     * @param          $post_id
     * @param \WP_Post $post
     *
     * @return void
     * @since 2.0.0
     */
    public function process_template_after_save( $post_id, WP_Post $post ) {
        if ( $post->post_status == 'publish' ) {
            Location_Domination_Admin::clear_permalinks_queued();
            Location_Domination_Admin::send_template_to_location_domination( $post_id );
        }
    }

    /**
     * Populate the last post request from Location Domination.
     *
     * @since 2.0.10
     * @return void
     */
    public function retrieve_last_post_request() {
        global $post;

        if ( ! $post || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return;
        }

        $last_post_request = get_post_meta( $post->ID, 'location_domination_post_request', true );

        if ( $last_post_request ) {
            return;
        }

        $uuid    = get_post_meta( $post->ID, '_uuid', true );
        $api_key = trim( get_option( 'mpb_api_key' ) );

        if ( $post->post_type !== LOCATION_DOMINATION_TEMPLATE_CPT || ! $uuid || ! $api_key ) {
            return;
        }

        $rest_url = sprintf( '%s/api/last-post-request?apiKey=%s&uuid=%s', trim( MAIN_URL, '/' ), $api_key, $uuid );

        $response = wp_remote_get( $rest_url );

        if ( is_wp_error( $response ) || $response['response']['code'] !== 200 ) {
            return;
        }

        $request = json_decode( $response['body'] );


        if ( $request ) {
            $_request = [];

            $page_title = get_field( 'page_title' );
            $page_slug = get_field( 'page_slug' );

            if ( ! $page_title ) {
                update_field( 'page_title', $request->post_name, $post->ID );
            }

            if ( ! $page_slug ) {
                update_field( 'page_slug', $request->post_slug, $post->ID );
            }

            if ( $request->cities ) {
                $_request['cities'] = $request->cities;
            } else {
                $_request['cities'] = [];
            }

            if ( $request->states ) {
                $_request['states'] = $request->states;
            } else {
                $_request['states'] = [];
            }

            if ( $request->counties ) {
                $_request['counties'] = $request->counties;
            } else {
                $_request['counties'] = [];
            }

            $group = 'For all cities/counties';

            if ( $request->states && $request->counties && $request->cities ) {
                $group = 'For specific cities';
            } else if ( $request->states && $request->counties ) {
                $group = 'For specific counties';
            } else if ( $request->states ) {
                $group = 'For specific states';
            }

            $_request['group'] = $group;

            update_post_meta( $post->ID, 'location_domination_post_request', $_request );
        }
    }

    /**
     * Hooks into ACF to get the latest data from the fields
     * before we save it into the database.
     *
     * @param $post_id
     *
     * @return void
     * @since 2.0.0
     */
    public function process_child_templates_after_save( $post_id ) {
        global $post;

        // Prevents pages from getting deleted
        if ( isset( $_POST[ 'acf' ], $_POST[ 'acf' ][ 'field_5edaf5dd883e4' ] ) ) {
            wp_update_post( [
                'ID'         => $post_id,
                'menu_order' => $_POST['acf']['field_5edaf5dd883e4'] ? -1 : 0,
            ] );
        }

        if ( $post->post_type !== LOCATION_DOMINATION_TEMPLATE_CPT ) {
            return;
        }

        Location_Domination_Admin::generate_schema( $post_id );

        $fields = get_fields( $post_id );

        remove_action( 'save_post_' . LOCATION_DOMINATION_TEMPLATE_CPT, [ $this, 'process_template_after_save' ] );
        remove_action( 'save_post_' . LOCATION_DOMINATION_TEMPLATE_CPT, [
            $this,
            'process_child_templates_after_save'
        ] );

        if ( ! $fields['spin_templates'] ) {
            $enabled_templates = [];
        } else {
            $enabled_templates = array_filter( $fields[ 'spin_templates' ], function ( $item ) {
                return $item[ 'enabled' ];
            } );
        }

        /**
         * Start to process sub-templates and manage their settings
         * accordingly.
         */
        $elementor_edit_mode_nonce = isset( $_POST[ '_elementor_edit_mode_nonce' ] ) ? $_POST[ '_elementor_edit_mode_nonce' ] : null;

        if ( count( $enabled_templates ) > 0 ) {
            // Stop Elementor turning off edit mode for child templates
            if ( $elementor_edit_mode_nonce ) {
                unset( $_POST[ '_elementor_edit_mode_nonce' ] );
            }
        }

        foreach ( $enabled_templates as $template ) {
            wp_update_post( [
                'ID'          => $template[ 'template' ],
                'post_parent' => $post_id,
                'post_status' => $template[ 'enabled' ] ? 'publish' : 'draft',
            ] );
        }

        // Reinstate Elementor edit mode nonce
        if ( $elementor_edit_mode_nonce ) {
            $_POST[ '_elementor_edit_mode_nonce' ] = $elementor_edit_mode_nonce;
        }

        add_action( 'save_post_' . LOCATION_DOMINATION_TEMPLATE_CPT, [ $this, 'process_template_after_save' ] );
        add_action( 'save_post_' . LOCATION_DOMINATION_TEMPLATE_CPT, [ $this, 'process_child_templates_after_save' ] );
    }

    /**
     * Add a button to send the template to Location Domination. Within this
     * period we also flush permalinks.
     *
     * @return void
     * @since 2.0.0
     */
    public function add_send_to_location_domination_button_below_editor() {
        if ( get_post_type() === LOCATION_DOMINATION_TEMPLATE_CPT ) {
            $url = admin_url( 'edit.php?post_type=mptemplates&location-domination=' . get_the_ID() );

            echo sprintf( '<a href="%s" class="button button-primary button-large" id="send-to-location-domination">' .
                          '<span style="display: inline-block; margin-right: 5px; position: relative; top: 3px;"><svg style=" height: 16px;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="paper-plane" class="svg-inline--fa fa-paper-plane fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"></path></svg></span>' .
                          'Send to Location Domination' .
                          '</a>', $url );
        }
    }

    /**
     * Check whether the site is using the permalink structure that
     * we required.
     *
     * @return bool
     * @since 2.0.0
     */
    public static function is_using_post_name_permalink_structure() {
        return '%postname%' === trim( get_option( 'permalink_structure' ), '/' );
    }

    /**
     * Used for admin notices.
     *
     * @return void
     * @since 2.0.0
     */
    protected function start_session() {
        if ( ! session_id() ) {
            session_start();
        }
    }

    /**
     * Called to report errors in the admin notices
     *
     * @param $message
     *
     * @since 2.0.0
     */
    public static function send_error_on_next_load( $message ) {
        $_SESSION[ 'location-domination-errors' ] = $message;
    }

    /**
     * Called to report success messages in the admin notices
     *
     * @param $message
     *
     * @since 2.0.0
     */
    public static function send_status_on_next_load( $message ) {
        $_SESSION[ 'location-domination-status' ] = $message;
    }

    /**
     * Queue the permalinks to be cleared.
     *
     * @return void
     * @since 2.0.0
     */
    public static function clear_permalinks_queued() {
        $_SESSION[ 'location-domination-clear-permalinks' ] = true;
    }

    /**
     * Check to see whether we are able to clear permalinks and set
     * structure to post name. In order for us to do that, the user
     * must already be using that structure so we are not changing
     * their site.
     *
     * @return void
     * @since 2.0.0
     */
    public static function clear_permalinks() {
        global $wp_rewrite;

        $is_using_permalink_structure = Location_Domination_Admin::is_using_post_name_permalink_structure();

        if ( $is_using_permalink_structure ) {
            $wp_rewrite->set_permalink_structure( '/%postname%/' );

            update_option( 'rewrite_rules', false );

            $wp_rewrite->flush_rules( true );
        }
    }

    /**
     * Send a copy of the post to Location Domination.
     *
     * @param $post_id int
     *
     * @return bool
     * @since 2.0.0
     */
    public static function send_template_to_location_domination( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $post    = get_post( $post_id, 'ARRAY_A' );
        $uuid    = get_post_meta( $post_id, '_uuid', true );
        $api_key = trim( get_option( 'mpb_api_key' ) );

        if ( $post[ 'post_type' ] !== LOCATION_DOMINATION_TEMPLATE_CPT ) {
            return;
        }

        if ( ! $uuid ) {
            $uuid = substr( sha1( $post[ 'post_title' ] . time() ), 0, 19 );

            update_post_meta( $post_id, '_uuid', $uuid );
        }

        $rest_url = sprintf( '%s/api/website/%s/template/%s', trim( MAIN_URL, '/' ), $api_key, $uuid );

        // Create an object that can be copied to our servers
        $mutated_post = $post;
        $terms        = [];

        unset( $mutated_post[ 'ID' ] );
        unset( $mutated_post[ 'guid' ] );
        unset( $mutated_post[ 'comment_count' ] );

        $taxonomies    = get_object_taxonomies( $mutated_post[ 'post_type' ] );
        $custom_fields = get_post_custom( $post_id );

        foreach ( $taxonomies as $taxonomy ) {
            $terms[ $taxonomy ] = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'names' ] );
        }

        $response = wp_remote_post( $rest_url, [
            'body' => [
                'title'      => $mutated_post[ 'post_title' ],
                'slug'       => $mutated_post[ 'post_name' ],
                'content'    => $mutated_post[ 'post_content' ],
                'meta'       => base64_encode( serialize( $custom_fields ) ),
                'taxonomies' => base64_encode( serialize( $terms ) ),
                'full_post'  => base64_encode( serialize( $mutated_post ) ),
            ],
        ] );

        if ( $response[ 'response' ][ 'code' ] === 404 ) {
            Location_Domination_Admin::send_error_on_next_load( 'Please make sure that you have added your website in Location Domination and you have entered the correct API key.' );

            return false;
        }

        if ( is_wp_error( $response ) ) {
            Location_Domination_Admin::send_error_on_next_load( 'We were unable to send your template to Location Domination. Please try again later.' );

            return false;
        }

        Location_Domination_Admin::send_status_on_next_load( 'We have successfully synced your template to Location Domination.' );

        return true;
    }

    /**
     * Sends a request to Location Domination to generate
     * the schema.
     *
     * @param $post_id     int
     * @param $return_json boolean
     *
     * @return array
     * @since 2.0.4
     */
    public static function generate_schema( $post_id, $return_json = false ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $post    = get_post( $post_id, 'ARRAY_A' );
        $api_key = trim( get_option( 'mpb_api_key' ) );

        if ( $post[ 'post_type' ] !== LOCATION_DOMINATION_TEMPLATE_CPT ) {
            return;
        }

        $rest_url = sprintf( '%s/api/website/%s/schema', trim( MAIN_URL, '/' ), $api_key );

        $salary = get_field( 'base_salary' );

        $response = wp_remote_post( $rest_url, [
            'body' => [
                'date_posted'     => get_field( 'job_date_posted', $post_id ),
                'valid_through'   => get_field( 'job_valid_through_date', $post_id ),
                'employment_type' => get_field( 'job_employment_type', $post_id ),
                'job_title'       => get_field( '_ld_job_title', $post_id ),
                'job_description' => get_field( '_ld_job_description', $post_id ),
                'company_name'    => get_field( 'company_name', $post_id ),
                'base_salary'     => $salary[ 'base_salary' ],
                'currency'        => $salary[ 'currency' ],
            ]
        ] );

        if ( is_wp_error( $response ) ) {
            $error = _e( 'There was an error communicating with Location Domination.' );

            if ( $return_json ) {
                return wp_send_json( [ 'success' => false, 'message' => $error ] );
            }

            return Location_Domination_Admin::send_error_on_next_load( $error );
        }

        $json_response = json_decode( $response[ 'body' ] );

        if ( $json_response->success ) {
            update_post_meta( $post_id, 'location_domination_schema_template', $json_response->schema );
        }

        return [];
    }

}
