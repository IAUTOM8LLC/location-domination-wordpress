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
class Location_Domination_Custom_Post_Types {

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
        'menu_name'          => 'Mass Page Templates'
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
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'page',
        'has_archive'        => true,
        'hierarchical'       => true,
        'menu_position'      => null,
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
     * Register all of the custom post types that
     * we need to operate the plugin. This includes the main
     * templates CPT, and sub-type templates.
     *
     * @return void
     * @since 2.0.0
     */
    public function register() {
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
                if ( ! current_user_can( 'editor' ) && ! current_user_can( 'administrator' ) ) {
                    wp_redirect( esc_url_raw( get_bloginfo( 'url' ) ), 301 );
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
        $template  = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post_type );

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
        ];

        $custom_post_types = get_posts( $arguments );

        foreach ( $custom_post_types as $post ) {
            setup_postdata( $post );

            $title    = get_the_title( $post->ID );
            $singular = $title;
//            $singular = \Doctrine\Common\Inflector\Inflector::singularize( $title );

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

            $use_template_slug = get_field( 'use_template_slug', $post->ID );

            $arguments = [
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => ! $use_template_slug ? [
                    'slug'       => '/',
                    'with_front' => false
                ] : [ 'slug' => $post->post_name ],
                'capability_type'    => 'page',
                'has_archive'        => true,
                'hierarchical'       => false,
                'supports'           => [
                    'title',
                    'editor',
                    'excerpt',
                    'thumbnail',
                    'custom-fields',
                ],
            ];

            $template_name = self::get_template_name( $post );

            register_post_type( $template_name, $arguments );

            wp_reset_postdata();
        }
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
            'meta_query'  => array(
                'relation' => 'AND',
                array(
                    'key'     => '_uuid',
                    'value'   => $uuid,
                    'compare' => '=',
                ),
            ),
        ];

        $posts = get_posts( $arguments );

        return isset( $posts[ 0 ] ) ? $posts[ 0 ] : null;
    }

    /**
     * Determine the name of the template based upon the
     * WP_Post object.
     *
     * @param \WP_Post $post
     *
     * @return mixed|string
     * @since 2.0.0
     */
    public static function get_template_name( WP_Post $post ) {
        $uuid = get_post_meta( $post->ID, '_uuid', true );

        if ( $uuid ) {
            $template_name = $uuid;
        } else if ( $post->post_name ) {
            $template_name = $post->post_name;
        } else {
            $template_name = sanitize_title_with_dashes( $post->post_title );
        }

        return $template_name;
    }
}
