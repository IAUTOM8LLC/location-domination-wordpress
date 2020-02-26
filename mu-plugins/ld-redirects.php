<?php

function redirect_by_request_uri() {
    $array = get_option( 'ld_redirect_posttypes' );

    foreach ( $array as $item ) {
        if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {

            // Store uri and create array of uri parts
            $request_uri = $_SERVER[ 'REQUEST_URI' ];
            $parts       = explode( '/', $request_uri );

            // Check post slug
            if ( strpos( $parts[ 1 ], $item ) !== false ) {
                $redirect = get_home_url();
                wp_redirect( $redirect, 301 );
                exit;
            }

        }
    }
}

add_action( 'template_redirect', 'redirect_by_request_uri' );
