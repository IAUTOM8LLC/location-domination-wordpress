<?php
add_filter( 'the_title', 'do_shortcode');

class mpbuilder_public {


	public function spintax_page_content( $content ) {
	    if ( $spun_content = get_post_meta( get_the_ID(), 'spun_content', true ) ) {
	        return $spun_content;
        }

	    $spintax = new Spintax();
	    $spun_content = $spintax->process( $content );

	    update_post_meta( get_the_ID(), 'spun_content', $spun_content );

	    return $spun_content;
	}

    public function spintax_comments_open( $open, $post_id ) {
        $post_type_query = new \WP_Query(
            array(
                'post_type'      => 'mptemplates',
                'posts_per_page' => - 1
            )
        );

        $posts_array      = $post_type_query->posts;
        $post_types = [];

        foreach ( $posts_array as $post ) {
            $post_types[] = get_post_meta( $post->ID, '_uuid', true );
        }

        $post_type = get_post_type( $post_id );

        // allow comments for built-in "post" post type
        if ( in_array( $post_type, $post_types ) ) {
            return false;
        }
        // disable comments for any other post types
        return true;
    }

    public function allow_iframe( $tags, $context ) {
        if ( 'post' === $context ) {
            $tags['iframe'] = array(
                'src'             => true,
                'height'          => true,
                'width'           => true,
                'frameborder'     => true,
                'allowfullscreen' => true,
            );
        }

        return $tags;
    }

	public function mpbuilder_flush_permalinks() {
	    if ( $flush = get_option( 'mpb_flush_permalinks' ) ) {
	        if ( $flush && (int) $flush === 1 ) {
                delete_option( 'mpb_flush_permalinks' );
	            flush_rewrite_rules();
            }
        }
    }

	public function mpbuilder_publish_schema( ) {
        $post_types = array_merge( [ 'page', 'post' ], array_keys(get_post_types( [ 'public' => true, '_builtin' => false ], 'names', 'and' ) ));

        if ( is_singular( $post_types ) ) {
            $jsonString = strip_tags( get_post_meta( get_the_ID(), '_ld_schema', true ) );

            if ( $jsonString ) {
                if ( $schemaArray = json_decode( $jsonString ) ) {
                    // Decode to make sure is a valid JSON object
                    echo '<script id="ld" type="application/ld+json">' . json_encode( $schemaArray ) . '</script>';
                }
            }
        }
	}


	private function get_template_html( $template_name ) {

		ob_start();

		include plugin_dir_path( dirname( __FILE__ ) ) .'public/schema/templates-parts/' . $template_name . '.php';

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function page_list($atts) {
	    global $post;

	    if ( is_admin() || ! $post ) {
	        return;
        }

        $a = shortcode_atts( array(
            'region' => null,
            'country' => null,
        ), $atts );

	    $search = array(
            'key'     => '_region',
            'value'   => esc_attr($a['region']),
            'compare' => '=',
        );

	    if ( $a['country'] ) {
	        $search = array(
                'key'     => '_region_index',
                'value'   => esc_attr($a['country']),
                'compare' => '=',
            );
        }

        $args = array(
            'post_type' => $post->post_type,
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                $search,
            )
        );

        $posts_in_region = get_posts( $args );

        echo '<ul>';
        foreach ( $posts_in_region as $post ) {
            $city = get_post_meta( $post->ID, '_city', true );
            $title = sprintf('%s, %s', $city, esc_attr($a['region']));

            echo '<li><a href="' . get_permalink($post) . '">' . $title . '</a></li>';
        }
        echo '</ul>';
	}

	public function get_city() {
		$city = get_post_meta( get_the_ID(), '_city', true );

		return $city;
	}

	public function get_region() {
		$region = get_post_meta( get_the_ID(), '_region', true );

		return $region;
	}

	public function get_map() {
        $country = get_post_meta( get_the_ID(), '_country', true );
        $region = get_post_meta( get_the_ID(), '_region', true );
        $city = get_post_meta( get_the_ID(), '_city', true );
        $county = get_post_meta( get_the_ID(), '_county', true );

        if ( $city && $county ) {
            $query = sprintf('%s, %s, United States', $city, $county );
        } else {
            $query = sprintf( '%s, %s', $city, $region, $country );
        }

		if ( $query && trim( $query ) !== ',' ) {
		    return sprintf('<iframe width="400" height="300" src="https://maps.google.com/maps?width=400&height=300&hl=en&q=%s&ie=UTF8&t=&z=14&iwloc=B&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>', urlencode($query));
        }
	}

	public function get_country() {
		$country = get_post_meta( get_the_ID(), '_country', true );

		return $country;
	}


	public function get_state(  ) {
		$state = get_post_meta( get_the_ID(), '_state', true );
		return $state;
	}


	public function get_county(  ) {
		$county = get_post_meta( get_the_ID(), '_county', true );
		return $county;
	}

	public function get_zips(  ) {
		$zips = get_post_meta( get_the_ID(), '_zips', true );
		return str_replace(',', ', ', $zips);
	}

	public function mpb_breadcrumb(){
		$state = get_post_meta( get_the_ID(), '_state', true );
		$county = get_post_meta( get_the_ID(), '_county', true );
		$city = get_post_meta( get_the_ID(), '_city', true );

		return $state . ' >> ' . $county . ' >> ' . $city;


	}

}