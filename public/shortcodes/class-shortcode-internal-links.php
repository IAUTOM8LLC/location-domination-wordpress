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
        require_once( __DIR__ . '/../../includes/class-location-domination-activator.php' );

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
            'post_type' => null,
            'scope' => 'states', // region, states, counties, cities,
        ], $attributes );

        /**
         * Get a collection of higher-grouped countries
         */
        $meta_query = '';

        if ( $attributes[ 'post_type' ] ) {
            $value = esc_attr( $attributes[ 'post_type' ] );

            $meta_query = "post_type = '${value}'";
        }

        if ( $attributes[ 'city' ] ) {
            $value = esc_attr( $attributes[ 'city' ] );

            $meta_query .= " AND city = '${value}'";
        }

        if ( $attributes[ 'country' ] ) {
            $value = esc_attr( $attributes[ 'country' ] );

            $meta_query .= " AND country = '${value}'";
        }

        if ( $attributes[ 'state' ] ) {
            $value = esc_attr( $attributes[ 'state' ] );

            $meta_query .= " AND state = '${value}'";
        }

        if ( $attributes[ 'region' ] ) {
            $value = esc_attr( $attributes[ 'region' ] );

            $meta_query .= " AND region = '${value}'";
        }

        $meta_query = ltrim( $meta_query, ' AND' );
        $table = Location_Domination_Activator::getTableName();

        $query = "SELECT * FROM ${table} WHERE ${meta_query}";

        $results = $wpdb->get_results( $query );

        echo '<ul>';

        foreach ( $results as $result ) {
            if ( $attributes[ 'scope' ] === 'cities' ) {
                echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $result->post_id ), $result->city );
            } else if ( $attributes[ 'scope' ] === 'region' ) {
                echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $result->post_id ), get_the_title( $result->city ) );
            }
        }

        echo '</ul>';
    }

}
