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

define( 'WC_CONSUMER_KEY', 'ck_3b19409a13312704121a7adc830e471a50991bee' );
define( 'WC_CONSUMER_SECRET', 'cs_435e86d4ae7d205fb67dea71dcde6f85de28d0b1' );

function random_products_block_register_block() {
    wp_register_script(
        'random-products-block-editor-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
    );

    wp_register_style(
        'random-products-block-editor-style',
        plugins_url( 'editor.css', __FILE__ ),
        array( 'wp-edit-blocks' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' )
    );

    register_block_type( 'random-products/random-products-block', array(
        'editor_script' => 'random-products-block-editor-script',
        'editor_style'  => 'random-products-block-editor-style',
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
    $response = wp_remote_get( add_query_arg( array(
        'consumer_key'    => WC_CONSUMER_KEY,
        'consumer_secret' => WC_CONSUMER_SECRET,
        'per_page'        => 3,
        'orderby'         => 'rand',
    ), 'http://localhost:10004/wp-json/wc/v3/products' ) );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'rest_api_error', 'Unable to fetch products', array( 'status' => 500 ) );
    }

    $products = json_decode( wp_remote_retrieve_body( $response ), true );

    return rest_ensure_response( $products );
}