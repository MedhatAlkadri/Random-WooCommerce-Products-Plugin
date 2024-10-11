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

// Define constants for option names
define( 'RANDOM_PRODUCTS_BLOCK_CONSUMER_KEY_OPTION', 'random_products_block_consumer_key' );
define( 'RANDOM_PRODUCTS_BLOCK_CONSUMER_SECRET_OPTION', 'random_products_block_consumer_secret' );

// Include necessary files
require_once plugin_dir_path( __FILE__ ) . 'includes/block-registration.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/enqueue-assets.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/render-callback.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/utilities.php';

// Register hooks
add_action( 'init', 'random_products_block_register_block' );
add_action( 'enqueue_block_assets', 'random_products_block_enqueue_assets' );
add_action( 'admin_menu', 'random_products_block_add_admin_menu' );
add_action( 'admin_init', 'random_products_block_settings_init' );