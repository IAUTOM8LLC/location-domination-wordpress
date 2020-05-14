<?php


class mpbuilder_main {
	/**
	 * A reference to the loader class that coordinates the hooks and callbacks
	 * throughout the plugin.
	 *
	 * @access protected
	 * @var     $loader    Manages hooks between the WordPress hooks and the callback functions.
	 */
	protected $loader;

	/**
	 * Represents the slug of the plugin that can be used throughout
	 * for internationalization and other purposes.
	 *
	 * @access protected
	 * @var    string $plugin_slug The single, hyphenated string used to identify this plugin.
	 */
	protected $plugin_slug;

	/**
	 * Maintains the current version of the plugin so that we can use it throughout
	 * the plugin.
	 *
	 * @access protected
	 * @var    string $version The current version of the plugin.
	 */
	protected $version;


	/**
	 * Instantiates the plugin by setting  up the core properties and loading
	 * all necessary dependencies and defining the hooks.
	 *
	 * The constructor will define both the plugin slug and the verison
	 * attributes, but will also use internal functions to import all the
	 * plugin dependencies, and will leverage the Loader for
	 * registering the hooks and the callback functions used throughout the
	 * plugin.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->plugin_slug = 'mpbuilder';
		$this->version     = '1.2.1';


	}

	private function load_dependencies() {


		/**
		 * Admin Dependencies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/location-domination-admin.php';

		/**
		 * Admin Templates
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/cpt-content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/meta-content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/shortcode-content.php';
		/**
		 * Admin API
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/api/location-domination-api.php';
		/**
		 * Admin Includes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/dynamic-cpt.php';


		/**
		 * Public Dependencies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/mpbuilder-public.php';
		/**
		 * Spintax
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/spintax/spintax.php';
		/**
		 * Schema
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'public/schema/schema-template.php';

		/**
		 * Shared Dependencies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shared/mpbuilder-shared.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shared/mpbulder-shortcodes.php';


		/**
		 * Load All Dependencies
		 */
		require_once plugin_dir_path( __FILE__ ) . 'mpbuilder-loader.php';
		$this->loader = new mpbuilder_loader();

	}

	/**
	 * Defines the hooks and callback functions that are used for setting up the plugin stylesheets, settings, and scripts.
	 *
	 *
	 * @access    private
	 */
	private function define_admin_hooks() {

		$admin = new mpbuilder_admin( $this->get_version() );
		if ( isset( $_GET['page'] ) && ( $_GET['page'] == 'location-domination-page.php' ) || isset( $_GET['page'] ) && ( $_GET['page'] == 'mpbuilder-setup' ) ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_admin_styles' );
		}
		$this->loader->add_action( 'admin_menu', $admin, 'mpbuilder_admin_page' );
		$this->loader->add_action( 'admin_init', $admin, 'register_settings_options' );
		$this->loader->add_action( 'wp_ajax_setlocation', $admin, 'mpbuilder_set_loc' );
		$this->loader->add_action( 'wp_ajax_nopriv_setlocation', $admin, 'mpbuilder_set_loc' );
		$this->loader->add_action( 'wp_ajax_setcounty', $admin, 'mpbuilder_set_county' );
		$this->loader->add_action( 'wp_ajax_nopriv_setcounty', $admin, 'mpbuilder_set_county' );
		$this->loader->add_action( 'wp_ajax_setcity', $admin, 'mpbuilder_set_city' );
		$this->loader->add_action( 'wp_ajax_locationdomination', $admin, 'mpbuilder_redirect_to_ld' );
		$this->loader->add_action( 'wp_ajax_nopriv_setcity', $admin, 'mpbuilder_set_city' );
		$this->loader->add_action( 'save_post', $admin, 'cpt_save_postdata' );
		$this->loader->add_action( 'admin_notices', $admin, 'add_upgrade_message' );
//		$this->loader->add_filter( 'redirect_post_location', $admin, 'add_mass_pages' );
		$this->loader->add_action( 'add_option_mpb_location_type', $admin, 'check_the_option', 10, 3);
		$this->loader->add_action( 'wp_ajax__do_batch_query', $admin, '_do_batch_query' );
		$this->loader->add_action( 'wp_ajax_nopriv__do_batch_query', $admin, '_do_batch_query' );
		$this->loader->add_action( 'save_post_mptemplates', $admin, 'save_template_content', 10, 2 );
		$this->loader->add_action( 'post_row_actions', $admin, 'modify_list_row_actions', 10, 2 );
		$this->loader->add_action( 'elementor/editor/after_save', $admin, 'elementor_save_template_content', 10, 2 );
		$this->loader->add_action( 'fl_builder_after_save_layout', $admin, 'beaverbuilder_save_template', 10, 4 );
	}

	private function define_shared_hooks(){
		$shared = new mpbulder_shortcodes( $this->get_version() );
	}


	/**
	 * Defines the hooks and callback functions that are used for rendering information on the front
	 * end of the site.
	 *
	 *
	 * @access    private
	 */
	private function define_public_hooks() {

		/*
		 * definitions
		 */
		$public = new mpbuilder_public( $this->get_version() );

//		$this->loader->add_action('the_content',$public, 'spintax_page_content');
		$this->loader->add_action('wp_head', $public, 'mpbuilder_publish_schema');
		$this->loader->add_action( 'init', $public, 'mpbuilder_flush_permalinks' );

		add_shortcode('internal_links', array($public, 'page_list'));

		foreach(['city', 'City', 'CITY'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_city'));
        }

		foreach(['state', 'State', 'STATE'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_state'));
        }

		foreach(['county', 'County', 'COUNTY'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_county'));
        }

		foreach(['zips', 'Zips', 'ZIPS'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_zips'));
        }

		foreach(['region', 'Region', 'REGION'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_region'));
        }

		foreach(['country', 'Country', 'COUNTRY'] as $shortcode) {
		    add_shortcode($shortcode, array($public, 'get_country'));
        }

		add_shortcode( 'breadcrumb', array( $public, 'mpb_breadcrumb' ) );

		/*
		 * actions
		 */



		/*
		 * Shortcodes
		 */




	}
	/**
	 * Sets this class into motion.
	 *
	 * Executes the plugin by calling the run method of the loader class which will
	 * register all of the hooks and callback functions used throughout the plugin
	 * with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Returns the current variables of the plugin to the caller.
	 *
	 * @return    string    $this->version
	 *
	 */
	public function get_version() {
		return $this->version;
	}
	public  function get_plugin_slug(){
		return $this->plugin_slug;
	}

}