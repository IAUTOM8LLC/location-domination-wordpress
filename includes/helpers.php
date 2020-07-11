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
