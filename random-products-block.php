<?php
/**
 * Plugin Name: Random Products Block
 * Description: A Gutenberg block to display 3 random WooCommerce products.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include the configuration file
if ( file_exists( plugin_dir_path( __FILE__ ) . 'config.php' ) ) {
    include( plugin_dir_path( __FILE__ ) . 'config.php' );
} else {
    exit( 'Configuration file not found.' );
}

function random_products_block_register_block() {
    wp_register_script(
        'random-products-block-editor-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
    );

    register_block_type( 'random-products/random-products-block', array(
        'editor_script' => 'random-products-block-editor-script',
    ) );
}
add_action( 'init', 'random_products_block_register_block' );

function random_products_block_enqueue_assets() {
    wp_enqueue_script(
        'random-products-block-fetch',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-api-fetch' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'block.js' ),
        true
    );

    $url = 'http://localhost:10004/wp-json/wc/v3/products';
    $params = array(
        'oauth_consumer_key'     => WC_CONSUMER_KEY,
        'oauth_nonce'            => wp_generate_password( 12, false ),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp'        => time(),
        'oauth_version'          => '1.0',
        'per_page'               => 3,
    );

    $base_info = build_base_string( $url, 'GET', $params );
    $composite_key = rawurlencode( WC_CONSUMER_SECRET ) . '&';
    $params['oauth_signature'] = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );

    $query_string = http_build_query( $params );
    $full_url = $url . '?' . $query_string;

    wp_localize_script( 'random-products-block-fetch', 'randomProductsBlock', array(
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'rest_url' => $full_url,
    ) );
}
add_action( 'enqueue_block_assets', 'random_products_block_enqueue_assets' );

function build_base_string( $baseURI, $method, $params ) {
    $r = array();
    ksort( $params );
    foreach ( $params as $key => $value ) {
        $r[] = "$key=" . rawurlencode( $value );
    }
    return $method . "&" . rawurlencode( $baseURI ) . '&' . rawurlencode( implode( '&', $r ) );
}