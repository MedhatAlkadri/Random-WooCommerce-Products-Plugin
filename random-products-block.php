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
        plugins_url( 'fetch.js', __FILE__ ),
        array( 'wp-api-fetch' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'fetch.js' ),
        true
    );

    wp_localize_script( 'random-products-block-fetch', 'randomProductsBlock', array(
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'rest_url' => rest_url( 'random-products/v1/products' ),
    ) );
}
add_action( 'enqueue_block_assets', 'random_products_block_enqueue_assets' );

function random_products_block_register_rest_route() {
    register_rest_route( 'random-products/v1', '/products', array(
        'methods'  => 'GET',
        'callback' => 'random_products_block_get_products',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'random_products_block_register_rest_route' );

function random_products_block_get_products() {
    $url = 'http://localhost:10004/wp-json/wc/v3/products';
    $args = array(
        'method' => 'GET',
        'headers' => array(
            'Authorization' => 'OAuth oauth_consumer_key="' . WC_CONSUMER_KEY . '", oauth_signature_method="HMAC-SHA1", oauth_timestamp="' . time() . '", oauth_nonce="' . wp_generate_password( 12, false ) . '", oauth_version="1.0", oauth_signature="' . base64_encode( hash_hmac( 'sha1', 'GET&' . rawurlencode( $url ) . '&' . rawurlencode( 'oauth_consumer_key=' . WC_CONSUMER_KEY . '&oauth_nonce=' . wp_generate_password( 12, false ) . '&oauth_signature_method=HMAC-SHA1&oauth_timestamp=' . time() . '&oauth_version=1.0' ), WC_CONSUMER_SECRET . '&', true ) ) . '"'
        )
    );

    $response = wp_remote_get( $url, $args );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'rest_api_error', 'Unable to fetch products', array( 'status' => 500 ) );
    }

    $products = json_decode( wp_remote_retrieve_body( $response ), true );

    return rest_ensure_response( $products );
}