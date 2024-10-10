# Random Products Block

A Gutenberg block to display 3 random WooCommerce products.

## Description

The Random Products Block plugin allows you to display 3 random WooCommerce products in a Gutenberg block. This plugin fetches products from your WooCommerce store using the WooCommerce REST API.

## Features

- Display 3 random WooCommerce products.
- Fetch products using the WooCommerce REST API.
- Easy configuration through the WordPress admin settings page.

## Installation

1. Download the plugin files and upload them to your WordPress site's `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Configuration

1. Go to the WordPress admin dashboard.
2. Navigate to `WooCommerce > Random Products Block`.
3. Enter your WooCommerce REST API Consumer Key and Consumer Secret.
4. Save the settings.

## Usage

1. Go to the Gutenberg editor.
2. Add the "Random Products Block" to a post or page.
3. Save the post or page.
4. View the post or page on the frontend to see the random products displayed.

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher

## Frequently Asked Questions

### Q: What happens if I revoke the WooCommerce REST API key?

A: If the WooCommerce REST API key is revoked, the plugin will not be able to fetch products from the WooCommerce API. The plugin will display an error message prompting you to check your WooCommerce API credentials.

### Q: How do I update the WooCommerce API credentials?

A: You can update the WooCommerce API credentials by going to `WooCommerce > Random Products Block` in the WordPress admin dashboard and entering the new credentials.

## Changelog

### 1.0
- Initial release.

## License

This plugin is licensed under the GPLv2 or later.