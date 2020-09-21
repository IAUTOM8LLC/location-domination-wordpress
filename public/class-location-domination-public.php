<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://i-autom8.com
 * @since      1.0.0
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Location_Domination
 * @subpackage Location_Domination/public
 * @author     iAutoM8 LLC <support@i-autom8.com>
 */
class Location_Domination_Public {

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
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     *
     * @since    1.0.0
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Displays schema on the frontend.
     *
     * @return void
     * @since 2.0.4
     */
    public function display_schema() {
        global $post;

        if ( ! $post ) {
            return;
        }

        $template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post->post_type );

        if ( ! $template || ! get_field( 'enable_job_schema', $template->ID ) ) {
            return;
        }

        $schema = get_post_meta( $template->ID, 'location_domination_schema_template', true );

        if ( ! $schema ) {
            return;
        }

        $shortcode_bindings = [
            '[city]'      => get_post_meta( $post->ID, '_city', true ),
            '[county]'    => get_post_meta( $post->ID, '_county', true ),
            '[state]'     => get_post_meta( $post->ID, '_state', true ),
            '[zips]'      => get_post_meta( $post->ID, '_zips', true ),
            '[zip_codes]' => get_post_meta( $post->ID, '_zips', true ),
            '[region]'    => get_post_meta( $post->ID, '_region', true ),
            '[country]'   => get_post_meta( $post->ID, '_country', true ),
        ];

        $schema = str_replace( [ '<script type="application/ld+json">', '</script>' ], '', $schema );
        $schema = apply_filters( 'location_domination_shortcodes', $schema, $shortcode_bindings );

        $json_schema = json_decode( $schema );

        if ( ! $json_schema ) {
            return;
        }

        if ( isset( $json_schema->title ) ) {
            $json_schema->title = Location_Domination_Spinner::spin( $json_schema->title, $post->ID );
        }

        if ( isset( $json_schema->description ) ) {
            $json_schema->description = Location_Domination_Spinner::spin( $json_schema->description, $post->ID );
        }

        echo '<script type="application/ld+json">' . json_encode( $json_schema ) . '</script>';
    }

    /**
     * Remove the template slug from any links if the user
     * has enabled it in their template settings.
     *
     * @param $post_link
     * @param $post
     *
     * @return string|string[]
     * @since 2.0.0
     */
    function remove_template_slug_from_links( $post_link, $post ) {
        $template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post->post_type );

        if ( ! $template || get_field( 'use_template_slug', $template->ID ) ) {
            return $post_link;
        }

        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

        return $post_link;
    }

    /**
     * Remove the template slug from the request if the user
     * has enabled it in their template settings.
     *
     * @param $query
     */
    function remove_template_slug_from_request( $query ) {
        if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query[ 'page' ] ) ) {
            return;
        }

        if ( ! empty( $query->query[ 'name' ] ) ) {
            $arguments = [
                'post_type'   => LOCATION_DOMINATION_TEMPLATE_CPT,
                'post_status' => 'publish',
                'numberposts' => - 1,
                'post_parent' => 0,
                'meta_query'  => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'use_template_slug',
                        'value'   => 0,
                        'compare' => '=',
                    ),
                ),
            ];

            $posts = get_posts( $arguments );

            $types = array( 'post', 'single-link', 'page' );

            if ( count( $posts ) > 0 ) {
                foreach ( $posts as $post ) {
                    $type = get_post_meta( $post->ID, '_uuid', true );

                    if ( $type ) {
                        $types[] = $type;
                    }
                }
            }

            $query->set( 'post_type', $types );
        } else if ( ! empty( $query->query[ 'pagename' ] ) && false === strpos( $query->query[ 'pagename' ], '/' ) ) {
            $query->set( 'post_type', array( 'post', 'single-link', 'page' ) );

            // We also need to set the name query var since redirect_guess_404_permalink() relies on it.
            $query->set( 'name', $query->query[ 'pagename' ] );
        }
    }

}
