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
class Location_Domination_Custom_Post_Types
{

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
	 * An instance of the page template class.
	 *
	 * @since 2.0.34
	 * @var \Location_Domination_Page_Template $page_templater
	 */
	protected $page_templater;

	/**
	 * The labels for our Template post type.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var string[] $labels The array representation of labels.
	 */
	protected $labels = [
		'name'               => 'Template',
		'singular_name'      => 'Mass Page Templete',
		'add_new'            => 'Add New Template',
		'add_new_item'       => 'Add New Template',
		'edit_item'          => 'Edit Template',
		'new_item'           => 'New Template',
		'all_items'          => 'All Templates',
		'view_item'          => 'View Template',
		'search_items'       => 'Search Templates',
		'not_found'          => 'No Templates found',
		'not_found_in_trash' => 'No Templates found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Mass Page Templates',
	];

	/**
	 * The arguments for our Template post type. There
	 * is no need to include the labels parameter as we
	 * bind that upon CPT registration.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var array The arguments for our templates post type.
	 */
	protected $arguments = [
		'public'             => true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'page',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
	];

	protected $cpts = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->page_templater = new Location_Domination_Page_Template( $this->plugin_name, $this->version );
	}

	/**
	 * Register all of the custom post types that
	 * we need to operate the plugin. This includes the main
	 * templates CPT, and sub-type templates.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function register() {
		if ( is_admin() || is_user_logged_in() ) {
			$this->arguments[ 'publicly_queryable' ] = true;
		}

		if ( in_array( trim( $_SERVER[ 'REQUEST_URI' ], '/' ), [ 'sitemap.xml', 'sitemap_index.xml' ] ) ) {
			$this->arguments[ 'publicly_queryable' ] = false;
		}

		register_post_type( LOCATION_DOMINATION_TEMPLATE_CPT, array_merge( $this->arguments, [
			'labels' => $this->labels,
		] ) );

		$this->register_dynamic_post_types();
	}

	/**
	 * We don't want to get our templates indexed so we
	 * redirect non-authenticated users.
	 *
	 * @return void
	 * @since 2.0.4
	 */
	public function redirect_frontend_for_guests() {
		$queried_post_type = get_query_var( 'post_type' );

		if ( $queried_post_type === LOCATION_DOMINATION_TEMPLATE_CPT ) {
			if ( is_singular( LOCATION_DOMINATION_TEMPLATE_CPT ) || is_archive() ) {
				if ( !current_user_can( 'editor' ) && !current_user_can( 'administrator' ) ) {
					wp_redirect( esc_url_raw( get_bloginfo( 'url' ) ), 301 );
					exit;
				}
			}
		}

		$templates = new WP_Query( [
			'posts_per_page' => -1,
			'post_type'      => LOCATION_DOMINATION_TEMPLATE_CPT,
		] );

		if ( is_singular() && !empty( $templates->posts ) ) {
			$template_post_types = array_filter( array_map( function ( $template ) {
				return get_post_meta( $template->ID, "_uuid", true );
			}, $templates->posts ) );

			global $post;

			$template = self::get_template_by_uuid( $post->post_type );

			if ( !empty( $template ) && $post instanceof WP_Post && in_array( $post->post_type, $template_post_types ) ) {
				$use_template_slug = get_field( "use_template_slug", $template->ID );

				// Redirect if they are accessing via the old slug
				if ( !$use_template_slug && strpos( $_SERVER[ 'REQUEST_URI' ], $post->post_type ) !== false ) {
					wp_safe_redirect( get_permalink( $post->ID ), 301 );
					exit;
				}
			}
		}
	}


	/**
	 * Disable comments for the template if they are
	 * turned off within the settings.
	 *
	 * @param $open
	 * @param $post_id
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function show_comments_for_template( $open, $post_id ) {
		$post_type = get_post_type( $post_id );
		$template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post_type );

		if ( $template ) {
			return get_field( 'show_comments', $template->ID );
		}

		return $open;
	}

	/**
	 * Register all of the sub-type templates that were
	 * created from templates.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	protected function register_dynamic_post_types() {
		$arguments = [
			'post_parent' => 0,
			'post_type'   => LOCATION_DOMINATION_TEMPLATE_CPT,
			'post_status' => 'publish',
			'numberposts' => -1,
			'fields'      => 'ids',
		];

		$custom_post_types = get_posts( $arguments );

		add_filter( 'theme_mptemplates_templates', [ $this->page_templater, 'add_page_template' ], 99 );
		add_filter( 'single_template', [ $this->page_templater, 'redirect_page_template' ], 99 );

		foreach ( $custom_post_types as $post_id ) {
			$title = get_the_title( $post_id );
			$singular = $title;

			$labels = [
				'name'               => $title,
				'singular_name'      => $singular,
				'add_new'            => sprintf( 'Add New %s', $singular ),
				'add_new_item'       => sprintf( 'Add New %s', $singular ),
				'edit_item'          => sprintf( 'Edit %s', $singular ),
				'new_item'           => sprintf( 'New %s', $singular ),
				'all_items'          => sprintf( 'All %s', $title ),
				'view_item'          => sprintf( 'View %s', $title ),
				'search_items'       => sprintf( 'Search %s', $title ),
				'not_found'          => sprintf( 'No %s\'s found', $singular ),
				'not_found_in_trash' => sprintf( 'No %s\'s in trash', $singular ),
				'menu_name'          => wp_strip_all_tags( $title ),
			];

			$use_template_slug = get_field( 'use_template_slug', $post_id );

			$arguments = [
				'labels'             => $labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => $use_template_slug ? [ 'slug' => get_post_field( 'post_name', $post_id ) ] : null,
				'capability_type'    => 'page',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => [
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'custom-fields',
				],
			];

			$template_name = self::get_template_name( $post_id );

			register_post_type( $template_name, $arguments );

			$this->cpts[] = $template_name;

			add_filter( "theme_{$template_name}_templates", [ $this->page_templater, 'add_page_template' ], 99 );
			add_filter( "manage_{$template_name}_posts_columns", [ $this, 'display_custom_fields' ], 99, 2 );
			add_filter( "manage_{$template_name}_posts_custom_column", [ $this, 'cpt_type_column' ], 99, 2 );
			add_filter( "restrict_manage_posts", [ $this, 'register_custom_filters' ] );
			add_filter( "parse_query", [ $this, 'modify_cpt_filter_query' ] );
		}
	}

	public function modify_cpt_filter_query( $query ) {
		global $pagenow;

		$type = 'post';

		if ( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}

		if ( in_array( $type, $this->cpts ) && is_admin() && $pagenow == 'edit.php' && isset( $_GET[ 'LOCATION_DOMINATION_FILTER' ] ) && $_GET[ 'LOCATION_DOMINATION_FILTER' ] != '' ) {
			switch ( $_GET[ 'LOCATION_DOMINATION_FILTER' ] ) {
				case 'neighborhoods':
					$query->query_vars[ 'meta_key' ] = '_neighborhood';
					$query->query_vars[ 'meta_compare' ] = 'EXISTS';
					break;
				case 'indexes':
					$query->query_vars[ 'meta_key' ] = '_ld_index';
					$query->query_vars[ 'meta_compare' ] = 'EXISTS';
					break;
				case 'cities':
					$query->query_vars[ 'meta_query' ] = [
						'relation' => 'AND',
						[
							'key'     => '_neighborhood',
							'compare' => 'NOT EXISTS',
						],

						[
							'key'     => '_ld_index',
							'compare' => 'NOT EXISTS',
						],
					];
					break;
			}
		}
	}

	public function register_custom_filters( $query ) {
		$type = 'post';

		if ( isset( $_GET[ 'post_type' ] ) ) {
			$type = $_GET[ 'post_type' ];
		}

		if ( in_array( $type, $this->cpts ) ) {
			$values = [
				__( 'Index Pages', 'location-domination' )        => 'indexes',
				__( 'City Pages', 'location-domination' )         => 'cities',
				__( 'Neighborhood Pages', 'location-domination' ) => 'neighborhoods',
			];
			?>
            <select name="LOCATION_DOMINATION_FILTER">
                <option value=""><?php _e( 'Filter By Type', 'wose45436' ); ?></option>
				<?php
				$current_v = isset( $_GET[ 'LOCATION_DOMINATION_FILTER' ] ) ? $_GET[ 'LOCATION_DOMINATION_FILTER' ] : '';
				foreach ( $values as $label => $value ) {
					printf
					(
						'<option value="%s"%s>%s</option>',
						$value,
						$value == $current_v ? ' selected="selected"' : '',
						$label
					);
				}
				?>
            </select>
			<?php
		}
	}

	public function cpt_type_column( $column, $post_id ) {
		if ( $column === "ld_type" ) {
			if ( get_post_meta( $post_id, '_ld_index', true ) ) {
				echo 'Index Page';
			} else if ( get_post_meta( $post_id, '_neighborhood', true ) ) {
				echo 'Neighborhood Page';
			} else {
				echo 'City Page';
			}

			return;
		}
	}

	/**
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function display_custom_fields( $columns ) {
		$columns[ 'ld_type' ] = __( 'Type', 'location-domination' );

		return $columns;
	}

	/**
	 * Locate a template by the given UUID.
	 *
	 * @param $uuid
	 *
	 * @return WP_Post|null
	 * @since 2.0.0
	 */
	public static function get_template_by_uuid( $uuid ) {
		$arguments = [
			'post_type'   => LOCATION_DOMINATION_TEMPLATE_CPT,
			'post_status' => 'publish',
			'numberposts' => 1,
			'meta_query'  => [
				'relation' => 'AND',
				[
					'key'     => '_uuid',
					'value'   => $uuid,
					'compare' => '=',
				],
			],
		];

		$posts = get_posts( $arguments );

		return isset( $posts[ 0 ] ) ? $posts[ 0 ] : null;
	}

	/**
	 * Determine the name of the template based upon the
	 * WP_Post object.
	 *
	 * @param int $post_id
	 *
	 * @return mixed|string
	 * @since 2.0.0
	 */
	public static function get_template_name( $post_id ) {
		$uuid = get_post_meta( $post_id, '_uuid', true );

		if ( $uuid ) {
			$template_name = $uuid;
		} else if ( $slug = get_post_field( 'post_name', $post_id ) ) {
			$template_name = $slug;
		} else {
			$template_name = sanitize_title_with_dashes( get_the_title( $post_id ) );
		}

		return $template_name;
	}

	/**
	 * Get a list of dynamically generated post types by
	 * their UUID.
	 *
	 * @param bool $parent_only
	 *
	 * @return array
	 * @since 2.0.8
	 */
	public static function get_dynamic_post_types( $parent_only = true ) {
		$arguments = [
			'post_type'   => LOCATION_DOMINATION_TEMPLATE_CPT,
			'post_status' => 'publish',
			'numberposts' => -1,
		];

		if ( $parent_only ) {
			$arguments[ 'post_parent' ] = 0;
		}

		$uuids = [];
		$custom_post_types = get_posts( $arguments );

		foreach ( $custom_post_types as $post_type ) {
			$uuid = get_post_meta( $post_type->ID, '_uuid', true );

			if ( $uuid ) {
				$uuids[] = $uuid;
			}
		}

		return $uuids;
	}
}
