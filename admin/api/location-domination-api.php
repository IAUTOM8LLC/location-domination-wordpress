<?php


/**
 * Class LocationDominationAPI
 */
class LocationDominationAPI extends WP_REST_Controller {

    /**
     * @return mixed
     */
    public function get_all_cities(){
		$body = file_get_contents( MPBUILDER_DATA_PATH . '/cities.json' );

		$json = json_decode( $body );

		return $json;
	}

    /**
     * @return mixed
     */
    public function get_all_states(){
        $data = wp_remote_get( 'https://spintax.noonanwebgroup.com/api/states' );

        $body = wp_remote_retrieve_body( $data );
        $json = json_decode( $body );

        return $json;
	}

    /**
     * @param $state_id
     *
     * @return mixed
     */public function get_counties( $state_id ){
        $data = wp_remote_get( 'https://spintax.noonanwebgroup.com/api/counties?state=' . $state_id );

        $body = wp_remote_retrieve_body( $data );
        $json = json_decode( $body );

        return $json;
	}

    /**
     * @param $post_ID
     *
     * @return mixed
     */public function get_cities_count( $post_ID ){

		 $counties    = get_post_meta( $post_ID, '_selected_counties', true );
		 foreach ( $counties as $county ) {
			 $county_ids[] = $county;
		 }

         $data = wp_remote_get( 'https://spintax.noonanwebgroup.com/api/cities?count=true&filter=' . implode( ',', $county_ids ) );

         $body = wp_remote_retrieve_body( $data );
		 $json = json_decode( $body );
		 return $json;
	 }

    /**
     * @param $county_ids
     * @param $increment
     * @param $offset
     *
     * @return mixed
     */public function get_selected_cities( $county_ids, $increment, $offset ){

		$data = wp_remote_get( 'https://spintax.noonanwebgroup.com/api/cities?offset='.$offset.'&limit='.$increment.'&filter='. implode( ",", $county_ids) );

		$body = wp_remote_retrieve_body( $data );
		$json = json_decode( $body );

		return $json;

	}

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->includes();

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Include the controller classes
     *
     * @return void
     */
    private function includes() {
        require_once __DIR__ . '/Api/PostCreation.php';
    }

    /**
     * Register the API routes
     *
     * @return void
     */
    public function register_routes() {
        ( new App\Api\PostCreation() )->register_routes();
    }

}
