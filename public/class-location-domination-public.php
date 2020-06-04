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
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Remove the template slug from any links if the user
     * has enabled it in their template settings.
     *
     * @param $post_link
     * @param $post
     *
     * @since 2.0.0
     * @return string|string[]
     */
    function remove_template_slug_from_links( $post_link, $post ) {
        $template  = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post->post_type );

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
                'numberposts' => 1,
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
            $types = [ 'post', 'page' ];

            if ( count( $posts ) > 0 ) {
                foreach ( $posts as $post ) {
                    $type = get_post_meta( $post->ID, '_uuid', true );

                    if ( $type ) {
                        $types[] = $type;
                    }
                }

                $query->set( 'post_type', $types );
            }
        }
    }

}
