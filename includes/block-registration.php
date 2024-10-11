<?php
/**
 * Register the Gutenberg block.
 */
function random_products_block_register_block() {
    $plugin_url = plugin_dir_url( dirname(__FILE__) );

    wp_register_script(
        'random-products-block-editor-script',
        $plugin_url . 'block.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch' ),
        filemtime( plugin_dir_path( dirname(__FILE__) ) . 'block.js' )
    );

    wp_register_style(
        'random-products-block-style',
        $plugin_url . 'style.css',
        array(),
        filemtime( plugin_dir_path( dirname(__FILE__) ) . 'style.css' )
    );

    register_block_type( 'random-products/random-products-block', array(
        'editor_script' => 'random-products-block-editor-script',
        'style'         => 'random-products-block-style',
        'render_callback' => 'random_products_block_render_callback',
    ) );
}