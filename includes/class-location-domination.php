<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Location_Domination
 * @subpackage Location_Domination/includes
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Location_Domination_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'LOCATION_DOMINATION_VERSION' ) ) {
            $this->version = LOCATION_DOMINATION_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->plugin_name = 'location-domination';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_cpt_hooks();
        $this->define_rest_hooks();
        $this->define_shortcode_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Location_Domination_Loader. Orchestrates the hooks of the plugin.
     * - Location_Domination_Admin. Defines all hooks for the admin area.
     * - Location_Domination_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-location-domination-loader.php';

        /**
         * The class responsible for spinning text.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-location-domination-spinner.php';

        /**
         * The class responsible for registering the custom post types.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-location-domination-custom-post-types.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-location-domination-admin.php';

        /**
         * The class responsible for defining all endpoints that occur in the rest area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'rest/class-location-domination-rest.php';

        /**
         * The class responsible for defining all shortcodes.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-location-domination-shortcodes.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-location-domination-public.php';

        $this->loader = new Location_Domination_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        require_once( dirname( __DIR__ ) . '/admin/actions/interface-action.php' );
        require_once( dirname( __DIR__ ) . '/metaboxes/interface-metabox.php' );

        foreach ( glob( dirname( __DIR__ ) . '/admin/actions/*.php' ) as $action ) {
            require_once( $action );
        }

        foreach ( glob( dirname( __DIR__ ) . '/metaboxes/*.php' ) as $metabox ) {
            require_once( $metabox );
        }

        $plugin_admin = new Location_Domination_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_back_to_edit_page' );
        $this->loader->add_Action( 'admin_init', $plugin_admin, 'check_permalink_structure' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_page' );
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'show_error_notices' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'register_metaboxes', 999 );
        $this->loader->add_action( 'post_row_actions', $plugin_admin, 'add_send_to_location_domination_row_action', 10, 2 );
        $this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'add_send_to_location_domination_button_below_editor', 10, 2 );
        $this->loader->add_action( 'save_post_' . LOCATION_DOMINATION_TEMPLATE_CPT, $plugin_admin, 'process_template_after_save', 10, 2 );
        $this->loader->add_action( 'acf/save_post', $plugin_admin, 'process_child_templates_after_save', 10, 2 );
        $this->loader->add_action( 'admin_head-post.php', $plugin_admin, 'retrieve_last_post_request', 40 );

        $this->loader->add_filter( 'location_domination_content_pre_spin', $plugin_admin, 'location_domination_content_pre_spin' );
        $this->loader->add_filter( 'location_domination_shortcodes', $plugin_admin, 'shortcode_filters', 10, 2 );
        $this->loader->add_filter( 'acf/settings/url', $plugin_admin, 'set_acf_url', 10 );
//        $this->loader->add_filter( 'acf/settings/show_admin', $plugin_admin, 'set_acf_admin_hidden', 10 );

        foreach ( $plugin_admin->get_loaded_actions() as $action ) {
            $this->loader->add_action( 'admin_post_' . $action->get_key(), $action, 'handle' );
        }
    }

    /**
     * Register all of the hooks related to the custom post types.
     *
     * @since    2.0.0
     * @access   private
     */
    private function define_cpt_hooks() {
        $plugin_cpt = new Location_Domination_Custom_Post_Types( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'init', $plugin_cpt, 'register' );
        $this->loader->add_action( 'template_redirect', $plugin_cpt, 'redirect_frontend_for_guests' );
        $this->loader->add_filter( 'comments_open', $plugin_cpt, 'show_comments_for_template', 10, 2 );
    }

    /**
     * Register all of the rest endpoints related to communicating
     * with the Location Domination service.
     *
     * @return void
     * @since  2.0.0
     * @access private
     */
    private function define_rest_hooks() {
        require_once( dirname( __DIR__ ) . '/rest/endpoints/interface-endpoint.php' );

        foreach ( glob( dirname( __DIR__ ) . '/rest/endpoints/*.php' ) as $validator ) {
            require_once( $validator );
        }

        $plugin_rest = new Location_Domination_Rest( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'rest_api_init', $plugin_rest, 'register_routes' );
    }

    /**
     * Register all of the shortcode endpoints.
     *
     * @return void
     * @since  2.0.0
     * @access private
     */
    private function define_shortcode_hooks() {
        require_once( dirname( __DIR__ ) . '/public/shortcodes/interface-shortcode.php' );

        foreach ( glob( dirname( __DIR__ ) . '/public/shortcodes/*.php' ) as $shortcode ) {
            require_once( $shortcode );
        }

        $plugin_shortcode = new Location_Domination_Shortcodes( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_filter( 'the_content', $plugin_shortcode, 'standardize_shortcodes' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Location_Domination_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_head', $plugin_public, 'display_schema', 99 );
        $this->loader->add_action( 'pre_get_posts', $plugin_public, 'remove_template_slug_from_request', 10 );
        $this->loader->add_filter( 'post_type_link', $plugin_public, 'remove_template_slug_from_links', 10, 2 );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Location_Domination_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version() {
        return $this->version;
    }

}
