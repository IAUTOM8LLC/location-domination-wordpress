<?php


class create_tables {

	function create_all_cities_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . "all_cities";

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "DROP TABLE IF EXISTS $table_name;CREATE TABLE $table_name (
  				id mediumint(11) NOT NULL AUTO_INCREMENT,
  				code int NOT NULL,
  				lat text ,
  				lon text ,
  				state text NOT NULL,
  				abbr text NOT NULL,
  				county text NOT NULL,
  				city text NOT NULL,
  				PRIMARY KEY  (id)
			) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );



	}

}