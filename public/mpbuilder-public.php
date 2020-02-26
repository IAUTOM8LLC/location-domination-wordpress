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

	public function mpbuilder_flush_permalinks() {
	    if ( $flush = get_option( 'mpb_flush_permalinks' ) ) {
	        if ( $flush && (int) $flush === 1 ) {
                delete_option( 'mpb_flush_permalinks' );
	            flush_rewrite_rules();
            }
        }
    }

	public function mpbuilder_publish_schema( ) {
		$templatepart = get_post_meta( get_the_ID(), '_schema_type', true );

		switch ( $templatepart ) {
			case 1:
				$part = 'event';
				break;
			case 2:
				$part = 'job';
				break;
			case 3:
				$part = 'product';
				break;
			case 4:
				$part = 'review';
				break;
			case 5:
				$part = 'recipe';
				break;
			case 6:
				$part = 'creative';
				break;
			default:
				$part = 'article';
				break;

		}
		echo $this->get_template_html( 'article' );
		return $this->get_template_html( 'article' );
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

	public function mpb_breadcrumb(){
		$state = get_post_meta( get_the_ID(), '_state', true );
		$county = get_post_meta( get_the_ID(), '_county', true );
		$city = get_post_meta( get_the_ID(), '_city', true );

		return $state . ' >> ' . $county . ' >> ' . $city;


	}

}