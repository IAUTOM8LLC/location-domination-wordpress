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

    /**
     * @var
     */
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
     * @return false|mixed
     */
    private function has_neighborhood_pages_enabled() {
        global $post;

        $template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post->post_type );

        if ( empty( $template ) ) {
            return false;
        }

        return get_field("create_neighborhood_pages", $template->ID );
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

        $created_neighborhood_pages = $this->has_neighborhood_pages_enabled();

        ob_start();

        if ( $created_neighborhood_pages ) {
            if ( $neighborhood && $post->post_parent ) {
                $city_parent = get_post( $post->post_parent );
                $city_permalink = get_permalink( $city_parent );

                echo sprintf( '<ul><li><a href="%s">%s</a></li>', $city_permalink, $neighborhood->city );

                return ob_get_clean();
            }

            $query = new \WP_Query( [
                'post_type'  => $post->post_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
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
        } else {
            $neighborhoods = get_post_meta( $post->ID, "_neighborhoods", true );

            if ( ! is_array( $neighborhoods ) || empty( $neighborhoods ) ) {
                return "";
            }

            echo '<ul>';

            foreach ( $neighborhoods as $neighborhood ) {
                echo sprintf( "<li>%s</li>", $neighborhood );
            }

            echo '</ul>';
        }

        return ob_get_clean();
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    public function cmp( $a, $b ) {
        return strcmp( $this->meta[ $a->ID ], $this->meta[ $b->ID ] );
    }

}
