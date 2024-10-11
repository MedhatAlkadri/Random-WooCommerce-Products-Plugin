<?php
/**
 * Build the base string for OAuth signature.
 *
 * @param string $baseURI The base URI.
 * @param string $method The HTTP method.
 * @param array $params The parameters.
 * @return string The base string.
 */
function build_base_string( $baseURI, $method, $params ) {
    $r = array();
    ksort( $params );
    foreach ( $params as $key => $value ) {
        $r[] = "$key=" . rawurlencode( $value );
    }
    return $method . "&" . rawurlencode( $baseURI ) . '&' . rawurlencode( implode( '&', $r ) );
}