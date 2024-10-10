<?php
/**
 * Plugin Name: Random Products Block
 * Description: A Gutenberg block to display 3 random WooCommerce products.
 * Version: 1.0
 * Author: Medhat Alkadri
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

    wp_register_style(
        'random-products-block-style',
        plugins_url( 'style.css', __FILE__ ),
        array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
    );

    register_block_type( 'random-products/random-products-block', array(
        'editor_script' => 'random-products-block-editor-script',
        'style' => 'random-products-block-style',
        'render_callback' => 'random_products_block_render_callback',
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
        'per_page'               => 10, // Fetch more products to shuffle
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

function random_products_block_render_callback() {
    $url = 'http://localhost:10004/wp-json/wc/v3/products';
    $params = array(
        'oauth_consumer_key'     => WC_CONSUMER_KEY,
        'oauth_nonce'            => wp_generate_password( 12, false ),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp'        => time(),
        'oauth_version'          => '1.0',
        'per_page'               => 10, // Fetch more products to shuffle
    );

    $base_info = build_base_string( $url, 'GET', $params );
    $composite_key = rawurlencode( WC_CONSUMER_SECRET ) . '&';
    $params['oauth_signature'] = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );

    $query_string = http_build_query( $params );
    $full_url = $url . '?' . $query_string;

    // Debugging: Output the full URL
    error_log( 'Full URL: ' . $full_url );

    $response = wp_remote_get( $full_url, array( 'timeout' => 25 ) ); // Increase timeout to 25 seconds

    if ( is_wp_error( $response ) ) {
        error_log( 'Error fetching products: ' . $response->get_error_message() );
        return '<p>Unable to fetch products</p>';
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        error_log( 'Unexpected response code: ' . $response_code );
        return '<p>Unable to fetch products</p>';
    }

    $products = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! is_array( $products ) ) {
        error_log( 'Unexpected response format: ' . wp_remote_retrieve_body( $response ) );
        return '<p>Unexpected response format</p>';
    }

    // Shuffle products to get random ones
    shuffle( $products );
    $products = array_slice( $products, 0, 3 );

    ob_start();
    ?>
    <div class="random-products-block">
        <?php foreach ( $products as $product ) : ?>
            <div class="product">
                <img src="<?php echo esc_url( $product['images'][0]['src'] ); ?>" alt="<?php echo esc_attr( $product['name'] ); ?>">
                <h2><?php echo esc_html( $product['name'] ); ?></h2>
                <p><?php echo wp_kses_post( $product['price_html'] ); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

function build_base_string( $baseURI, $method, $params ) {
    $r = array();
    ksort( $params );
    foreach ( $params as $key => $value ) {
        $r[] = "$key=" . rawurlencode( $value );
    }
    return $method . "&" . rawurlencode( $baseURI ) . '&' . rawurlencode( implode( '&', $r ) );
}