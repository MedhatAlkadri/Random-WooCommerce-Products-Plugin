<?php
/**
 * Render callback for the block.
 *
 * @return string HTML content for the block.
 */
function random_products_block_render_callback() {
    $consumer_key = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION );
    $consumer_secret = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION );

    if ( empty( $consumer_key ) || empty( $consumer_secret ) ) {
        return '<p>Please enter your WooCommerce API credentials in the <a href="' . esc_url( admin_url( 'admin.php?page=random-products-block' ) ) . '">plugin settings</a>.</p>';
    }

    $transient_key = 'random_products_block';
    $products = get_transient( $transient_key );

    if ( false === $products ) {
        $base_url = get_site_url();
        $url = $base_url . '/wp-json/wc/v3/products';

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

        // Cache the products for 1 hour
        set_transient( $transient_key, $products, HOUR_IN_SECONDS );
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