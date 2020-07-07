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

        $attributes = shortcode_atts( [
            'city'      => null,
            'county'    => null,
            'region'    => null,
            'state'     => null,
            'country'   => null,
            'post_type' => null,
            'scope'     => 'states', // region, states, counties, cities,
        ], $attributes );

        if ( $attributes[ 'scope' ] === 'cities' ) {

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

            if ( $attributes[ 'county' ] ) {
                $value = esc_attr( $attributes[ 'county' ] );

                $meta_query .= " AND county = '${value}'";
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
            $table      = Location_Domination_Activator::getTableName();

            $query = "SELECT * FROM ${table} WHERE ${meta_query}";

            $results = $wpdb->get_results( $query );

            echo '<ul>';

            foreach ( $results as $result ) {
                echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $result->post_id ), $result->city );

//                else if ( $attributes[ 'scope' ] === 'region' ) {
//                    echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $result->post_id ), get_the_title( $result->city ) );
//                }
            }

            echo '</ul>';
        } else if ( $attributes[ 'scope' ] === 'counties' ) {
            $state    = esc_attr( $attributes[ 'state' ] );
            $counties = $this->get_counties_in_state( $state );

            if ( $counties ) {
                // context = State, County
                $counties_contexts = [];

                foreach ( $counties as $record ) {
                    $counties_contexts[] = sprintf( '%s, %s', $record->state, $record->county );
                }

                $posts = array(
                    'post_type'  => $post->post_type,
                    'fields'     => 'ids',
                    'meta_query' => array(
                        array(
                            'key'     => 'county',
                            'value'   => $counties_contexts,
                            'compare' => 'IN'
                        )
                    )
                );

                $query = new \WP_Query( $posts );

                if ( $query->have_posts() ) {
                    echo '<ul>';

                    foreach ( $query->posts as $post_id ) {
                        echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $post_id ), get_the_title( $post_id ) );
                    }

                    echo '</ul>';
                }
            }
        } else if ( $attributes[ 'scope' ] === 'states' ) {
            $country    = esc_attr( $attributes[ 'country' ] );
            $states = $this->get_states_in_country( $country );

            if ( $states ) {
                // context = State
                $state_contexts = [];

                foreach ( $states as $record ) {
                    $state_contexts[] = $record->state;
                }

                $posts = array(
                    'post_type'  => $post->post_type,
                    'fields'     => 'ids',
                    'meta_query' => array(
                        array(
                            'key'     => 'state',
                            'value'   => $state_contexts,
                            'compare' => 'IN'
                        )
                    )
                );

                $query = new \WP_Query( $posts );

                if ( $query->have_posts() ) {
                    echo '<ul>';

                    foreach ( $query->posts as $post_id ) {
                        echo sprintf( '<li><a href="%s">%s</a></li>', get_the_permalink( $post_id ), get_the_title( $post_id ) );
                    }

                    echo '</ul>';
                }
            }
        }
    }

    protected function get_counties_in_state( $state ) {
        global $wpdb, $post;

        $table_name = Location_Domination_Activator::getTableName();

        $query = $wpdb->prepare( "SELECT state, county FROM ${table_name} WHERE post_type = '%s' AND state = '%s' GROUP BY state, county", $post->post_type, $state );

        return $wpdb->get_results( $query );
    }

    protected function get_states_in_country( $country ) {
        global $wpdb, $post;

        $table_name = Location_Domination_Activator::getTableName();

        $query = $wpdb->prepare( "SELECT state FROM ${table_name} WHERE post_type = '%s' AND country = '%s' GROUP BY state", $post->post_type, $country );

        return $wpdb->get_results( $query );
    }

}
