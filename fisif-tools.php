<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://swasis.com
 * @since             0.0.7
 * @package           FISIF_Tools
 *
 * @wordpress-plugin
 * Plugin Name:       FISIF Tools for WordPress
 * Plugin URI:        http://www.fisif.com/
 * Description:       This is a group of custom WordPress tools developed for FISIF by Southwest Technology Associates, Inc. / TeamWeb
 * Version:           0.0.7
 * Author:            SouthWest Technology Associates, Inc. / TeamWeb
 * Author URI:        http://swasis.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fisif-tools
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// increate memory limit and execution timeout for PDF document generation
ini_set('memory_limit', '512M');
//ini_set('max_execution_time', 300);

// force concatenation of scripts to avoid sending output to allow
// PDF generation to return appropriate headers
//global $concatenate_scripts;
//$concatenate_scripts = TRUE;
//global $wp_scripts;
//if ( ! ( $wp_scripts instanceof WP_Scripts ) ) {
    //$wp_scripts = new WP_Scripts();
//}
//$wp_scripts->do_concat = TRUE;

//remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
//remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
//remove_action( 'wp_print_styles', 'print_emoji_styles' );
//remove_action( 'admin_print_styles', 'print_emoji_styles' );
//remove_action( 'admin_print_styles', 'usp_load_admin_styles' );
//remove_action( 'wp_print_scripts', 'usp_js_vars');
//remove_action('wp_enqueue_scripts', 'usp_enqueueResources');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fisif-tools-activator.php
 */
function activate_fisif_tools() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fisif-tools-activator.php';
	FISIF_Tools_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fisif-tools-deactivator.php
 */
function deactivate_fisif_tools() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fisif-tools-deactivator.php';
	FISIF_Tools_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_fisif_tools' );
register_deactivation_hook( __FILE__, 'deactivate_fisif_tools' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
 // Change page orientation for some reports
//define('DOMPDF_PAPER_ORIENTATION', 'landscape');
require plugin_dir_path( __FILE__ ) . 'includes/class-fisif-tools.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.7
 */
function run_fisif_tools() {

	$plugin = new FISIF_Tools();
	$plugin->run();

}
run_fisif_tools();
?>
