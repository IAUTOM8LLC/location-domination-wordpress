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
class Shortcode_Internal_Links implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'internal_links';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @param $attributes string
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = [] ) {
        global $post, $wpdb;

        if ( is_admin() || ! $post ) {
            return;
        }

        $attributes = shortcode_atts([
            'city' => null,
            'county' => null,
            'region' => null,
            'state' => null,
            'country' => null,
        ], $attributes );

        /**h
         * Get a collection of higher-grouped countries
         */
        $table = $wpdb->prefix . 'postmeta';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT meta_key, meta_value FROM ${table} WHERE meta_key = '_county' AND meta_value = %s GROUP BY meta_key",
                'Arkansas'
//            'SELECT * FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key = \'_city\' GROUP BY \'\''
        ));

        var_dump($results);
        exit;

        $meta_query = [];

        if ( $attributes[ 'city' ] ) {
            $meta_query = [
                'key'     => '_city',
                'value'   => esc_attr( $attributes[ 'city' ] ),
                'compare' => '=',
            ];
        }

        if ( $attributes[ 'state' ] ) {
            $meta_query = [
                'key'     => '_state',
                'value'   => esc_attr( $attributes[ 'state' ] ),
                'compare' => '=',
            ];
        }

        $arguments = [
            'post_type'   => $post->post_type,
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query'  => array(
                'relation' => 'AND',
                $meta_query,
            ),
        ];

        echo '<ul>';

        foreach ( get_posts( $arguments ) as $post ) {
            echo sprintf( '<li>%s</li>', $post->post_title );
        }

        echo '</ul>';
    }

}