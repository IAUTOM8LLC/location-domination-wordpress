<?php

if ( ! function_exists( 'array_map_recursive' ) ) {
    function array_map_recursive( $arr, $fn )
    {
        return array_map( function ( $item ) use ( $fn ) {
            return is_array( $item ) ? array_map_recursive( $item, $fn ) : $fn( $item );
        }, $arr );
    }
}

if ( ! function_exists( 'is_json' ) ) {
    function is_json( $string )
    {
        json_decode( $string );

        return ( json_last_error() == JSON_ERROR_NONE );
    }
}

if ( ! function_exists( 'is_beaverbuilder_installed' ) ) {
    function is_beaverbuilder_installed()
    {
        return class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_enabled();
    }
}

if ( ! function_exists( 'ld_get_template_post_types' ) ) {
    function ld_get_template_post_types()
    {
        $templates = new \WP_Query( [
            'posts_per_page' => -1,
            'post_type' => LOCATION_DOMINATION_TEMPLATE_CPT,
        ] );

        return array_filter( array_map( function ( $template ) {
            return get_post_meta( $template->ID, "_uuid", true );
        }, $templates->posts ) );
    }
}