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
class Shortcode_RelatedCityNoLink implements Shortcode_Interface {

    /**
     * The shortcode name.
     *
     * @return string
     * @since 2.0.0
     */
    public function get_key() {
        return 'ld_nearby_cities_county_only_no_links';
    }

    /**
     * The contents to replace the shortcode with
     *
     * @return string
     * @since 2.0.0
     */
    public function handle( $attributes = null ) {
        $request = get_post_meta( get_the_ID(), 'location_domination_post_request', true );
        $county = get_post_meta( get_the_ID(), '_county', true );
        $state = get_post_meta( get_the_ID(), '_state', true );
        if (!empty($county)) {
            $parent_id = get_the_ID();
            $args = array( 
                'post_type'  => get_post_type(get_the_ID()),
                'posts_per_page' => '-1', 
                'meta_query' => array(
                    array(
                        'key'     => '_county',
                        'value'   => [$county],
                        'compare' => 'IN',         
                    ),
                ),
                'orderby'        => 'meta_value',  // Order by meta value
                'meta_key'       => '_city', // Specify the meta field for ordering
                'order'          => 'ASC', // You can change the order to ASC or DESC as needed
                'meta_compare'   => 'EXISTS', // Only consider posts where _city exists
            );
            $links = '';
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) {
                $links = "<br>We also serve the following cities in $county, $state <br>";
                while ( $query->have_posts() ) {
                    $query->the_post();
                    if ($parent_id != get_the_ID()) {
                        $links .= "<p class='ld_nearby_cities_county_only_no_links'>".get_post_meta( get_the_ID(), '_city', true )."</p>";
                    }
                }
                wp_reset_postdata();
            }
            return $links;
        }
    }

}
