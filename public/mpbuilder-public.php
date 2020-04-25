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

	public function get_city() {
		$city = get_post_meta( get_the_ID(), '_city', true );

		return $city;
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