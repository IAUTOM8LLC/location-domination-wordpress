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
class Shortcode_Suburbs implements Shortcode_Interface {

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
        return 'suburbs';
    }

    /**
     * @return false|mixed
     */
    private function has_suburb_pages_enabled() {
        global $post;

        $template = Location_Domination_Custom_Post_Types::get_template_by_uuid( $post->post_type );

        if ( empty( $template ) ) {
            return false;
        }

        return get_field("create_suburb_pages", $template->ID );
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
        $suburb = get_post_meta( get_the_ID(), '_suburb', true );
        $city_parent = null;

        $created_suburb_pages = $this->has_suburb_pages_enabled();

        ob_start();

        if ( $created_suburb_pages ) {
            if ( $suburb && $post->post_parent ) {
                $city_parent = get_post( $post->post_parent );
                $city_permalink = get_permalink( $city_parent );

                echo sprintf( '<ul><li><a href="%s">%s</a></li>', $city_permalink, $suburb->suburb );

                return ob_get_clean();
            }

            $query = new \WP_Query( [
                'post_type'  => $post->post_type,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_suburb',
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

                    $suburb = get_post_meta( $post->ID, '_suburb', true );
                    $permalink    = get_the_permalink( $post->ID );

                    echo sprintf( '<li><a href="%s">%s</a></li>', $permalink, $suburb->suburb );
                }

                echo '</ul>';

                wp_reset_postdata();
            }
        } else {
            $suburbs = get_post_meta( $post->ID, "_suburbs", true );

            if ( ! is_array( $suburbs ) || empty( $suburbs ) ) {
                return "";
            }

            echo '<ul>';

            foreach ( $suburbs as $suburb ) {
                echo sprintf( "<li>%s</li>", $suburb );
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
