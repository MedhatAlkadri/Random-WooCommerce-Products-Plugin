<?php
/**
 * Add settings page under WooCommerce menu.
 */
function random_products_block_add_admin_menu() {
    add_submenu_page(
        'woocommerce',
        'Random Products Block Settings',
        'Random Products Block',
        'manage_options',
        'random-products-block',
        'random_products_block_options_page'
    );
}

/**
 * Register settings for the plugin.
 */
function random_products_block_settings_init() {
    register_setting( 'randomProductsBlock', RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION );
    register_setting( 'randomProductsBlock', RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION );

    add_settings_section(
        'random_products_block_section',
        __( 'WooCommerce API Settings', 'random-products-block' ),
        'random_products_block_settings_section_callback',
        'randomProductsBlock'
    );

    add_settings_field(
        'random_products_block_consumer_key',
        __( 'Consumer Key', 'random-products-block' ),
        'random_products_block_consumer_key_render',
        'randomProductsBlock',
        'random_products_block_section'
    );

    add_settings_field(
        'random_products_block_consumer_secret',
        __( 'Consumer Secret', 'random-products-block' ),
        'random_products_block_consumer_secret_render',
        'randomProductsBlock',
        'random_products_block_section'
    );
}

/**
 * Render the consumer key input field.
 */
function random_products_block_consumer_key_render() {
    $consumer_key = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION );
    ?>
    <input type='text' name='<?php echo RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION; ?>' value='<?php echo esc_attr( $consumer_key ); ?>'>
    <?php
}

/**
 * Render the consumer secret input field.
 */
function random_products_block_consumer_secret_render() {
    $consumer_secret = get_option( RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION );
    ?>
    <input type='text' name='<?php echo RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION; ?>' value='<?php echo esc_attr( $consumer_secret ); ?>'>
    <?php
}

/**
 * Settings section callback.
 */
function random_products_block_settings_section_callback() {
    echo __( 'Enter your WooCommerce API credentials here.', 'random-products-block' );
}

/**
 * Render the settings page.
 */
function random_products_block_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Random Products Block Settings</h2>
        <?php
        settings_fields( 'randomProductsBlock' );
        do_settings_sections( 'randomProductsBlock' );
        submit_button();
        ?>
    </form>
    <?php
}