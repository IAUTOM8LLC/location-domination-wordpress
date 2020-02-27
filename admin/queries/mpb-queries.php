<?php


class mpb_queries {


	public function query_all( $increment, $offset ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'all_cities';
		$results    = $wpdb->get_results( "SELECT distinct(city), id, state, code, lat, lon, county FROM $table_name group by (city) order by id LIMIT $increment OFFSET $offset", OBJECT );

		return $results;
	}

	public function insert_cities() {
		global $wpdb;
		$this->api = new LocationDominationAPI();
		//query the data
		$api = new LocationDominationAPI();

		$results = $api->get_all_cities();

		if ( $results ) {
			foreach ( $results as $result ) {
				$table_name = $wpdb->prefix . 'all_cities';

				$wpdb->insert(
					$table_name,
					array(
						'id'     => $result->id,
						'city'   => $result->city,
						'state'  => $result->state,
						'code'   => $result->code,
						'county' => $result->county,
						'lat'    => $result->lat,
						'lon'    => $result->lon,
						'abbr'   => $result->abbr
					)
				);

			}
		}

	}

	public function add_dynamic_posts( $post_ID ) {


	}


}