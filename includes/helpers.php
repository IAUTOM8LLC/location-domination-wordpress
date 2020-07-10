<?php

if ( ! function_exists( 'array_map_recursive' ) ) {
    function array_map_recursive( $arr, $fn ) {
        return array_map( function ( $item ) use ( $fn ) {
            return is_array( $item ) ? array_map_recursive( $item, $fn ) : $fn( $item );
        }, $arr );
    }
}

if ( ! function_exists( 'is_json' ) ) {
    function is_json( $string ) {
        json_decode( $string );

        return ( json_last_error() == JSON_ERROR_NONE );
    }
}

if ( ! function_exists( 'maybe_create_table' ) ) {
    function maybe_create_table( $table_name, $create_ddl ) {
        global $wpdb;

        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

        if ( $wpdb->get_var( $query ) == $table_name ) {
            return true;
        }

        // Didn't find it, so try to create it.
        $wpdb->query( $create_ddl );

        // We cannot directly tell that whether this succeeded!
        if ( $wpdb->get_var( $query ) == $table_name ) {
            return true;
        }

        return false;
    }
}
