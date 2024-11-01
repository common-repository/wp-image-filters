<?php
/**
 * Plugin Name: WP Image Filters
 * Description: Plugin for manipulating images
 * Author:      Kamer DINC
 * Version:     1.0.0
 * Author URI:  http://github.com/merkdev
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-image-filters
 * Domain Path: /languages
 */

defined('ABSPATH') or die('No script kiddies please!');
define('WIF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WIF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WIF_SLUG', 'wp-image-filters');
define('WIF_PHP_VERSION_REQUIRED', '5.6.3');
define('WIF_IMAGE_QUALITY', 100);

$wif_default_options = [
	'wif_status',
	'wif_horizontal_flip_image',
	'wif_watermark',
	'wif_watermark_apply_sizes', // array
	'wif_watermark_image'
];

$plugin_data = get_plugin_data(WIF_PLUGIN_DIR.'/wp-image-filters.php');
define('WIF_VERSION', $plugin_data['Version']);

add_action('plugins_loaded', 'load_textdomain_wif');
function load_textdomain_wif() {
	load_plugin_textdomain(WIF_SLUG, FALSE, basename(dirname(__FILE__)).'/languages/');
}

include WIF_PLUGIN_DIR.'functions.php';
// include WIF_PLUGIN_DIR.'admin.php';

// On Active plugin
// register_activation_hook( __FILE__ , 'wip_activate' );


// On De-Active plugin
// register_uninstall_hook( __FILE__ , 'wip_uninstall' );

?>