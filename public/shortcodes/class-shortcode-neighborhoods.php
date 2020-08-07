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
class Shortcode_Neighborhoods implements Shortcode_Interface {

    protected $meta;

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'neighborhoods';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        global $post;

        $city         = get_post_meta( get_the_ID(), '_city', true );
        $neighborhood = get_post_meta( get_the_ID(), '_neighborhood', true );
        $city_parent = null;

        ob_start();

        if ( $neighborhood && $post->post_parent ) {
            $city_parent = get_post( $post->post_parent );
            $city_permalink = get_permalink( $city_parent );

            echo sprintf( '<ul><li><a href="%s">%s</a></li>', $city_permalink, $neighborhood->city );

            return ob_get_clean();
        }

        $query = new \WP_Query( [
            'post_type'  => $post->post_type,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'     => '_neighborhood',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key'   => '_city',
                    'value' => $city,
                ),
            ),
        ] );

        $posts = $query->posts;
        $meta = [];

        foreach ( $posts as $post ) {
            $meta[ $post->ID ] = get_post_meta( $post->ID, '_city', true );
        }

        $this->meta = $meta;

        usort( $posts, [ $this, "cmp" ] );

        if ( count ( $posts ) > 0 ) {
            echo '<ul>';

           foreach ( $posts as $post ) {
               setup_postdata( $post );

                $neighborhood = get_post_meta( $post->ID, '_neighborhood', true );
                $permalink    = get_the_permalink( $post->ID );

                echo sprintf( '<li><a href="%s">%s</a></li>', $permalink, $neighborhood->neighborhood );
            }

            echo '</ul>';

            wp_reset_postdata();
        }

        return ob_get_clean();
    }

    public function cmp( $a, $b ) {
        return strcmp( $this->meta[ $a->ID ], $this->meta[ $b->ID ] );
    }

}
