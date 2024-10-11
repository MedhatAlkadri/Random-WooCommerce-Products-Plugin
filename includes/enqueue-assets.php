<?php
/**
 * Enqueue assets for the block.
 */
function random_products_block_enqueue_assets() {
    $plugin_url = plugin_dir_url( dirname(__FILE__) );

    wp_enqueue_script(
        'random-products-block-fetch',
        $plugin_url . 'block.js',
        array( 'wp-api-fetch' ),
        filemtime( plugin_dir_path( dirname(__FILE__) ) . 'block.js' ),
        true
    );

    $base_url = get_site_url();
    $url = $base_url . '/wp-json/wc/v3/products';
    $consumer_key = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION );
    $consumer_secret = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION );

    if ( empty( $consumer_key ) || empty( $consumer_secret ) ) {
        return;
    }

    $params = array(
        'oauth_consumer_key'     => $consumer_key,
        'oauth_nonce'            => wp_generate_password( 12, false ),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp'        => time(),
        'oauth_version'          => '1.0',
        'per_page'               => 10, // Fetch more products to shuffle
        'fields'                 => 'id,name,images,price_html', // Fetch only necessary fields
    );

    $base_info = build_base_string( $url, 'GET', $params );
    $composite_key = rawurlencode( $consumer_secret ) . '&';
    $params['oauth_signature'] = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );

    $query_string = http_build_query( $params );
    $full_url = $url . '?' . $query_string;

    wp_localize_script( 'random-products-block-fetch', 'randomProductsBlock', array(
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'rest_url' => $full_url,
    ) );
}